@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15">

        <div class="layui-col-md8">
            <div class="layui-row layui-col-space15">

                <div class="layui-col-md6">
                    <div class="layui-card b2">
                        <div class="layui-card-header">快捷方式</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-shortcut">
                                <div carousel-item>
                                    @foreach($data['shortcut'] as $shortcut)
                                        <ul class="layui-row layui-col-space10">
                                            @foreach($shortcut as $dd)
                                                <li class="layui-col-xs3">
                                                    <a @if(!empty($dd['url'])) lay-href="{{$dd['url']}}" @endif>
                                                        <i class="layui-icon {{$dd['icon']}}"></i>
                                                        <cite>{{$dd['title']}}</cite>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">统计数</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-backlog" data-autoplay="true" data-interval="5000">
                                <div carousel-item>
                                    @foreach($data['data_counts'] as $count)
                                        <ul class="layui-row layui-col-space10">
                                            @foreach($count as $cc)
                                                <li class="layui-col-xs6">
                                                    <a @if(!empty($cc['url'])) lay-href="{{$cc['url']?:''}}" @endif class="layadmin-backlog-body">
                                                        <h3>{{$cc['title']}}</h3>
                                                        <p><cite>{{$cc['count']?:0}}</cite></p>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">数据概览</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview" data-height="500px">
                                <div carousel-item id="LAY-index-dataview">
                                    <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="layui-col-md4">

            <div class="layui-card">
                <div class="layui-card-header">用户信息</div>
                <div class="layui-card-body">
                    <div class="admin_user">
                        <div class="admin_user_rght">
                            <div class="headimg">
                                <i></i>
                                <a href="JavaScript:;" data-icon="fa-user" data-title="修改信息" class="new_tab"><img src="{{ asset('static/admin/img/default_headimg.png') }}" alt=""></a>
                            </div>
                            <div class="welcome en-font">
                                您好！<span id="welcome-span">{{ $user['name'] }}</span>
                                <a href="{{route('admin.logout')}}"><i style="color: red;" class="fa fa-sign-out" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="admin_user_left">
                            <ul class="list">
                                <li>账号：<span class="c">{{ $user['username'] }}</span></li>
                                <li>地址：<span class="c">{{ $user['ip'] }}</span></li>
                                <li>时间：<span class="c">{{ $user['created_at'] }}</span></li>
                            </ul>
                            <div class="user_link layui-btn-group">
                                <a lay-href="{{route('admin.basic.index')}}" class="layui-btn layui-btn-primary new_tab" data-icon="layui-icon-chart-screen">个人信息</a>
                                @can('system.user')
                                <a lay-href="{{route('admin.loginLog')}}" class="layui-btn layui-btn-primary new_tab" data-icon="layui-icon-chart-screen">登录日志</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">
                    当前时间
                    <i class="layui-icon layui-icon-tips" lay-tips="要支持的噢" lay-offset="5"></i>
                </div>
                <div class="layui-card-body layui-text layadmin-text">
                    <blockquote class="layui-elem-quote layui-bg-green">
                        <div id="nowTime"></div>
                    </blockquote>
                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">版本信息</div>
                <div class="layui-card-body layui-text">
                    <table class="layui-table">
                        <tbody>
                        @foreach($data['widget_config'] as $key => $widget)
                            @if($key%2==0)
                                <tr style="background-color: #f2f2f2;">
                                    <th>{{$widget[0]}}</th>
                                    <th>{{$widget[1]}}</th>
                                    <th>{{$widget[2]}}</th>
                                </tr>
                            @else
                                <tr>
                                    <td>{{$widget[0]}}</td>
                                    <td>{{$widget[1]}}</td>
                                    <td>{{$widget[2]}}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

@endsection

@section('script')
    <script>
        //获取系统时间
        getLangDate();
        layui.use(['index', "admin", "carousel", 'echarts', "echartsTheme" ,'element'],function () {
            let $ = layui.$,
                g = layui.carousel,
                t = layui.admin,
                s = layui.device(),
                echarts = layui.echarts,
                r = $("#LAY-index-dataview").children("div");

            $(".layadmin-carousel").each(function () {
                let a = $(this);
                g.render({
                    elem: this,
                    width: "100%",
                    arrow: "none",
                    interval: a.data("interval"),
                    autoplay: a.data("autoplay") === !0,
                    trigger: s.ios || s.android ? "click" : "hover",
                    anim: a.data("anim")
                })
            });
            get_line_chart();

            function get_line_chart() {
                $.get("{{route('admin.line_chart')}}", {}, function (result) {
                    if (result.code === 0) {
                        setData(result.data);
                    }
                });
            }

            function setData(data) {
                let l = []
                    , n = [{
                    title: { text: data.platform.title },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'cross',
                            label: { backgroundColor: '#6a7985' }
                        }
                    },
                    legend: { data: ['PV', 'UV'] },
                    toolbox: {
                        feature: { saveAsImage: {} }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: [{
                        type: 'category',
                        boundaryGap: false,
                        data: data.platform.keys
                    }],
                    yAxis: [{ type: 'value' }],
                    series: [
                        {
                            name: 'PV',
                            type: 'line',
                            stack: '总量',
                            areaStyle: {},
                            data: data.platform.pv
                        },
                        {
                            name: 'UV',
                            type: 'line',
                            stack: '总量',
                            areaStyle: {},
                            data: data.platform.uv
                        }
                    ]
                },
                    {
                        title: {
                            text: data.browser.title,
                            x: "center",
                            textStyle: { fontSize: 14 }
                        },
                        tooltip: {
                            trigger: "item",
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            orient: "vertical",
                            x: "left",
                            data: data.browser.keys
                        },
                        series: [{
                            name: "访问来源",
                            type: "pie",
                            radius: "55%",
                            center: ["50%", "50%"],
                            data: data.browser.data
                        }]
                    }]
                    , o = function (e) {l[e] = echarts.init(r[e], layui.echartsTheme);l[e].setOption(n[e]);t.resize(function () { l[e].resize(); });};

                if (r[0]) {
                    let d = 0;o(0);
                    g.on("change(LAY-index-dataview)", function (e) { o(d = e.index); }); t.on("side", function () {  setTimeout(function () { o(d); }, 300);  });t.on("hash(tab)", function () { layui.router().path.join("") || o(d); });
                }
            }

        });
    </script>
@endsection
