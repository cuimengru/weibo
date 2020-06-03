<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];
    public function user()
    {
        //关联用户表
        return $this->belongsTo(User::class);//User指的模型user
    }
}
