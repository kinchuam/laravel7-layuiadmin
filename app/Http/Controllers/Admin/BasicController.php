<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BasicController extends Controller
{

    public function index()
    {
        $item = auth('admin')->user();
        return view('admin.system.set.info', compact('item'));
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request,[
            'username'  => 'required|min:4|max:14',
            'name'  => 'required',
        ]);
        $data = $request->only(['email','phone','username','name']);
        $user = auth('admin')->user();
        if (!empty($data['email']) && $data['email'] != $user->email){
            $user->email = trim($data['email']);
        }
        if (!empty($data['phone']) && $data['phone'] && $user->phone){
            $user->phone = trim($data['phone']);
        }
        if (!empty($data['username']) && $data['username'] != $user->username){
            $user->username = trim($data['username']);
        }
        if (!empty($data['name']) && $data['name'] != $user->name){
            $user->name = trim($data['name']);
        }
        if ($user->save()){
            ActivityLog::addLog($user->name.' 修改信息', $user, $user);
            return response()->json(['code'=>0, 'message'=>'更新成功']);
        }
        return response()->json(['code'=>1, 'message'=>'更新失败']);
    }

    public function password()
    {
        return view('admin.system.set.password');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request,[
            'old_password'  => 'required|string',
            'password'  => 'required|string|min:6|confirmed'
        ],[
            'old_password.required' => '原始密码不能为空',
            'password.required' => '新密码不能为空',
            'password.min' => '新密码不能少于6个字符',
            'password.confirmed' => '两次输入新密码不符',
        ]);
        $password = $request->get('password','');
        $old_password = $request->get('old_password','');
        if ($password == $old_password){
            return response()->json(['code'=>1, 'message'=>'不能使用原始密码']);
        }
        $user = auth('admin')->user();
        if (Hash::check($old_password, $user->password)){
            $user->password = bcrypt($password);
            $user->save();
            ActivityLog::addLog($user->name.' 修改密码', [], $user);
            return response()->json(['code'=>0, 'message'=>'更新成功']);
        }
        return response()->json(['code'=>1, 'message'=>'密码不正确']);
    }
}
