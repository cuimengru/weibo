<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    //生成令牌，boot 方法会在用户模型类完成初始化之后进行加载
    public static function boot()
    {
        parent::boot();

        //creating 用于监听模型被创建之前的事件
        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*生成头像的 gravatar 方法*/
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    /*指明一个用户拥有多条微博
    *使用了微博动态的复数形式 statuses 来作为定义的函数名
    */
    public function statuses()
    {
        //关联微博表
        return $this->hasMany(Status::class);//Status指的是模型Status.php
    }

    /* feed 方法将当前用户发布过的所有微博从数据库中取出，并根据创建时间来倒序排序*/
    public function feed()
    {
        return $this->statuses()
            ->orderBy('created_at', 'desc');
    }

    //根据用户获取粉丝关系列表
    public function followers()
    {
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    //获取用户关注人列表
    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }

    //关注
    public function follow($user_ids){
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        //sync 方法不重复关注用户
        $this->followings()->sync($user_ids, false);
    }

    //取消关注
    public function unfollow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }

        //detach 取消关注
        $this->followings()->detach($user_ids);
    }

    //判断当前登录的 A用户是否关注了 B 用户， contains 方法来做判断
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
