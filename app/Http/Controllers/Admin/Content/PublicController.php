<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Site;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PublicController extends Controller
{
    private $file_type;
    private $image_type;
    private $config = [];
    private $storage = 'local';
    private $group_id = 0;

    public function FileUpload(Request $request): \Illuminate\Http\JsonResponse
    {
        $param = $request->only(['upload_type']);
        $upload_type = 'formData';
        if(!empty($param['upload_type'])){
            $upload_type = $param['upload_type'];
        }
        if (!extension_loaded('fileinfo')){
            return response()->json(['code'=>200, 'msg' => 'fileinfo拓展未开启']);
        }
        $this->config = Site::getPluginSet('attachment.set');
        $this->image_type = isset($this->config['image_type']) ? explode('|', $this->config['image_type']) : Attachment::image_type;
        $this->file_type = isset($this->config['file_type']) ? explode('|', $this->config['file_type']) : Attachment::file_type;
        $this->storage = $this->config['storage'] ?? $this->storage;
        $this->group_id = $request->input('group_id',0);
        $data = ['code'=>200, 'msg'=>'fail'];
        if ($upload_type == 'formData') {
            $data = $this->formData($request);
        }elseif ($upload_type == 'formData') {
            $data = $this->Network_picture_extraction($request);
        }
        return response()->json($data);
    }

    /**
     * @param $file_path
     * @param $storage
     * @return array
     */
    public function delFile($file_path, $storage): array
    {
        if (empty($file_path)) {
            return ['code' => 205, 'msg' => '路径不能为空'];
        }
        try {
            $disk = $this->getDisk($storage);
            if (!$disk->exists($file_path)) {
                return ['code' => 205, 'msg' => '文件不存在'];
            }
            if ($disk->delete($file_path)) {
                return ['code' => 0, 'msg' => '删除成功'];
            }
        }catch (\Exception $e){
            logger()->error('delFile error:'.$e->getMessage().' path: '.$file_path);
        }
        return ['code' => 205, 'msg' => '删除失败'];
    }

    /**
     * @param $request
     * @return array
     */
    public function formData($request)
    {
        //返回信息json
        $file = $request->file('iFile');
        //检查文件是否上传完成`
        if ($file->isValid()){
            try {
                return $this->putFiles([
                    'ext' => $file->getClientOriginalExtension(),
                    'filetype' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'file_name' => $file->getClientOriginalName(),
                ], $file);
            }catch (\Exception $e) {
                return ['code'=>200, 'msg'=> $e->getMessage()];
            }
        }
        return ['code'=>200, 'msg'=> $file->getErrorMessage()];
    }

    /**
     * @param $request
     * @return array
     */
    private function Network_picture_extraction($request): array
    {
        $url = trim($request->input('url'));
        if (empty($url)) {
            return ['code'=>200, 'msg'=>'文件地址不存在'];
        }
        $url_host = parse_url($url, PHP_URL_HOST);
        if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $url_host)) {
            return ['code'=>200, 'msg'=>'网络链接不支持IP地址!'];
        }
        $client = new Client(['verify' => false]);
        try{
            $resp = $client->get($url);
            if (intval($resp->getStatusCode()) != 200) {
                return ['code'=>200, 'msg'=>'提取文件失败: 未找到该资源文件'];
            }
            $ClientSize = $resp->getHeader('Content-Length')[0];
            $filetype = $resp->getHeader('content-type')[0];
            if (empty($filetype)) {
                return ['code'=>200, 'msg' => '提取资源失败, 资源文件类型错误'];
            }
            $str = explode('/', $filetype);
            $f_name = pathinfo(urldecode($url));
            return $this->putFiles([
                'ext' => $str[1],
                'filetype' => $filetype,
                'file_size' => $ClientSize,
                'file_name' => trim($f_name['basename']),
            ], $resp->getBody()->getContents(), true);
        }catch (\Exception $e) {
            return ['code'=>200, 'msg'=> '提取文件失败, 错误信息: '.$e->getMessage()];
        }
    }

    /**
     * @param $resData
     * @param $file_content
     * @param false $is_network
     * @return array
     */
    private function putFiles($resData, $file_content, $is_network = false): array
    {
        $image_size = isset($this->config['image_size']) ? intval($this->config['image_size']) : Attachment::image_size;
        $file_size = isset($this->config['file_size']) ? intval($this->config['file_size']) : Attachment::file_size;
        $attachment_limit = isset($this->config['attachment_limit']) ? intval($this->config['attachment_limit']) : 0;

        $maxSize = $image_size/1024;
        $extensions = $this->image_type;
        $resData['ext'] = strtolower($resData['ext']);
        if (in_array($resData['ext'], $this->file_type)) {
            $maxSize = $file_size/1024;
            $extensions = $this->file_type;
        }
        $allowed_extensions = array_map('strtolower', $extensions);
        if (!in_array($resData['ext'], $allowed_extensions)){
            return ['code'=>200, 'msg'=> '仅支持 '.implode(',', $allowed_extensions).' 格式'];
        }
        if (intval($resData['file_size']) > $maxSize*1024*1024){
            return ['code'=>200, 'msg'=> '附件大小限制 '.$maxSize.'M'];
        }
        if ($attachment_limit > 0) {
            if (number_format($attachment_limit,2) < number_format($this->folderSize()+$resData['file_size'], 2)) {
                return ['code'=>200, 'msg'=> '附件空间限制 '.$attachment_limit.'M'];
            }
        }

        $str = explode('/', $resData['filetype']);
        $newPath = $str[0].'s/'.date('Ymd');
        if ($is_network) {
            $newPath .= '/'.str_random(random_int(30,35)).".".$resData['ext'];
        }
        $disk = $this->getDisk();
        $user = auth('admin')->user();
        if($path = $disk->put($newPath, $file_content)){
            $arr = [
                'file_name' => $resData['file_name'],
                'extension' => $resData['ext'],
                'file_size' => $resData['file_size'],
                'file_path' => $path,
                'file_url' => $disk->url($path),
                'file_type' => $resData['filetype'],
                'group_id' => intval($this->group_id),
                'storage' => $this->storage,
                'uuid' => $user->uuid ?? '',
            ];
            $res = Attachment::addUploadFile($arr);
            $arr['file_id'] = $res['id'];
            return [
                'code'  => 0,
                'msg'   => '上传成功',
                'data'  => $arr,
            ];
        }
        return ['code'=>200, 'msg'=>'上传失败'];
    }

    /**
     * @param string $storage
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    private function getDisk($storage = 'local'): \Illuminate\Contracts\Filesystem\Filesystem
    {
        $disk = 'public';
        if ($storage == 'qiniu') {
            $disk = 'qiniu';
        }
        return Storage::disk($disk);
    }

    /**
     * @return int|mixed
     */
    private function folderSize()
    {
        try {
            return cache()->remember('folderSize', Carbon::now()->addMinutes(2), function () {
                $file_size = 0;
                $list = File::allFiles(config('filesystems.disks.public.root'));
                if (!empty($list)) {
                    foreach($list as $file) {
                        $file_size += round($file->getSize(),2);
                    }
                }
                return round($file_size/1048576,2);
            });
        }catch (\Exception $e) { }
        return 0;
    }

}
