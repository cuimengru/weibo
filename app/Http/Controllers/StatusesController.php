<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class StatusesController extends Controller
{
    //只对登陆的用户开放，过滤
    public function __construct(){
        $this->middleware('auth');
    }

    //创建微博
    public function store(Request $request){
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        // Auth::user()获取当前用户实例
        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);
        session()->flash('success', '发布成功！');
        return redirect()->back(); //back方法 是在用户完成微博的创建之后，需要将其导向至上一次发出请求的页面，即网站主页
    }
}
