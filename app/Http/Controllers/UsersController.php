<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; //对user模型声明
use Auth;
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
        //验证规则 validate
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        //create创建
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        Auth::login($user); //注册完后直接登陆
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    //编辑表单，更新个人资料
    public function edit(user $user){
        return view('users.edit',compact('user'));
    }

    //处理更新个人资料的 update 方法
    public function update(user $user,Request $request){
        //验证规则 validate
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'required|confirmed|min:6'
        ]);
        //update 更新
        $data=[];
        $data['name']=$request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show',$user);
    }

}
