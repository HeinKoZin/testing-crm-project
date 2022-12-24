<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
       return view('pages.role.index');
    }

    public function getRoleList(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*');
            return DataTables::of($data)
                    ->addColumn('action', function ($row) {
                        return '

                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="' . route("roles.edit", ["id" => $row->id]) . '" class="btn btn-primary btn-sm " >
                                        Edit
                                    </a>
                                </div>
                                <div >
                                    <form method="post" action="' . route("roles.delete", ["id" => $row->id]) . ' "
                                    id="from1" data-flag="0">
                                    ' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm delete" style="margin-left: 6px">Delete</button>
                                        </form>
                                </div>
                            </div>
                        </div>';
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
        return view('pages.role.create', compact('role','permission','rolePermissions'));
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
        return redirect()->route('roles.index')
                        ->with('success','Role has been deleted successfully');
    }
}
