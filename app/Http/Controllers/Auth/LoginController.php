<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 显示登录页面
    public function showLoginPage()
    {
        return view('auth.login');
    }

    // 处理登录请求
//    public function login(Request $request)
//    {
//        // 验证表单输入
//        $credentials = $request->only('account', 'password');
//
//        // 尝试登录用户
//        if (Auth::attempt($credentials, $request->remember)) {
//            // 登录成功，重定向到首页
//            return redirect()->intended('dashboard');
//        }
//        // 登录失败，返回错误信息
//        return back()->withErrors([
//            'account' => '账号或密码错误，请重试。',
//        ])->withInput($request->except('password'));
//    }
    public function login(Request $request)
    {
        $credentials = $request->only('account', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return response()->json(['message' => '登录成功'], 200);
        }

        return response()->json(['error' => '账号或密码错误'], 401);
    }


    // 处理登出请求
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}
