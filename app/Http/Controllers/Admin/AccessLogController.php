<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccessLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use GuzzleHttp;

class AccessLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $methods = AccessLog::$methods;
        return view('admin.logs.access.index',compact('methods'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = AccessLog::query();
        if (!empty($request->get('method'))){
            $model = $model->where('method',$request->get('method'));
        }
        if (!empty($request->get('path'))){
            $model = $model->where('path','like',$request->get('path').'%');
        }
        if (!empty($request->get('ip'))){
            $model = $model->where('ip','like',$request->get('ip').'%');
        }

        $res = $model->orderBy('id','desc')->paginate($request->get('limit',30))->toArray();
        $methodColors = AccessLog::$methodColors;
        $agent = new Agent();
        foreach ($res['data'] as &$row)
        {
            $agent->setUserAgent($row['agent']);
            $row['code'] = "{$row['input']}";
            $browser = $agent->browser();
            $system = $agent->platform();
            $row['platform'] = $system.' '.$agent->version($system);
            $row['browser'] = $browser.' '.$agent->version($browser);
            $row['method_color'] = $methodColors[$row['method']]??'red';
        }
        unset($row);
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ];
        return response()->json($data);
    }

    public function get_ip_localhost($ip = null)
    {
        if (empty($ip)) return false;

        $data = Cache::get('ip_localhost:'.$ip, function ()use ($ip) {
            $client = new GuzzleHttp\Client();
            $res = $client->request('GET', 'http://ip-api.com/json/'.$ip, [
                'query' => ['fields'=>'status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,query','lang' => 'zh-CN']
            ]);
            $a = $res->getBody()->getContents();
            $b = json_decode($a,true);
            cache(['ip_localhost:'.$ip => $b], now()->addMinutes(60));
            return $b;
        });

        return $data;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $item = AccessLog::findOrFail($id);
        if (!filter_var($item['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
        {
            $ipdata['address'] = '内网';
            $ipdata['lon_lat'] = '未知';
            $ipdata['isp'] = '内网';
        }
        else
            {
            $ipdata = [];
            if (empty($item['ipdata'])){
                $record = $this->get_ip_localhost($item['ip']);
                if ($record['status'] == 'success') {
                    $item->update(['ipdata' => json_encode($record)]);
                }
            }else if ($item['ipdata']) {
                $record = json_decode($item['ipdata'],true);
            }

            $ipdata['address'] = ($record['country']??'') . " " . ($record['regionName']??'') . " " . ($record['city']??'');
            $ipdata['lon_lat'] = ($record['lat']??'') . ' , ' . ($record['lon']??'');
            $ipdata['isp'] = ($record['isp']??'');
        }

        $arr = [];
        if (!empty($item['agent']))
        {
            $agent = new Agent();
            $agent->setUserAgent($item['agent']);

            $browser = $agent->browser();
            $system = $agent->platform();
            $arr = [
                'device_name' => $agent->device(),
                'system_name' => $system.' '.$agent->version($system),
                'browser_name' => $browser.' '.$agent->version($browser),
                'isRobot' => $agent->isRobot(),
                'Robot_name' => $agent->robot(),
                'languages' => implode('、',$agent->languages()),
            ];
        }

        return view('admin.logs.access.show',compact('item','ipdata','arr'));
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
        if (AccessLog::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

}
