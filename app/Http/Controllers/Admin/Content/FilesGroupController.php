<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\AttachmentGroup;
use Illuminate\Http\Request;

class FilesGroupController extends Controller
{

    public function index()
    {
        return view("admin.content.files_group.index");
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $model = AttachmentGroup::query()->select(['id', 'name', 'sort', 'created_at', 'updated_at']);
        $keywords = $request->get('keywords');
        if (!empty($keywords)) {
            $keyword = $this->escape_like($keywords);
            $model->whereRaw("( LOCATE('".$keyword."', `name`) > 0 )");
        }
        $res = $model->orderBy('sort','desc')->paginate($request->get('limit',10))->toArray();
        return response()->json([
            'code' => 0,
            'message'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    public function create()
    {
        return view('admin.content.files_group.create');
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['sort', 'group_name']);
        $data['sort'] = intval($data['sort']);
        $data['name'] = trim($data['group_name']);
        if ($model = AttachmentGroup::create($data)){
            ActivityLog::addLog('添加附件分组: '.$data['name'], $data, $model);
            return response()->json([
                'code' => 0,
                'message' => '添加成功',
                'data' => [
                    'group_id' => $model['id'],
                    'group_name' => $data['group_name']
                ],
            ]);
        }
        return response()->json(['code'=>1, 'message'=>'添加失败']);
    }

    public function edit(Request $request)
    {
        $item = AttachmentGroup::findOrFail(intval($request->get('id')));
        return view('admin.content.files_group.edit', compact('item'));
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = intval($request->get('id'));
        $data = $request->only(['sort', 'group_name']);
        $data['sort'] = intval($data['sort']);
        $data['name'] = trim($data['group_name']);
        $item = AttachmentGroup::findOrFail($id);
        if ($item->update($data)){
            ActivityLog::addLog('修改附件分组: '.$data['name'], $data, $item);
            return response()->json([
                'code' => 0,
                'message' => '修改成功',
                'data' => [
                    'group_id' => $id,
                    'group_name' => $data['group_name']
                ],
            ]);
        }
        return response()->json(['code'=>1, 'message'=>'修改失败']);
    }

    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = $request->get('ids');
        if (empty($ids)) {
            return response()->json(['code'=>1, 'message'=>'请选择删除项']);
        }
        $ids = is_array($ids)?$ids:[$ids];
        $list = AttachmentGroup::query()->whereIn('id', $ids)->with(['files:group_id'])->get(['id','name', 'sort']);
        if ($list->isEmpty()){
            return response()->json(['code'=>1, 'message'=>'记录不存在']);
        }
        try {
            foreach ($list as $model) {
                if (!$model->files->isEmpty()) {
                    return response()->json(['code'=>1, 'message'=>'分组下存在记录']);
                }
                ActivityLog::addLog('删除附件分组: '.$model['name'], $model->toArray(), $model);
                $model->delete();
            }
            return response()->json(['code'=>0, 'message'=>'删除成功']);
        }catch (\Exception $e) {
            return response()->json(['code'=>1, 'message'=>'系统错误']);
        }
    }

    public function moveFiles(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only(['group_id', 'fileIds']);
        $fileIds = $data['fileIds'];
        if (!is_array($fileIds)) {
            return response()->json(['code'=>0, 'message'=>'请选择删除项']);
        }
        if ((new Attachment)->moveGroup(intval($data['group_id']), $fileIds)) {
             return response()->json(['code'=>1, 'message'=>'删除成功']);
        }
        return response()->json(['code'=>0, 'message'=>'删除失败']);
    }

}
