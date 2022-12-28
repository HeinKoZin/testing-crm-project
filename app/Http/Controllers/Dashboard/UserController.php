<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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
       return view('pages.user.index');
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
                    alt="Profile Image" style="width: 60px; height: 60px; border-radius: 4px;">';
                    })
                    ->addColumn('action', function ($row) use ($user) {
                        if ($user->can('role-delete') && $user->can('role-edit')) {
                            return '
                            <div class="d-flex align-items-center">
                                    <div>
                                        <a href="' . route("users.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm " >
                                        <i class="bi bi-pencil-square"></i>  Edit
                                        </a>
                                    </div>
                                    <div >
                                        <form method="post" action="' . route("users.delete", ["id" => $row->id]) . ' "
                                        id="from1" data-flag="0">
                                        ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-outline-danger btn-sm delete" style="margin-left: 6px;"> <i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                    </div>
                                </div>
                            </div>';
                        }
                        if ($user->can('role-edit')) {
                            return '
                            <div class="d-flex align-items-center">
                                    <div>
                                        <a href="' . route("users.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm " >
                                        <i class="bi bi-pencil-square"></i>  Edit
                                        </a>
                                    </div>
                                </div>
                            </div>';
                        }
                        if ($user->can('role-delete')) {
                            return '
                            <div class="d-flex align-items-center">
                                    <div >
                                        <form method="post" action="' . route("users.delete", ["id" => $row->id]) . ' "
                                        id="from1" data-flag="0">
                                        ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-outline-danger btn-sm delete" style="margin-left: 6px;"> <i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                    </div>
                                </div>
                            </div>';
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
        dd($request->profile);
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
}
