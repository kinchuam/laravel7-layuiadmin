@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15" style="background-color: #fff;">

        <div class="layui-col-sm5">
            <fieldset class="layui-elem-field ">
                <legend><a name="default">环境</a></legend>
                <div class="layui-field-box">
                    <table class="layui-table" lay-skin="line">
                        <tbody>
                        @foreach($envs as $env)
                            <tr>
                                <td>{{ $env['name'] }}</td>
                                <td>
                                    {{ $env['value'] }}
                                    @if($env['value']=='redis')
                                        <span style="margin-left: 10px;color: #ff5661">{{$extras[$env['value']]['extra']}}</span>
                                    @elseif(isset($env['type'])&&$env['type']=='php')
                                        <span style="margin-left: 10px;color: #ff5661">{{$extras['php']['extra']}}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="layui-col-sm4">
            <fieldset class="layui-elem-field ">
                <legend><a name="default">依赖</a></legend>
                <div class="layui-field-box">
                    <table class="layui-table" lay-skin="line">
                        <tbody>
                        @foreach($dependencies as $key => $val)
                            <tr>
                                <td>{{$key}}</td>
                                <td><span class="layui-btn layui-bg-blue layui-btn-xs">{{$val}}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="layui-col-sm3">
            <fieldset class="layui-elem-field ">
                <legend><a name="default">依赖-DEV</a></legend>
                <div class="layui-field-box">
                    <table class="layui-table" lay-skin="line">
                        <tbody>
                        @foreach($dependencie_desvs as $key => $val)
                            <tr>
                                <td>{{$key}}</td>
                                <td><span class="layui-btn layui-bg-blue layui-btn-xs">{{$val}}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

    </div>
@endsection

