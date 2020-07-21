<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use zgldh\QiniuStorage\QiniuStorage;

class File
{

    /**
     * @param $file
     * @param $storage
     * @param $newFile
     * @return array|bool
     */
    public Static function file_upload($file, $storage, $newFile)
    {
        if ($storage === 'qiniu'){
            $disk = QiniuStorage::disk('qiniu');
        }else{
            $disk = Storage::disk('localupload');
        }
        $res = $disk->put($newFile, $file);
        if(!empty($res)) {
            if ($storage === 'qiniu') {
                $file_path = $newFile;
                $https_url = env('QINIU_DOMAIN_HTTPS', '');
                $attach_url = !empty($https_url) ? 'https://'. $https_url : 'http://'. env('QINIU_DOMAIN_DEFAULT', '');
                $downloadUrl = $attach_url;
            }else{
                $file_path = $newFile;
                $downloadUrl = '';
            }

            return [
                'file_path'  => $file_path,
                'file_url'   => $downloadUrl
            ];
        }
        return false;
    }

    /**
     * @param $file_path
     * @param $storage
     * @return array
     */
    public Static function del_file($file_path,$storage)
    {
        $data = ['code'=>205, 'msg'=>'上传失败'];
        if (empty($file_path)) {
            $data['msg'] = "路径不能为空";
            return $data;
        }

        if ($storage === 'qiniu') {
            $disk = QiniuStorage::disk('qiniu');
        }else{
            $disk = Storage::disk('localupload');
            if (strpos($file_path, '/uploads/') !== false){
                $file_path = str_replace('/uploads/','', $file_path);
            }
        }

        if (!$disk->exists($file_path)) {
            $data['msg'] = "文件不存在";
            return $data;
        }

        if ($disk->delete($file_path)) {
            $data['code'] = 0;
            $data['msg'] = "删除成功";
            return $data;
        }

        $data['msg'] = "删除失败";
        return $data;
    }
}
