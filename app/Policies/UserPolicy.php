<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     * 添加 update 方法，用于用户更新时的权限验证。
     * 不需要检查 $currentUser 是不是 NULL。未登录用户，框架会自动为其 所有权限 返回 false
     * @return void
     */
    public function update(User $currentUser, User $user)
    {
        //
        return $currentUser->id === $user->id;
    }
    /*
     * 删除用户动作相关的授权
     */
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }

    //关注用户，自己不能关注自己的授权
    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id;
    }
}
