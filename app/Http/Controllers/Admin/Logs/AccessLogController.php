<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Models\AccessLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class AccessLogController extends Controller
{

    public function index()
    {
        $methods = AccessLog::$methods;
        return view('admin.logs.access.index', compact('methods'));
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $model = AccessLog::query()->select(['id', 'path', 'method', 'input', 'platform', 'browser', 'ip', 'ip_data', 'created_at']);
        if ($method = trim($request->get('method'))) {
            $model->where('method', $method);
        }
        if ($path = trim($request->get('path'))) {
            $model->whereRaw("( LOCATE('".$this->escape_like($path)."', `path`) > 0 )");
        }
        if ($ip = trim($request->get('ip'))) {
            $model->whereRaw("( LOCATE('".$this->escape_like($ip)."', `ip`) > 0 )");
        }

        $res = $model->orderBy('id','desc')->paginate($request->get('limit',10))->toArray();
        $methodColors = AccessLog::$methodColors;
        $list = $res['data'];
        foreach ($list as $ke => $row) {
            $list[$ke]['method_color'] = $methodColors[$row['method']] ?: 'red';
            if (!empty($row['ip_data'])) {
                $ip_data = json_decode($row['ip_data'], true);
                $list[$ke]['ip_data'] = explode('|', $ip_data['region']);
            }
        }
        return response()->json([
            'code' => 0,
            'message'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

    public function show(int $id)
    {
        $item = AccessLog::findOrFail($id);
        if (!empty($item['agent'])) {
            $agent = new Agent();
            $agent->setUserAgent($item['agent']);
            $browser = $item['browser'];
            $system = $item['platform'];
            $item["device_name"] = $agent->device();
            $item["system_name"] = $system.' '.$agent->version($system);
            $item["browser_name"] = $browser.' '.$agent->version($browser);
            $item["isRobot"] = $agent->isRobot();
            $item["Robot_name"] = $agent->robot();
            $item["languages"] = implode('、',$agent->languages());
        }

        if ($item['ip_data']) {
            $ip_data = json_decode($item['ip_data'],true);
            if (!empty($ip_data['region'])) {
                $data = explode('|', $ip_data['region']);
                $record = [
                    "country" => $data[0],
                    "regionName" => $data[2],
                    "city" => $data[3],
                    "lat" => '',
                    "lon" => '',
                    "isp" => $data[4],
                ];
            }
            $item['address'] = ($record['country'] ?? '') . " " . ($record['regionName'] ?? '') . " " . ($record['city'] ?? '');
            $item['lon_lat'] = ($record['lat'] ?? '') . ' , ' . ($record['lon'] ?? '');
            $item['isp'] = ($record['isp'] ?? '');
        }
        return view('admin.logs.access.show',compact('item'));
    }

}
