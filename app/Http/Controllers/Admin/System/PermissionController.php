<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        return view('admin.system.permission.index');
    }

    public function data(): \Illuminate\Http\JsonResponse
    {
        $list = Permission::query()->get(['id','name','display_name','route','icon','parent_id','created_at','updated_at'])->toArray();
        return response()->json([
            'code' => 0,
            'message'   => '正在请求中...',
            'data'  => $list
        ]);
    }

    public function get_lists(Request $request): \Illuminate\Http\JsonResponse
    {
        $parent_id = $request->get('parent_id',0);
        $model = Permission::query();
        $keywords = $request->get('keywords','');
        if (!empty($keywords)){
            $keyword = $this->escape_like($keywords);
            $model = $model->whereRaw("( LOCATE('".$keyword."', `display_name`) > 0 )");
        }
        $list = $model->get(['id', 'display_name', 'parent_id'])->toArray();
        if (!empty($list)) {
            foreach ($list as $ke => $row) {
                $list[$ke]['selected'] = ($parent_id > 0 && $parent_id == $row['id']);
            }
        }
        $expandedKeys = $this->GetPerId($parent_id, $list);
        $list = $this->tree($list,'id','parent_id','children');
        return response()->json([
            'data' => $list,
            'expandedKeys' => $expandedKeys
        ]);
    }

    protected function GetPerId($id, $arr): array
    {
        $pid = [];
        if (empty($id) || $id == 0) { return $pid; }
        $arr = collect($arr)->pluck('parent_id','id');
        if (!isset($arr[$id])) { return $pid; }
        while ($arr[$id]) {
            $id = $arr[$id];
            $pid[] = intval($id);
        }
        return $pid;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.system.permission.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param PermissionCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PermissionCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        if ($permission = Permission::create($data)){
            ActivityLog::addLog('添加权限: '.$permission['name'], $data, $permission);
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'fromData' => [
                    'parent_id' => $data['parent_id'],
                    'model' => 'permission'
                ],
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
        $permission = Permission::findOrFail($id);
        return view('admin.system.permission.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\PermissionUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PermissionUpdateRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $data = $request->all();
        if ($permission->update($data)){
            ActivityLog::addLog('更新权限: '.$permission['name'], $data, $permission);
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'fromData' => [
                    'parent_id'=>$permission['parent_id'],
                    'model'=>'permission'],
                'message' => '更新权限成功'
            ]);
        }
        return response()->json(['status' => 'fail', 'message' => '系统错误']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1, 'message'=>'请选择删除项']);
        }
        $ids = is_array($ids)?$ids:[$ids];
        $list = Permission::query()->whereIn('id', $ids)->with(['childs:id,parent_id'])->get();
        if ($list->isEmpty()){
            return response()->json(['code'=>-1, 'message'=>'权限不存在']);
        }
        try {
            foreach ($list as $model) {
                if (!$model->childs->isEmpty()){
                    return response()->json(['code'=>2, 'message'=>'存在子权限禁止删除']);
                }
                ActivityLog::addLog('删除权限: '.$model['name'], $model->toArray(), $model);
                $model->delete();
            }
            return response()->json(['code'=>0, 'message'=>'删除成功']);
        }catch (\Exception $e) {
            return response()->json(['code'=>1, 'message'=>'删除失败']);
        }
    }
}
