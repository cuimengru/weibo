<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    //用户注册页面的请求指定给用户控制器的 create 方法进行处理
    public function create(){
        return view('users.create');
    }
}
