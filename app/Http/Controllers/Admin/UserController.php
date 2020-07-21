<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Jenssegers\Agent\Agent;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserCreateRequest $request)
    {
        $data = $request->all();
        $data['uuid'] = \Faker\Provider\Uuid::uuid();
        $data['password'] = bcrypt($data['password']);
        $data['name'] = !$data['name']?$data['username']:'';
        $data['email'] = $data['email']??'';
        $data['phone'] = $data['phone']??'';
        if (User::create($data)){
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'message' => '添加用户成功'
            ]);
        }
        return response()->json(['status' => 'fail', 'message' => '系统错误']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->except('password');
        if ($request->get('password')){
            $data['password'] = bcrypt($request->get('password'));
        }
        $data['email'] = $data['email']??'';
        $data['phone'] = $data['phone']??'';
        if ($user->update($data)){
            return response()->json(['status' => 'success','noRefresh' => false, 'message' => '更新用户成功']);
        }
        return response()->json(['status' => 'fail', 'message' => '系统错误']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if(in_array(1,$ids)){
            return response()->json(['code'=>1,'msg'=>'默认管理员不能删除']);
        }
        if (User::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    /**
     * 分配角色
     */
    public function role(Request $request,$id)
    {
        $user = User::query()->findOrFail($id);
        $roles = Role::query()->get(['id','name','display_name']);
//        $hasRoles = $user->roles();
        foreach ($roles as $role){
            $role->own = $user->hasRole($role) ? true : false;
        }
        return view('admin.user.role',compact('roles','user'));
    }

    /**
     * 更新分配角色
     */
    public function assignRole(Request $request,$id)
    {
        $user = User::query()->findOrFail($id);
        $roles = $request->get('roles',[]);
        if ($user->syncRoles($roles)){
            return response()->json([
                'status' => 'success',
                'noRefresh' => true,
                'message' => '更新用户角色成功'
            ]);
        }
        return response()->json(['status' => 'fail', 'message' => '系统错误']);
    }

    /**
     * 分配权限
     */
    public function permission(Request $request,$id)
    {
        $user = User::query()->findOrFail($id);
        $permissions = $this->tree(\App\Models\Permission::query()->get(['id','name','display_name','route','icon','parent_id','sort'])->toArray());
        foreach ($permissions as $key1 => $item1){
            $permissions[$key1]['own'] = $user->hasDirectPermission($item1['id']) ? 'checked' : false ;
            if (isset($item1['_child'])){
                foreach ($item1['_child'] as $key2 => $item2){
                    $permissions[$key1]['_child'][$key2]['own'] = $user->hasDirectPermission($item2['id']) ? 'checked' : false ;
                    if (isset($item2['_child'])){
                        foreach ($item2['_child'] as $key3 => $item3){
                            $permissions[$key1]['_child'][$key2]['_child'][$key3]['own'] = $user->hasDirectPermission($item3['id']) ? 'checked' : false ;
                        }
                    }
                }
            }
        }
        return view('admin.user.permission',compact('user','permissions'));
    }

    /**
     * 存储权限
     */
    public function assignPermission(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $permissions = $request->get('permissions');

        if (empty($permissions)){
            $user->permissions()->detach();
            return response()->json([
                'status' => 'success',
                'noRefresh' => true,
                'message' => '已更新用户直接权限'
            ]);
        }
        $user->syncPermissions($permissions);
        return response()->json([
            'status' => 'success',
            'noRefresh' => true,
            'message' => '已更新用户直接权限'
        ]);
    }


    public function LoginLog()
    {
        $users = User::query()->get(['id','name']);
        return view('admin.user.loginlog',compact('users'));
    }

    public function LoginLogDate(Request $request)
    {
        $query = User\LoginLog::query()->select(['id','uuid','ip','agent','message','ipData','created_at']);

        if (!empty($request->get('ip',''))){
            $query = $query->where('ip','like',$request->get('ip').'%');
        }
        if (!empty($request->get('uuid',''))){
            $query = $query->where('uuid', trim($request->get('uuid','')));
        }
        $res = $query->with('user')->orderBy('id','desc')->paginate($request->get('limit', 20))->toArray();
        $agent = new Agent();
        foreach ($res['data'] as &$row) {
            $agent->setUserAgent($row['agent']);
            $browser = $agent->browser();
            $system = $agent->platform();
            $row['system_browser'] = $system.' '.$agent->version($system).' | '.$browser.' '.$agent->version($browser);
            $ipData = json_decode($row['ipData'],true);
            $row['ip'] = ($ipData?$ipData['state_name'].$ipData['city']:'').' 【'.$row['ip'].'】';
        }
        unset($row);
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }
}
