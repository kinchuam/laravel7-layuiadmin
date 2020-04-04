<?php
namespace App\Http\Controllers;

use App\Helpers\File;
use App\Models\Attachment;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    private $file_type = ['mp3','txt']; //文件类型
    private $image_type = ["png", "jpg", "gif","jpeg","bmp"]; //图片类型
    private $upload_type = 'formData'; //上传方式
    private $storage = 'local'; //储存方式

    //文件上传
    public function FileUpload(Request $request)
    {
        $param = $request->all();
        $upload_type = $this->upload_type;
        if(isset($param['upload_type']) && !empty($param['upload_type'])){
            $upload_type = $param['upload_type'];
        }

        if (!extension_loaded('fileinfo')){
            return response()->json(['code'=>200, 'msg'=>'fileinfo拓展未开启']);
        }

        switch ($upload_type){
            case 'formData':
                $data = $this->formData();
                break;
            case 'NetworkToLocal':
                $data = $this->Network_picture_extraction();
                break;
        }
        return response()->json($data);
    }

    /**
     * @return array
     * Author: phpstorm
     * Date: 2019/9/8 22:41
     */
    public function formData()
    {
        $group_id = Request()->input('group_id',0);

        $config = (new \App\Models\Site)->getPluginset('attachment.set');
        $image_type = isset($config['image_type']) ? explode('|',$config['image_type']) : $this->image_type;
        $image_size = isset($config['image_size']) ? $config['image_size'] : 2048;
        $file_type = isset($config['file_type']) ? explode('|',$config['file_type']) : $this->file_type;
        $file_size = isset($config['file_size']) ? $config['file_size'] : 2048;

        $storage = isset($config['storage']) ? $config['storage'] : $this->storage;

        //返回信息json
        $data = ['code'=>200, 'msg'=>'上传失败', 'data'=>''];
        $file = Request()->file('iFile');

        //检查文件是否上传完成
        if ($file->isValid()){
            //检测文件类型
            $ext = $file->getClientOriginalExtension();
            $filetype = $file->getMimeType();
            $ClientSize = $file->getSize();

            if (in_array(strtolower($ext),$image_type)) {
                $maxSize = $image_size/1024;
                $allowed_extensions = array_map('strtolower',$image_type);
            }else {
                $maxSize = $file_size/1024;
                $allowed_extensions = array_map('strtolower',$file_type);
            }

            if (!in_array(strtolower($ext),$allowed_extensions)){
                $data['msg'] = "仅支持 ".implode(",",$allowed_extensions)." 格式";
                return $data;
            }
            //检测文件大小
            if ($ClientSize > $maxSize*1024*1024){
                $data['msg'] = "附件大小限制 ".$maxSize."M";
                return $data;
            }
        }else{
            $data['msg'] = $file->getErrorMessage();
            return $data;
        }
        $str = explode('/',$filetype);
        $newFile = $str[0].'s/'.date('Ymd').'/'.date('Ymd')."_".uniqid().".".$ext;

        $res = File::file_upload(file_get_contents($file->getRealPath()) , $storage , $newFile);

        if($res){
            $savedata = [
                'file_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'file_size' => $ClientSize,
                'file_path' => $res['file_path'],
                'file_type' => $filetype,
                'file_url' => (isset($storage)&&$storage=='qiniu') ? $res['file_url'].'/'.$res['file_path'] : asset('uploads').'/'.$res['file_path'],//$res['file_url'],
                'group_id' => intval($group_id),
                'storage' => isset($storage)?$storage:'local',
            ];
            //添加记录
            $redata = $this->addUploadFile($savedata);
            $savedata['file_id'] = $redata->id;
            $data = [
                'code'  => 0,
                'msg'   => '上传成功',
                'data'  => $savedata,
            ];
        }else{
            $data['data'] = $file->getErrorMessage();
        }
        return $data;
    }

    /**
     * @return array
     * Author: phpstorm
     * Date: 2019/9/20 22:37
     */
    private function Network_picture_extraction() {

        //返回信息json
        $data = ['code'=>200, 'msg'=>'上传失败', 'data'=>''];
        $group_id = Request()->input('group_id',0);
        $url = trim(Request()->input('url'));

        $url_host =  parse_url($url, PHP_URL_HOST);
        $is_ip = preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url_host);
        if ($is_ip) {
            $data['msg'] = '网络链接不支持IP地址!';
            return $data;
        }

        if (empty($url)) {
            $data['msg'] = '文件地址不存在.';
            return $data;
        }

        $client = new Client(['verify' => false]);  //忽略SSL错误
        try{
            $resp = $client->get($url);
        }catch (\Exception $e) {
            $data['msg'] = '提取文件失败, 错误信息: ' . $e->getMessage();
            return $data;
        }

        if (intval($resp->getStatusCode()) != 200) {
            $data['msg'] = '提取文件失败: 未找到该资源文件.';
            return $data;
        }

        $ClientSize = $resp->getHeader('Content-Length')[0];
        $filetype = $resp->getHeader('content-type')[0];
        if (empty($filetype)) {
            $data['msg'] = '提取资源失败, 资源文件类型错误.';
            return $data;
        } else {
            $str = explode('/',$filetype);
            $ext = $str[1];
        }

        $config = (new \App\Models\Site)->getPluginset('attachment.set');
        $image_type = isset($config['image_type']) ? explode('|',$config['image_type']) : $this->image_type;
        $image_size = isset($config['image_size']) ? $config['image_size'] : 2048;
        $file_type = isset($config['file_type']) ? explode('|',$config['file_type']) : $this->file_type;
        $file_size = isset($config['file_size']) ? $config['file_size'] : 2048;

        $storage = isset($config['storage']) ? $config['storage'] : $this->storage;

        if (in_array(strtolower($ext),$image_type)) {
            $maxSize = $image_size/1024;
            $allowed_extensions = array_map('strtolower',$image_type);
        }else {
            $maxSize = $file_size/1024;
            $allowed_extensions = array_map('strtolower',$file_type);
        }

        if (!in_array(strtolower($ext),$allowed_extensions)){
            $data['msg'] = "仅支持 ".implode(",",$allowed_extensions)." 格式";
            return $data;
        }
        //检测文件大小
        if (intval($ClientSize) > $maxSize*1024*1024){
            $data['msg'] = "附件大小限制 ".$maxSize."M";
            return $data;
        }
        $filename = date('Ymd')."_".uniqid().".".$ext;
        $newFile = $str[0].'s/'.date('Ymd').'/'.$filename;

        $res = File::file_upload($resp->getBody()->getContents() , $storage , $newFile);

        if($res){
            $savedata = [
                'file_name' => $filename,
                'extension' => $ext,
                'file_size' => intval($ClientSize),
                'file_path' => $res['file_path'],
                'file_type' => $filetype,
                'file_url' => (isset($storage)&&$storage=='qiniu') ? $res['file_url'].'/'.$res['file_path'] : asset('uploads').'/'.$res['file_path'],//$res['file_url'],
                'group_id' => $group_id,
                'storage' => isset($storage)? $storage:'local',
            ];

            //添加记录
            $redata = $this->addUploadFile($savedata);
            $savedata['file_id'] = $redata->id;

            $data = [
                'code'  => 0,
                'msg'   => '保存成功',
                'data'  => $savedata,
            ];
        }else{
            $data['data'] = $res;
        }
        return $data;
    }

    /**
     * @param $file_path
     * @param $storage
     * @return array
     * Author: phpstorm
     * Date: 2019/9/8 23:10
     */
    public function delfile($file_path,$storage)
    {
        $storage = isset($storage)?$storage:$this->storage;
        $data = File::del_file($file_path,$storage);
        return $data;
    }

    /**
     * @param array $fileInfo
     * @return mixed
     */
    private function addUploadFile(array $fileInfo)
    {
        $file = Attachment::create([
            'group_id' => $fileInfo['group_id'] > 0 ? intval($fileInfo['group_id']) : 0,
            'storage' => isset($fileInfo['storage'])?$fileInfo['storage']:'local',
            'file_url' => isset($fileInfo['file_url'])?$fileInfo['file_url']:'',
            'path' => $fileInfo['file_path'],
            'filename' => $fileInfo['file_name'],
            'size' => $fileInfo['file_size'],
            'suffix' => $fileInfo['extension'],
            'type' => $fileInfo['file_type'],
            'uuid' => auth('admin')->user()?auth('admin')->user()->uuid:'',
        ]);
        return $file;
    }

}
