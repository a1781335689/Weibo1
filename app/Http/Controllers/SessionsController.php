<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

class SessionsController extends Controller
{
	// Auth 中间件提供的 guest 选项，用于指定一些只允许未登录用户访问的动作
	
	public function __construct()
    {
    	// 只让未登录用户访问登录页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }


	// 登录界面
    public function create()
    {
        return view('sessions.create');
    }


    // 登录界面登录功能表单提交
    // validate 方法来进行数据验证。validate 方法接收两个参数，第一个参数为用户的输入数据，第二个参数为该输入数据的验证规则
    //attempt 方法会接收一个数组来作为第一个参数，该参数提供的值将用于寻找数据库中的用户数据。方法可接收两个参数，第一个参数为需要进行用户身份认证的数组，第二个参数为是否为用户开启『记住我』功能的布尔值
    // redirect() 实例提供了一个 intended 方法，该方法可将页面重定向到上一次请求尝试访问的页面上，并接收一个默认跳转地址参数，当上一次请求记录为空时，跳转到默认地址上。
    public function store(Request $request)
    {
       $credentials = $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required'
       ]);

       if (Auth::attempt($credentials, $request->has('remember'))) {
        // dump(Auth::user());dump(Auth::user()->activated);dd(Auth::user()->created_at);
           if(Auth::user()->activated) {
               session()->flash('success', '欢迎回来！');
               $fallback = route('users.show', Auth::user());
               return redirect()->intended($fallback);
           } else {
               Auth::logout();
               session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
               return redirect('/');
           }
       } else {
           session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
           return redirect()->back()->withInput();
       }
    }


    // 退出登录
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}