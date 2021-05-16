<?php

if (!function_exists('GetClientIp')) {
    function GetClientIp() {
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return $arr[0];
        }
        return $_SERVER['REMOTE_ADDR'];
    }
}

if (!function_exists('sexists')) {
    function sexists($string, $find) {
        return !(strpos($string, $find) === FALSE);
    }
}

if (!function_exists('price_format')) {
    function price_format($price) {
        if (empty($price)) { return ''; }
        $prices = explode('.', $price);
        if (intval($prices[1]) <= 0) {
            return $prices[0];
        }
        if (isset($prices[1][1]) && $prices[1][1] <= 0) {
            $price = $prices[0] . '.' . $prices[1][0];
        }
        return $price;
    }
}

if (!function_exists('is_array2')) {
    function is_array2($array) {
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
    function set_medias($list = [], $fields = null) {
        if (empty($list)) { return $list; }
        if (empty($fields)) {
            foreach ($list as &$row) {
                $row = ToMedia($row);
            }
            return $list;
        }
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        if (is_array2($list)) {
            foreach ($list as &$value) {
                foreach ($fields as $field) {
                    if (isset($list[$field])) {
                        $list[$field] = ToMedia($list[$field]);
                    }
                    if (is_array($value) && isset($value[$field])) {
                        $value[$field] = ToMedia($value[$field]);
                    }
                }
            }
            return $list;
        }
        foreach ($fields as $field) {
            if (isset($list[$field])) {
                $list[$field] = ToMedia($list[$field]);
            }
        }
        return $list;
    }
}

if (!function_exists('ToMedia')) {
    function ToMedia($src , $local_path = false, $is_cache = false) {
        $src = trim($src);
        if (empty($src)) { return $src; }
        if (substr($src, 0, 2) == '//') {
            return 'http:' . $src;
        }
        if ((substr($src, 0, 7) == 'http://') || (substr($src, 0, 8) == 'https://')) {
            return $src;
        }
        $storage = 'public';$st = 'local';
        if (!$local_path) {
            $config = \App\Models\Site::getPluginSet('attachment.set');
            if (!empty($config) && !empty($config['storage'])) {
                $st = $config['storage'];
            }
        }
        if ($st == 'qiniu') {
            $storage = 'qiniu';
        }
        $disk = \Illuminate\Support\Facades\Storage::disk($storage);
        $url = $disk->url($src);
        if ($is_cache) {
            $url .= "?v=" . time();
        }
        return $url;
    }
}

if (!function_exists('format_date')) {
    function format_date($time) {
        $is_date = strtotime($time) ? strtotime($time) : false;
        if (!$is_date) { return $time; }
        $today = time();
        $difference = $today - $time;
        $msg = $time;
        switch ($difference) {
            case $difference <= 60 :
                $msg = '刚刚';
                break;
            case $difference > 60 && $difference <= 3600 :
                $msg = floor($difference / 60) . '分钟前';
                break;
            case $difference > 3600 && $difference <= 86400 :
                $msg = floor($difference / 3600) . '小时前';
                break;
            case $difference > 86400 && $difference <= 2592000 :
                $msg = floor($difference / 86400) . '天前';
                break;
            case $difference > 2592000 &&  $difference <= 7776000:
                $msg = floor($difference / 2592000) . '个月前';
                break;
            case $difference > 7776000:
                $msg = '很久以前';
                break;
        }
        return $msg;
    }
}

if (!function_exists('byteCount')) {
    function byteCount($bit) {
        $type = ['Bytes','KB','MB','GB','TB'];
        for($i = 0; $bit >= 1024; $i++) {
            $bit /= 1024;
        }
        return ( floor($bit*100) / 100 ). $type[$i];
    }
}

if (!function_exists('cut_str')) {
    function cut_str($string, $sublen, $start = 0, $code = 'UTF-8') {
        if ($code == 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);
            if (count($t_string[0]) - $start > $sublen) {
                return join('', array_slice($t_string[0], $start, $sublen));
            }
            return join('', array_slice($t_string[0], $start, $sublen));
        }
        $start = $start * 2;
        $sublen = $sublen * 2;
        $strlen = strlen($string);
        $tmpstr = '';
        for ($i = 0; $i < $strlen; $i++) {
            if ($i >= $start && $i < ($start + $sublen)) {
                if (ord(substr($string, $i, 1)) > 129) {
                    $tmpstr .= substr($string, $i, 2);
                    $i++;
                    continue;
                }
                $tmpstr .= substr($string, $i, 1);
            }
            if (ord(substr($string, $i, 1)) > 129) {
                $i++;
            }
        }
        return $tmpstr;
    }
}