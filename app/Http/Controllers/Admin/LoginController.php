<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    use AuthenticatesUsers;

    protected $redirectTo = '/admin/index';

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        return view('Admin.auth.login');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/admin/login');
    }
}
