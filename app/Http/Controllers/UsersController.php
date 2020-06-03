<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; //对user模型声明
use Auth;
use Mail;
class UsersController extends Controller
{
    /* 设置权限，用户只能编辑自己的资料
     * 过滤未登录的用户，middleware方法接受两个参数，第一个为中间件的名称，第二个为要进行过滤的动作
     * except 方法来设定指定动作
     *
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store','index','confirmEmail']
        ]);
        //只让未登录用户访问注册页面，guest属性设置
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    //用户注册页面的请求指定给用户控制器的 create 方法进行处理
    //创建用户的页面
    public function create(){
        return view('users.create');
    }

    //显示用户的信息，并关联发布微博的条数及信息
    public function show(user $user){
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('users.show', compact('user', 'statuses'));
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
        //Auth::login($user); //注册完后直接登陆
        //注册完后发送到邮箱验证, sendEmailConfirmationTo 方法，该方法将用于发送邮件给指定用户
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    //sendEmailConfirmationTo 方法，该方法将用于发送邮件给指定用户
    protected function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        //$from = 'summer@example.com';
        //$name = 'Summer';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";
        Mail::send($view,$data,function ($message) use ($to,$subject){
            $message->to($to)->subject($subject);
        });
    }

    //激活邮件功能
    public function confirmEmail($token){
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();
        Auth::login($user);
        session()->flash('success','恭喜你，激活成功！');
        return redirect()->route('users.show',[$user]);
    }

    //编辑表单，更新个人资料
    public function edit(user $user){
        //使用 authorize 方法来验证用户授权策略,authorize 方法接收两个参数，第一个为授权策略的名称，第二个为进行授权验证的数据
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }

    //处理更新个人资料的 update 方法
    public function update(user $user,Request $request){
        //使用 authorize 方法来验证用户授权策略
        $this->authorize('update', $user);
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

    //显示所有用户
    public function index(){
        //分页
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    //删除用户
    public function destroy(User $user)
    {
        //授权
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    //显示用户的关注人列表
    public function followings(user $user){
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }


}
