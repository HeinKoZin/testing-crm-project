<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Member;
use App\Mail\MemberMail;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class MemberController extends Controller
{
    public function index()
    {
        if(session('success')){
            toast(Session::get('success'), "success");
        }
        $members = Member::get();
       return view('pages.member.index', compact('members'));
    }


    public function getRoleList(Request $request)
    {
        if ($request->ajax()) {
            $data = Member::select('*');
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
                                            <a href="' . route("members.show", ["id" => $row->id]) . '" class="btn btn-warning btn-sm mb-2" style="width: 100%;" >
                                                <i class="bi bi-eye"></i>  Detail
                                            </a>
                                        </li>
                                        <li>
                                            <a href="' . route("members.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm mb-2" style="width: 100%;" >
                                                <i class="bi bi-pencil-square"></i>  Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form method="post" action="' . route("members.delete", ["id" => $row->id]) . ' "
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
                                        <a href="' . route("members.show", ["id" => $row->id]) . '" class="btn btn-warning btn-sm mb-2" style="width: 100%;" >
                                            <i class="bi bi-eye"></i>  Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a href="' . route("members.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm " style="width: 100%" >
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
                                        <a href="' . route("members.show", ["id" => $row->id]) . '" class="btn btn-warning btn-sm mb-2" style="width: 100%;" >
                                            <i class="bi bi-eye"></i>  Detail
                                        </a>
                                    </li>
                                    <li>
                                        <form method="post" action="' . route("members.delete", ["id" => $row->id]) . ' "
                                        id="from1" data-flag="0">
                                        ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-outline-danger btn-sm delete" style="width: 100%"> <i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                    </li>
                                </ul>
                            </div>';
                        }


                    })
                    // ->filter(function ($instance) use ($request) {
                    //     if($request->has('from_date')){
                    //         $from_date = Carbon::parse($request->get('from_date'))->format('Y-m-d');
                    //         $to_date = Carbon::parse($request->get('to_date'))->format('Y-m-d');
                    //         $start_date = $from_date != null ? "$from_date 00:00:00" : null;
                    //         $end_date = $to_date != null ? "$to_date 23:59:59" : null;
                    //         $instance = $instance->whereBetween('users.created_at', [$start_date, $end_date]);

                    //     }
                    //     if ($request->get('gender')){
                    //         $instance->where('users.gender', $request->get('gender'));
                    //     }
                    //     if ($request->get('role')){
                    //         $instance->select('users.*', 'roles.id as role_id')->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    //         ->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
                    //         $instance->where('role_id', $request->get('role'));
                    //     }


                    // })
                    ->rawColumns(['created_at', 'action', 'profile'  ])
                    ->make(true);
        }
    }

    public function create()
    {
        return view('pages.member.create');
    }

    public function save(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required',
            'email' => 'bail|required|email',
            'password' => 'bail|required|confirmed|min:6',
        ]);

        $password = Hash::make($request->password);
        $path = '';
        if ($request->file()) {
            $fileName = time() . '_' . $request->profile->getClientOriginalName();
            $filePath = $request->file('profile')->storeAs('userProfile', $fileName, 'public');
            $path = '/storage/' . $filePath;
        }

        $member = Member::create([
            'name' => $request->name,
            'email' => $request->email,
            'profile' => $path,
            'password' => $password,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'nrc' => $request->nrc,
            'father_name' => $request->father_name,
            'status' => false,
        ]);
        return redirect()->route('members')->with('success', "User has been created successfully!");
    }

    public function edit($id)
    {
        $member = Member::find($id);
        // dd($userRole);
        return view('pages.members.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->profile);
        $member = Member::find($id);


        $password = $member->password;
        if ($request->password) {
            $password = Hash::make($request->password);
        }
        $path = '';
        $pathEmp = $request->file('profile');
        $path= Member::where('id', $id)->value('profile');
        if($pathEmp){
            if ($request->file()) {
                $fileName = time() . '_' . $request->profile->getClientOriginalName();
                $filePath = $request->file('profile')->storeAs('userProfile', $fileName, 'public');
                $path = '/storage/' . $filePath;
            }
        }
        $member = Member::find($id);
        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile' => $path,
            'password' => $password,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return redirect()->route('members')->with('success', "Member has been updated successfully!");
    }

    public function show($id)
    {
        $member = Member::find($id);
        return view('pages.user.show', compact('member'));
    }

    public function delete($id)
    {
        Member::find($id)->delete();
        return redirect()->route('members')->with('success', 'Member has been deleted successfully!');
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
        $member = Member::where('id', $id)->first();
        $title = $request->title;
        $content = $request->content;

        Mail::to($member->email)->send(new MemberMail($member,$title, $content));

        return redirect()->back()->with('success', 'Email has been send successfully!');
    }
}
