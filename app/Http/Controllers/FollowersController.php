<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class FollowersController extends Controller
{
    //过滤
    public function __construct()
    {
        $this->middleware('auth');
    }

    //关注
    public function store(User $user){
        $this->authorize('follow', $user);
        //判断用户是否关注
        if(! Auth::user()->isFollowing($user->id)){
            //没有关注的话，进行关注操作
            Auth::user()->follow($user->id);
        }
        return redirect()->route('users.show', $user->id);
    }

    //取消关注
    public function destroy(User $user)
    {
        $this->authorize('follow', $user);
        //判断用户是否关注
        if (Auth::user()->isFollowing($user->id)) {

            //关注的话，进行取消关注操作
            Auth::user()->unfollow($user->id);
        }

        return redirect()->route('users.show', $user->id);
    }
}
