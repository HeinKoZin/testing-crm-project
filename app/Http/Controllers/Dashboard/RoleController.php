<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','save']]);
         $this->middleware('permission:role-create', ['only' => ['create','save']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['delete']]);
    }

    public function index()
    {
        if (session('success')) {
            toast(Session::get('success'), "success");
        }
       return view('pages.role.index');
    }

    public function getRoleList(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*');
            $user = auth()->user();
            return DataTables::of($data)
                    ->addColumn('action', function ($row) use ($user) {
                        if ($user->can('role-delete') && $user->can('role-edit')) {
                            return '
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="' . route("roles.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm " >
                                    <i class="bi bi-pencil-square"></i>  Edit
                                    </a>
                                </div>
                                <div>
                                    <form method="post" action="' . route("roles.delete", ["id" => $row->id]) . ' "
                                    id="from1" data-flag="0">
                                    ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-outline-danger btn-sm delete" style="margin-left: 6px;"> <i class="bi bi-trash"></i> Delete</button>
                                        </form>
                                </div>
                            </div>';
                        }
                        if ($user->can('role-edit')) {
                            return '
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="' . route("roles.edit", ["id" => $row->id]) . '" class="btn btn-success btn-sm " >
                                    <i class="bi bi-pencil-square"></i>  Edit
                                    </a>
                                </div>
                            </div>';
                        }
                        if ($user->can('role-delete')) {
                            return '
                            <div class="d-flex align-items-center">
                                    <div >
                                        <form method="post" action="' . route("roles.delete", ["id" => $row->id]) . ' "
                                        id="from1" data-flag="0">
                                        ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-outline-danger btn-sm delete" style="margin-left: 6px;"> <i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                    </div>
                                </div>
                            </div>';
                        }


                    })
                    ->rawColumns(['created_at', 'action', 'profile_img'  ])
                    ->make(true);
        }
    }

    public function create()
    {
        $permission = Permission::get();
        return view('pages.role.create', compact('permission'));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles')
                        ->with('success','Role has been created successfully');
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('pages.role.edit', compact('role','permission','rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles')->with('success', 'Role has been updated successfully!');
    }

    public function delete($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles')
                        ->with('success','Role has been deleted successfully');
    }
}
