<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{

    public function registerPage()
    {
        if(session('success')){
            toast(Session::get('success'), "success");
        }
        if(session('error')){
            toast(Session::get('error'), "error");
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
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
        // $role = Role::find(2);
        // $user->assignRole($role->id);
        return redirect()->route('success.member')->with('success', "Registration Successfully!");
    }

    public function successMember()
    {
       return view('pages.member.successful');
    }

    public function loginPage()
    {
        if(session('success')){
            toast(Session::get('success'), "success");
        }
        if(session('error')){
            toast(Session::get('error'), "error");
        }
       return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/')
                        ->with('success','You have been login successfully! ');
        }

        return redirect()->back()->with('error','Oppes! You have entered invalid credentials');
    }

    public function logout() {
        Session::flush();
        Auth::logout();
        return redirect()->route('auth.login.index')->with('success', 'You have been logout successfully!');
    }
}
