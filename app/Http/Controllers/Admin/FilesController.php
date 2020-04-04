<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicController;
use App\Models\AttachmentRoup;
use App\Models\Attachment;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    private $file_type = ['mp3','flv','txt','rar']; //文件类型
    private $image_type = ["png", "jpg", "gif","jpeg","bmp"]; //图片类型
    /**
     * 文件库列表
     */
    public function getFiles(Request $request)
    {

        if ($request->ajax()) {
            $model = Attachment::query();

            $keywords = $request->get('keywords','');
            $type = $request->get('type','');
            $groupid = $request->get('group_id',-1);

            if ($groupid >= 0) {
                $model = $model->where('group_id', $groupid);
            }
            if (!empty($keywords)) {
                $keyword = $this->escape_like($keywords);
                $model->whereRaw("( LOCATE('".$keyword."', `filename`)>0 )");
            }
            if (!empty($type)) {
                $model = $model->where('type', 'like', $type . '%');
            }
            // 文件列表
            $res = $model->orderBy('created_at', 'desc')->with(['group'])->paginate($request->get('limit', 16))->toArray();

            foreach ($res['data'] as &$row){
                $row['file_url'] = tomedia($row['path']);
            }
            unset($row);
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res['total'],
                'limit' => 16,
                'page' => $request->get('page',1),
                'data' => $res['data'],
            ];
            return response()->json($data);
        }

        // 分组列表
        $config = (new \App\Models\Site)->getPluginset('attachment.set');
        $image_type = isset($config['image_type']) ? explode('|',$config['image_type']) : $this->image_type;
        $exts = implode('|',$image_type);
        $group_list = AttachmentRoup::orderBy('sort', 'desc')->get();
        return view('admin.files.selectfiles_v1',compact('exts','group_list'));
    }

    /**
     * 新增分组
     */
    public function addGroup(Request $request)
    {
        $data = $request->all();
        if (empty($data['group_name']))
            return response()->json(['code' => 0,'msg' => 'The given data was invalid.']);

        if ($group = AttachmentRoup::create(['name'=>$data['group_name']])) {
            return response()->json(['code' => 1, 'msg'  => '添加成功', 'data' => ['group_id'=>$group->id,'group_name'=>$group->name]]);
        }
        return response()->json(['code' => 0,'msg' => '添加失败']);
    }

    /**
     * 编辑分组
     */
    public function editGroup(Request $request)
    {
        $data = $request->all();
        $model = AttachmentRoup::findOrFail($data['group_id']);
        if ($model->update(['name'=>$data['group_name']])) {
            return response()->json(['code' => 1, 'msg'  => '修改成功']);
        }
        return response()->json(['code' => 0,'msg' => '修改失败',]);
    }

    /**
     * 删除分组
     */
    public function deleteGroup(Request $request)
    {
        $group_id = $request->input('group_id',0);
        if (empty($group_id)){
            return response()->json(['code'=>0,'msg'=>'请选择删除项']);
        }
        if (AttachmentRoup::destroy($group_id)){
            return response()->json(['code'=>1,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>0,'msg'=>'删除失败']);
    }

    /**
     * 批量移动文件分组
     */
    public function moveFiles(Request $request)
    {
        $model = new Attachment();
        $group_id = $request->input('group_id',0);
        $fileIds = $request->input('fileIds','');
        if ($model->moveGroup($group_id, $fileIds) !== false) {
            return response()->json(['code'=>1,'msg'=>'移动成功']);
        }
        return response()->json(['code'=>0,'msg'=>'移动失败']);
    }

    public function create()
    {
        $config = (new \App\Models\Site)->getPluginset('attachment.set');
        $image_type = isset($config['image_type']) ? explode('|',$config['image_type']) : $this->image_type;
        $file_type = isset($config['file_type']) ? explode('|',$config['file_type']) : $this->file_type;
        $exts = array_merge($image_type,$file_type);
        $exts = implode('|',$exts);
        return view('admin.files.create',compact('exts'));
    }

    public function index()
    {
        return view("admin.files.index");
    }

    public  function recycle()
    {
        return view("admin.files.recycle");
    }

    public function data(Request $request)
    {

        $model = Attachment::query();

        if (!empty($request->get('recycle',''))){
            $model = $model->onlyTrashed();
        }
        $res = $model->orderBy('created_at','desc')->with(['group'])->paginate($request->get('limit',30))->toArray();
        $res['data'] = set_medias($res['data'], "path");
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * 批量移动回收站
     * @param $fileIds
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Attachment::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    public function recover(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择恢复项']);
        }
        if (Attachment::onlyTrashed()->whereIn('id',$ids)->restore()){
            return response()->json(['code'=>0,'msg'=>'恢复成功']);
        }
        return response()->json(['code'=>1,'msg'=>'恢复失败']);
    }

    public function expurgate(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        foreach (Attachment::onlyTrashed()->whereIn('id',$ids)->get() as $model){
            //删除储存文件
            (new PublicController)->delfile($model['path'],$model['storage']);
            //删除文件
            $model->forceDelete();

        }
        return response()->json(['code'=>0,'msg'=>'删除成功']);
    }

    public function download(Request $request)
    {
        $pathToFile = $request->get('pathToFile','');
        if (!empty($pathToFile))
        {
            return response()->download($pathToFile);
        }

    }

}
