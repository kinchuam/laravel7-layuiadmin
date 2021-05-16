<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AttachmentGroup;
use App\Models\Attachment;
use App\Models\Site;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function index()
    {
        return view("admin.content.files.index");
    }

    public  function recycle()
    {
        return view("admin.content.files.recycle");
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $model = Attachment::query()->select(['id','filename','path','suffix','type','size','group_id','storage','file_url','created_at','deleted_at']);
        if (!empty($request->get('recycle'))) {
            $model->onlyTrashed();
        }
        if ($keywords = trim($request->get('keywords'))) {
            $keyword = $this->escape_like($keywords);
            $model->whereRaw("( LOCATE('".$keyword."', `filename`) > 0 )");
        }
        $type = trim($request->get('type'));
        if (!empty($type) && $type == 'image') {
            $model->whereIn('suffix', Attachment::image_type);
        }
        $group_id = $request->get('group_id',-1);
        if ($group_id >= 0) {
            $model = $model->where('group_id', intval($group_id));
        }
        $res = $model->orderBy('created_at','desc')->with(['group:id,name'])->paginate($request->get('limit',10))->toArray();
        $list = $res['data'];
        if (!empty($list)) {
            foreach ($list as $ke => $row) {
                $list[$ke]['file_path'] = $row['path'];
                $list[$ke]['file_url'] = $list[$ke]['path'] = ToMedia($row['path']);
                $list[$ke]['name'] = $row['filename'];
                $list[$ke]['file_type'] = $row['type'];
                $list[$ke]['type'] = $row['suffix'];
                $list[$ke]['thumb'] = $list[$ke]['path'];
            }
        }
        return response()->json([
            'code' => 0,
            'message'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    public function create()
    {
        $config = Site::getPluginSet('attachment.set');
        $image_type = isset($config['image_type']) ? explode('|', $config['image_type']) : Attachment::image_type;
        $file_type = isset($config['file_type']) ? explode('|', $config['file_type']) : Attachment::file_type;
        $file_type = implode('|', array_unique(array_merge($image_type, $file_type)));
        return view('admin.content.files.create', compact('file_type'));
    }

    /**
     * 批量移动回收站
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1, 'message'=>'请选择删除项']);
        }
        $ids = is_array($ids)?$ids:[$ids];
        $list = Attachment::query()->whereIn('id', $ids)->get();
        if ($list->isEmpty()){
            return response()->json(['code'=>1, 'message'=>'记录不存在']);
        }
        try {
            foreach ($list as $model) {
                ActivityLog::addLog('附件 ID:'.$model->id.' 移到回收站', $model->toArray(), $model);
                $model->delete();
            }
            return response()->json(['code'=>0, 'message'=>'删除成功']);
        }catch (\Exception $e) {
            return response()->json(['code'=>1, 'message'=>'系统错误']);
        }
    }

    public function recover(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1, 'message'=>'请选择恢复项']);
        }
        $ids = is_array($ids)?$ids:[$ids];
        $list = Attachment::onlyTrashed()->whereIn('id', $ids)->get();
        if ($list->isEmpty()){
            return response()->json(['code'=>1, 'message'=>'记录不存在']);
        }
        try {
            foreach ($list as $model) {
                ActivityLog::addLog('恢复附件 ID:'.$model->id, $model->toArray(), $model);
                $model->restore();
            }
            return response()->json(['code'=>0, 'message'=>'恢复成功']);
        }catch (\Exception $e) {
            return response()->json(['code'=>1, 'message'=>'系统错误']);
        }
    }

    public function expurgate(Request $request): \Illuminate\Http\JsonResponse
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1, 'message'=>'请选择删除项']);
        }
        $ids = is_array($ids)?$ids:[$ids];
        $list = Attachment::onlyTrashed()->whereIn('id', $ids)->get();
        if ($list->isEmpty()){
            return response()->json(['code'=>1, 'message'=>'记录不存在']);
        }
        try {
            foreach ($list as $model) {
                (new PublicController)->delfile($model['path'], $model['storage']);
                ActivityLog::addLog('删除附件 ID:'.$model->id, $model->toArray(), $model);
                $model->forceDelete();
            }
            return response()->json(['code'=>0, 'message'=>'删除成功']);
        }catch (\Exception $e) {
            return response()->json(['code'=>1, 'message'=>'系统错误']);
        }
    }

    public function download(Request $request)
    {
        $pathToFile = $request->get('pathToFile');
        return !empty($pathToFile) ? response()->download($pathToFile) : 'fail';
    }

    //文件库
    public function getFiles()
    {
        $group_list = AttachmentGroup::query()->orderBy('sort', 'desc')->get(['id','name','name as title']);
        return view('admin.content.files.open_files_v1', compact('group_list'));
    }

}
