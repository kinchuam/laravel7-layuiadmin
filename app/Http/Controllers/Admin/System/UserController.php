<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function index()
    {
        return view('admin.system.user.index');
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $res = User::query()->paginate($request->get('limit', 10))->toArray();
        return response()->json([
            'code' => 0,
            'message'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.system.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\UserCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        $data['uuid'] = Str::uuid();
        $data['password'] = bcrypt($data['password']);
        $data['name'] = !$data['name']?$data['username']:'';
        $data['email'] = $data['email']??'';
        $data['phone'] = $data['phone']??'';
        if ($model = User::create($data)){
            ActivityLog::addLog('添加账号 ID:'.$model['id'], $data, $model);
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'message' => '添加成功'
            ]);
        }
        return response()->json(['status' => 'fail', 'message' => '系统错误']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        return view('admin.system.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UserUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        $item = User::findOrFail($id);
        $data = $request->except('password');
        if ($request->get('password')){
            $data['password'] = bcrypt($request->get('password'));
        }
        $data['email'] = $data['email']??'';
        $data['phone'] = $data['phone']??'';
        if ($item->update($data)){
            ActivityLog::addLog('更新账号 ID:'.$item['id'], $data, $item);
            return response()->json(['status' => 'success','noRefresh' => false, 'message' => '更新用户成功']);
        }
        return response()->json(['status' => 'fail', 'message' => '系统错误']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        $ids = is_array($ids)?$ids:[$ids];
        if(in_array(1, $ids)){
            return response()->json(['code'=>1,'msg'=>'默认管理员不能删除']);
        }
        $list = User::query()->whereIn('id', $ids)->get();
        if ($list->isEmpty()){
            return response()->json(['code'=>1, 'message'=>'记录不存在']);
        }
        try {
            foreach ($list as $model) {
                ActivityLog::addLog('删除账号: '.$model['name'], $model->toArray(), $model);
                $model->delete();
            }
            return response()->json(['code'=>0, 'message'=>'删除成功']);
        }catch (\Exception $e) {
            return response()->json(['code'=>1, 'message'=>'系统错误']);
        }
    }

    /**
     * 分配角色
     */
    public function role($id)
    {
        $user = User::query()->findOrFail($id);
        $roles = Role::query()->get(['id','name','display_name']);
        //$hasRoles = $user->roles();
        foreach ($roles as $role){
            $role->own = (bool)$user->hasRole($role);
        }
        return view('admin.system.user.role', compact('roles','user'));
    }

    /**
     * 更新分配角色
     */
    public function assignRole(Request $request, $id): \Illuminate\Http\JsonResponse
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
    public function permission($id)
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
        return view('admin.system.user.permission', compact('user','permissions'));
    }

    /**
     * 存储权限
     */
    public function assignPermission(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = User::findOrFail($id);
        $permissions = $request->get('permissions');

        if (empty($permissions)){
            $user->permissions()->detach();
            ActivityLog::addLog('账号：'.$user['username'].' 更新权限 ID:'.$id, $permissions, $user);
            return response()->json([
                'status' => 'success',
                'noRefresh' => true,
                'message' => '已更新用户直接权限'
            ]);
        }
        $user->syncPermissions($permissions);
        ActivityLog::addLog('账号：'.$user['username'].' 更新权限 ID:'.$id, $permissions, $user);
        return response()->json([
            'status' => 'success',
            'noRefresh' => true,
            'message' => '已更新用户直接权限'
        ]);
    }

}
