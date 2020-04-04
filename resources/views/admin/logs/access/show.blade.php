@extends('admin.from')

@section('content')

    <div class="layui-card-body">
        <table class="layui-table" style="table-layout: fixed;">

            <tbody>

            <tr>
                <td class="set">路由</td>
                <td>{{isset($item['path'])?$item['path']:''}}</td>
                <td class="set">IP地址</td>
                <td>{{isset($item['ip'])?$item['ip']:''}}</td>
            </tr>

            <tr>
                <td class="set">请求方式</td>
                <td>{{isset($item['method'])?$item['method']:''}}</td>
                <td class="set">请求参数</td>
                <td id="showview">{{isset($item['input'])?$item['input']:''}}</td>
            </tr>

            <tr>
                <td class="set">IP解析地址</td>
                <td>{{(isset($ipdata['address'])?$ipdata['address']:'')}}</td>
                <td class="set">运营商</td>
                <td> {{isset($ipdata['isp'])?$ipdata['isp']:''}}</td>
            </tr>


            <tr>
                <td class="set"> 请求时间</td>
                <td> {{isset($item['created_at'])?$item['created_at']:''}} </td>
            </tr>

            <tr>
                <td class="set">Agent</td>
                <td colspan="3">{{isset($item['agent'])?$item['agent']:''}}</td>
            </tr>

            <tr>
                <td class="set">操作系统</td>
                <td>{{$arr['system_name']?:''}}</td>
                <td class="set">浏览器</td>
                <td>{{$arr['browser_name']?:''}}</td>
            </tr>

            <tr>
                <td class="set">设备名称</td>
                <td>{{isset($arr['device_name'])?$arr['device_name']:''}}</td>
                <td class="set">语言</td>
                <td>{{isset($arr['languages'])?$arr['languages']:''}}</td>
            </tr>

            <tr>
                <td class="set">是否机械人</td>
                <td>{{isset($arr['isRobot'])&&$arr['isRobot']?'是':'否'}}</td>
                <td class="set">机械人名称</td>
                <td>{{isset($arr['Robot_name'])?$arr['Robot_name']:''}}</td>
            </tr>

            </tbody>
        </table>

    </div>
@endsection

@section('script')
    <style>
        .layui-table tr .set {
            background-color: #f2f2f2;
            width: 130px;
        }
        /*.layui-table tr td{
            word-wrap: break-word;
            overflow: hidden;
            text-overflow:ellipsis;
            white-space: nowrap;
        }*/
        .layui-table #showview{
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
    <script>
        layui.use(['jquery','layer'],function () {
            var $ = layui.$,layer = layui.layer;
            $("#showview").on('click',function () {
                var text = $(this).text();

                var html = '<pre class="layui-code"><code>' + '\n'
                    + JSON.stringify(JSON.parse(text),null,2) + '\n'
                    + '</pre></code>';

                layer.open({
                    title:'code'
                    ,type: 1
                    ,anim: 2
                    ,shadeClose: true
                    ,skin: 'layui-layer-rim', //加上边框
                    area: ['70%', '70%'], //宽高
                    content: html
                });
            });
        });

    </script>
@endsection
