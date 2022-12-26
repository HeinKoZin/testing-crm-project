<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
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
}
