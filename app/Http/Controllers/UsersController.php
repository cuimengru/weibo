<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; //对user模型声明
class UsersController extends Controller
{
    //用户注册页面的请求指定给用户控制器的 create 方法进行处理
    //创建用户的页面
    public function create(){
        return view('users.create');
    }

    //显示用户的信息
    public function show(user $user){
        return view('users.show',compact('user'));
    }

    //创建用户，表单中store的提交方法
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        return;
    }

}
