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
        return $this->hasMany(Status::class);
    }

    /* feed 方法将当前用户发布过的所有微博从数据库中取出，并根据创建时间来倒序排序*/
    public function feed()
    {
        return $this->statuses()
            ->orderBy('created_at', 'desc');
    }
}
