<?php

if (!function_exists('strexists')) {
    function strexists($string, $find)
    {
        return !(strpos($string, $find) === FALSE);
    }
}

if (!function_exists('price_format')) {
    function price_format($price)
    {
        if (empty($price)){
            return '';
        }
        $prices = explode('.', $price);
        if (intval($prices[1]) <= 0) {
            $price = $prices[0];
        } else {
            if (isset($prices[1][1]) && $prices[1][1] <= 0) {
                $price = $prices[0] . '.' . $prices[1][0];
            }
        }
        return $price;
    }
}

if (!function_exists('tpl_form_field_image')){
    function tpl_form_field_image($name, $value = '')
    {
        $html = '<div class="layui-upload" >
            <input type="text" class="layui-input" name="'.$name.'"  value="'.$value.'" style="width: 60%;float: left;">
            <button type="button" class="layui-btn" data-default_pic="'.asset('static/admin/img/default-pic.jpg').'"  data-nopic="'.asset('static/admin/img/nopic.png').'" onclick="active.multi_image(this,\''.route("admin.files.getFiles").'\');">上传图片</button>
            <div class="input-group">
                <ul class="layui-upload-box">
                    <li>
                        <img src="'.(!empty($value)?tomedia($value):asset('static/admin/img/default-pic.jpg')).'" onerror="this.src=\''.asset('static/admin/img/nopic.png').'\'"  alt="'.$value.'"/>
                        <i class="layui-icon layui-icon-close-fill"  data-default_pic="'.asset('static/admin/img/default-pic.jpg').'"  onclick="active.del_image(this)"></i>
                    </li>
                </ul>
            </div>
        </div>';

        return $html;
    }
}

if (!function_exists('tpl_form_field_multi_image')){
    function tpl_form_field_multi_image($name, $value = [], $limit = 5)
    {
        $html = '<div class="layui-upload">
            <button type="button" class="layui-btn" data-name="'.$name.'" data-limit="'.$limit.'" data-default_pic="'.asset('static/admin/img/default-pic.jpg').'"  data-nopic="'.asset('static/admin/img/nopic.png').'" data-multiple="true" onclick="active.multi_image(this,\''.route("admin.files.getFiles").'\');">多图片上传</button>
            <blockquote class="input-group layui-elem-quote layui-quote-nm" style="margin-top: 10px;">
                <ul class="layui-clear layui-upload-box">';
                    if (!empty($value) && is_array($value)) {
                        foreach ($value as $row) {
                            $html .= '<li>
                                <img src="'.(!empty($row)?tomedia($row):asset('static/admin/img/default-pic.jpg')).'" onerror="this.src=\''.asset('static/admin/img/nopic.png').'\'"  alt="'.$row.'"/>
                                <i class="layui-icon layui-icon-close-fill" data-default_pic="'.asset('static/admin/img/default-pic.jpg').'"  onclick="active.del_image(this)"></i>
                                <input type="hidden" name="'.$name.'[]" value="'.$row.'">
                            </li>';
                        }
                    }
        $html .= '</ul>
            </blockquote>
        </div>';
        return $html;
    }
}

if (!function_exists('is_array2')) {
    function is_array2($array)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                return is_array($v);
            }
            return false;
        }
        return false;
    }
}

if (!function_exists('set_medias')) {
    function set_medias($list = [], $fields = null)
    {
        if (empty($list)) {
            return [];
        }
        if (empty($fields)) {
            foreach ($list as &$row) {
                $row = tomedia($row);
            }
            return $list;
        }
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        if (is_array2($list)) {
            foreach ($list as $key => &$value) {
                foreach ($fields as $field) {
                    if (isset($list[$field])) {
                        $list[$field] = tomedia($list[$field]);
                    }
                    if (is_array($value) && isset($value[$field])) {
                        $value[$field] = tomedia($value[$field]);
                    }
                }
            }
            return $list;
        } else {
            foreach ($fields as $field) {
                if (isset($list[$field])) {
                    $list[$field] = tomedia($list[$field]);
                }
            }
            return $list;
        }
    }
}

if (!function_exists('tomedia')){
    function tomedia($src , $local_path = false, $is_cahce = false)
    {
        $config = (new App\Models\Site)->getPluginset('attachment.set');

        $src = trim($src);
        if (empty($src)) {
            return '';
        }
        if (substr($src, 0, 2) == '//') {
            return 'http:' . $src;
        }
        if ((substr($src, 0, 7) == 'http://') || (substr($src, 0, 8) == 'https://')) {
            return $src;
        }
        $path = public_path('uploads/'.$src);
        if ($local_path || empty($config['storage']) || $config['storage']=='local' || file_exists($path)) {
            $url = asset('/uploads/'. $src);
        } else {
            $url = attachment_set_attach_url($config) . (substr($src, 0, 1) == '/' ? $src : '/'.$src);
        }
        if ($is_cahce) {
            $url .= "?v=" . time ();
        }
        return $url;
    }

    function attachment_set_attach_url($config)
    {
        $attach_url = asset('/uploads/');
        if (!empty($config['storage'])) {
            if ($config['storage'] == 'ftp') {
                $attach_url = '';
            } elseif ($config['storage'] == 'alioss') {
                $attach_url = '';
            } elseif ($config['storage'] == 'qiniu') {
                $https_url = config('filesystems.disks.qiniu.domains.https');
                $attach_url = $https_url=='' ? 'http://'.config('filesystems.disks.qiniu.domains.default') :'https://'. $https_url;
            } elseif ($config['storage'] == 'cos') {
                $attach_url = '';
            }
        }
        return $attach_url;
    }
}

if (!function_exists('isMobile')) {
    function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }

        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;// 找不到为flase,否则为TRUE
        }

        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {

            $clientkeywords = [
                'mobile', 'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg',
                'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod',
                'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce',
                'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap'
            ];

            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }

        if (isset($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('time2Units')) {
    function time2Units($time)
    {
        $year = floor($time / 60 / 60 / 24 / 365);
        $time -= $year * 60 * 60 * 24 * 365;
        $month = floor($time / 60 / 60 / 24 / 30);
        $time -= $month * 60 * 60 * 24 * 30;
        $week = floor($time / 60 / 60 / 24 / 7);
        $time -= $week * 60 * 60 * 24 * 7;
        $day = floor($time / 60 / 60 / 24);
        $time -= $day * 60 * 60 * 24;
        $hour = floor($time / 60 / 60);
        $time -= $hour * 60 * 60;
        $minute = floor($time / 60);
        $time -= $minute * 60;
        $second = $time;
        $elapse = '';
        $unitArr = ['年' => 'year', '个月' => 'month', '周' => 'week', '天' => 'day', '小时' => 'hour', '分钟' => 'minute', '秒' => 'second'];
        foreach ($unitArr as $cn => $u) {
            if ($$u > 0) {
                $elapse = $$u . $cn;
                break;
            }
        }
        return $elapse . '前';
    }
}

if (!function_exists('bytecount')) {
    function bytecount($str) {
        if (strtolower($str[strlen($str) -1]) == 'b') {
            $str = substr($str, 0, -1);
        }
        if(strtolower($str[strlen($str) -1]) == 'k') {
            return floatval($str) * 1024;
        }
        if(strtolower($str[strlen($str) -1]) == 'm') {
            return floatval($str) * 1048576;
        }
        if(strtolower($str[strlen($str) -1]) == 'g') {
            return floatval($str) * 1073741824;
        }
        return $str;
    }
}
