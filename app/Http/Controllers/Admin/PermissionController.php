<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Support\Arr;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view('admin.permission.index');
    }

    public function get_lists(Request $request)
    {
        $parent_id = $request->get('parent_id',0);
        $id = $request->get('id',0);
        $type = $request->get('type','');
        $model = Permission::query();
        if ($id > 0) {
            $model = $model->where('id','<>',$id);
        }
        $list = $model->get(['id','display_name','parent_id'])->toArray();
        foreach ($list as &$row)
        {
            $row['selected'] = false;
            $row['name'] = $row['display_name'];
            if ($parent_id > 0 && $parent_id == $row['id']) {
                $row['selected'] = true;
            }
            $row['value'] = $row['id'];
        }
        unset($row);
        $list = $this->tree($list,'id','parent_id','children');
        if ($type == 'permission') {
            $arr = ['value'=>0,'selected'=>false,'name'=>'顶级权限','parent_id'=>0];
            if (empty($parent_id)) {
                $arr['selected'] = true;
            }
            $list = Arr::prepend($list,$arr);
        }
        return response()->json([
            'data' => $list
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.permission.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PermissionCreateRequest $request)
    {
        $data = $request->all();
        if (Permission::create($data)){
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'fromdata' => [
                    'parent_id'=>$data['parent_id'],
                    'model'=>'permission'
                ],
                'message' => '添加成功'
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
        $permission = Permission::findOrFail($id);
        return view('admin.permission.edit',compact('permission'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PermissionUpdateRequest $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $data = $request->all();
        if ($permission->update($data)){
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'fromdata' => [
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        $permission = Permission::find($ids[0]);
        if (!$permission){
            return response()->json(['code'=>-1,'msg'=>'权限不存在']);
        }
        //如果有子权限，则禁止删除
        if (Permission::where('parent_id',$ids[0])->first()){
            return response()->json(['code'=>2,'msg'=>'存在子权限禁止删除']);
        }

        if ($permission->delete()){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
