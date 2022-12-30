<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Mail\MemberMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        if(session('success')){
            toast(Session::get('success'), "success");
        }
        $roles = Role::get();
        $users = User::get();
       return view('pages.user.index', compact('roles', 'users'));
    }

    public function getRoleList(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('*');
            $user = auth()->user();
            return DataTables::of($data)
                    ->addColumn('profile', function ($row) {
                        $url = asset($row->profile ? $row->profile : "assets/img/pp.jpg");
                        return '<img src="' . $url . '"
                    alt="Profile Image" style="width: 40px; height: 40px; border-radius: 4px;">';
                    })
                    ->addColumn('action', function ($row) use ($user) {
                        if ($user->can('role-delete') && $user->can('role-edit')) {
                            return '
                                <div class="dropdown">
                                    <button class="btn" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu p-4">
                                        <li>
                                            <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal'.$row->id.'" style="width: 100%;">
                                                Send Mail
                                            </button>
                                        </li>
                                        <li>
                                            <a href="' . route("users.show", ["id" => $row->id]) . '" class="btn btn-warning btn-sm mb-2" style="width: 100%;" >
                                                <i class="bi bi-eye"></i>  Detail
                                            </a>
                                        </li>
                                        <li>
                                            <a href="' . route("users.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm mb-2" style="width: 100%;" >
                                                <i class="bi bi-pencil-square"></i>  Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form method="post" action="' . route("users.delete", ["id" => $row->id]) . ' "
                                            id="from1" data-flag="0">
                                            ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm delete" style="width: 100%"> <i class="bi bi-trash"></i> Delete</button>
                                                </form>
                                        </li>
                                    </ul>
                                </div>';
                        }
                        if ($user->can('role-edit')) {
                            return '
                            <div class="dropdown">
                                <button class="btn" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu p-4">
                                    <li>
                                        <a href="' . route("users.show", ["id" => $row->id]) . '" class="btn btn-warning btn-sm mb-2" style="width: 100%;" >
                                            <i class="bi bi-eye"></i>  Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . route("users.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm " style="width: 100%" >
                                            <i class="bi bi-pencil-square"></i>  Edit
                                        </a>
                                    </li>
                                </ul>
                            </div>';
                        }
                        if ($user->can('role-delete')) {
                            return '
                            <div class="dropdown">
                                <button class="btn" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu p-4">
                                    <li>
                                        <a href="' . route("users.show", ["id" => $row->id]) . '" class="btn btn-warning btn-sm mb-2" style="width: 100%;" >
                                            <i class="bi bi-eye"></i>  Detail
                                        </a>
                                    </li>
                                    <li>
                                        <form method="post" action="' . route("users.delete", ["id" => $row->id]) . ' "
                                        id="from1" data-flag="0">
                                        ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-outline-danger btn-sm delete" style="width: 100%"> <i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                    </li>
                                </ul>
                            </div>';
                        }


                    })
                    ->filter(function ($instance) use ($request) {
                        if ($request->get('role')){
                            $instance->select('users.*', 'roles.id as role_id')->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
                            $instance->where('role_id', $request->get('role'));
                        }
                        if ($request->get('gender')){
                            $instance->where('gender', $request->get('gender'));
                        }
                        if($request->has('from_date')){
                            $from_date = Carbon::parse($request->get('from_date'))->format('Y-m-d');
                            $to_date = Carbon::parse($request->get('to_date'))->format('Y-m-d');
                            $start_date = $from_date != null ? "$from_date 00:00:00" : null;
                            $end_date = $to_date != null ? "$to_date 23:59:59" : null;
                            $instance = $instance->whereBetween('created_at', [$start_date, $end_date]);

                        }
                    })
                    ->rawColumns(['created_at', 'action', 'profile'  ])
                    ->make(true);
        }
    }

    public function create()
    {
        $roles = Role::get();
        return view('pages.user.create', compact('roles'));
    }

    public function save(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|confirmed|min:6',
            'phone_no' => 'unique:users',
            'role_id' => 'required'
        ]);

        $password = Hash::make($request->password);
        $path = '';
        if ($request->file()) {
            $fileName = time() . '_' . $request->profile->getClientOriginalName();
            $filePath = $request->file('profile')->storeAs('userProfile', $fileName, 'public');
            $path = '/storage/' . $filePath;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'profile' => $path,
            'password' => $password,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        $user->assignRole($request->role_id);
        return redirect()->route('users')->with('success', "User has been created successfully!");
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::get();
        $userRole = $user->roles->first();
        // dd($userRole);
        return view('pages.user.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->profile);
        $user = User::find($id);
        $this->validate($request, [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user)],
            'phone_no' => [Rule::unique('users', 'phone')->ignore($user)],
            'role_id' => 'required'
        ]);

        $password = $user->password;
        if ($request->password) {
            $password = Hash::make($request->password);
        }
        $path = '';
        $pathEmp = $request->file('profile');
        $path= User::where('id', $id)->value('profile');
        if($pathEmp){
            if ($request->file()) {
                $fileName = time() . '_' . $request->profile->getClientOriginalName();
                $filePath = $request->file('profile')->storeAs('userProfile', $fileName, 'public');
                $path = '/storage/' . $filePath;
            }
        }
        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile' => $path,
            'password' => $password,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->role_id);
        return redirect()->route('users')->with('success', "User has been updated successfully!");
    }

    public function show($id)
    {
        $user = User::find($id);
        $roles = Role::get();
        $userRole = $user->roles->first();
        return view('pages.user.show', compact('user', 'userRole'));
    }

    public function delete($id)
    {
        User::find($id)->delete();
        return redirect()->route('users')->with('success', 'User has been deleted successfully!');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function import()
    {
        Excel::import(new UsersImport,request()->file('file'));

        return back();
    }

    public function sendEmail(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        $title = $request->title;
        $content = $request->content;

        Mail::to($user->email)->send(new MemberMail($user,$title, $content));

        return redirect()->back()->with('success', 'Email has been send successfully!');
    }
}
