@extends('admin.from')

@section('content')
    <style>
        .layui-table tr .set { background-color: #f2f2f2; width: 130px; }
    </style>
    <div class="layui-card-body">
        <table class="layui-table" style="table-layout: fixed;">
            <tbody>
            <tr>
                <td class="set">路由</td>
                <td colspan="3">{{isset($item['path'])?$item['path']:''}}</td>
            </tr>
            <tr>
                <td class="set">IP地址</td>
                <td>{{isset($item['ip'])?$item['ip']:''}}</td>
                <td class="set">请求方式</td>
                <td>{{isset($item['method'])?$item['method']:''}}</td>
            </tr>
            <tr>
                <td class="set">请求参数</td>
                <td colspan="3"><pre>{{isset($item['input'])?$item['input']:''}}</pre></td>
            </tr>
            <tr>
                <td class="set">Agent</td>
                <td colspan="3">{{isset($item['agent'])?$item['agent']:''}}</td>
            </tr>
            <tr>
                <td class="set">IP解析地址</td>
                <td>{{(isset($item['address'])?$item['address']:'')}}</td>
                <td class="set">运营商</td>
                <td> {{isset($item['isp'])?$item['isp']:''}}</td>
            </tr>
            <tr>
                <td class="set">操作系统</td>
                <td>{{$item['system_name']?$item['system_name']:''}}</td>
                <td class="set">浏览器</td>
                <td>{{$item['browser_name']?$item['browser_name']:''}}</td>
            </tr>
            <tr>
                <td class="set">设备名称</td>
                <td>{{isset($item['device_name'])?$item['device_name']:''}}</td>
                <td class="set">语言</td>
                <td>{{isset($item['languages'])?$item['languages']:''}}</td>
            </tr>
            <tr>
                <td class="set">是否机械人</td>
                <td>{{isset($item['isRobot'])&&$item['isRobot']?'是':'否'}}</td>
                <td class="set">机械人名称</td>
                <td>{{isset($item['Robot_name'])?$item['Robot_name']:''}}</td>
            </tr>
            <tr>
                <td class="set">请求时间</td>
                <td>{{isset($item['created_at'])?$item['created_at']:''}}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
