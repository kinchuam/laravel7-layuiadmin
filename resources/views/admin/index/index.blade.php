@extends('admin.base')

@section('content')
    <style>
        .layadmin-dataview{
            height: 380px !important;
        }
    </style>
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
                                您好！<span>{{ auth('admin')->user()->name?:'' }}</span>
                                <a href="{{route('admin.logout')}}"><i style="color: red;" class="fa fa-sign-out" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="admin_user_left">
                            <ul class="list">
                                <li>账号：<span class="c">{{ auth('admin')->user()->username??'' }}</span></li>
                                <li>地址：<span class="c">{{ $loginlog->ip ?? ''}}</span></li>
                                <li>时间：<span class="c">{{ $loginlog->created_at ?? '' }}</span></li>
                            </ul>
                            <div class="user_link layui-btn-group">
                                <a lay-href="{{route('admin.set.index')}}" class="layui-btn layui-btn-primary new_tab" data-icon="layui-icon-chart-screen">个人信息</a>
                                @can('system.user')
                                <a lay-href="{{route('admin.user.loginlog')}}" class="layui-btn layui-btn-primary new_tab" data-icon="layui-icon-chart-screen">登录日志</a>
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

            @if(config('custom.PUSH_MESSAGE_STATUS'))
            <div class="layui-card">
                <div class="layui-card-header">实时监控</div>
                <div class="layui-card-body layadmin-takerates">
                    <div class="layui-progress" lay-showPercent="yes" lay-filter="cpu_usage">
                        <h3>CPU使用率</h3>
                        <div class="layui-progress-bar" lay-percent="0%"></div>
                    </div>
                    <div class="layui-progress" lay-showPercent="yes" lay-filter="percent_used">
                        <h3>内存占用率</h3>
                        <div class="layui-progress-bar layui-bg-red" lay-percent="0%"></div>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>

@endsection

@section('script')
    <script>
        //获取系统时间
        getLangDate();
        function getLangDate() {
            //值小于10时，在前面补0
            function dateFilter(date) {
                if (date < 10) { return "0" + date; }
                return date;
            }
            var dateObj = new Date()
                ,year = dateObj.getFullYear()
                ,month = dateObj.getMonth() + 1
                ,date = dateObj.getDate()
                ,day = dateObj.getDay()
                ,weeks = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"]
                ,week = weeks[day]
                ,hour = dateObj.getHours()
                ,minute = dateObj.getMinutes()
                ,second = dateObj.getSeconds()
                ,timeValue = "" + ((hour >= 12) ? (hour >= 18) ? "晚上" : "下午" : "上午");
            var newDate = dateFilter(year) + "年" + dateFilter(month) + "月" + dateFilter(date) + "日 " + " " + dateFilter(hour) + ":" + dateFilter(minute) + ":" + dateFilter(second);
            document.getElementById("nowTime").innerHTML = "亲爱的{{ !empty(auth('admin')->user())?auth('admin')->user()->name:'' }}，" + timeValue + "好！<br/> " + newDate + "　" + week;
            setTimeout("getLangDate()", 1000);
        }
        layui.use(['index', "admin", "carousel", "echarts" ,'element'],function () {
            var g = layui.carousel,
                e = layui.$,
                t = layui.admin,
                i = layui.echarts,
                s = layui.device(),
                r = e("#LAY-index-dataview").children("div"),
                element = layui.element,
                l = [];

            e(".layadmin-carousel").each(function () {
                var a = e(this);
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
            element.render("progress");
            get_line_chart();

            function get_line_chart() {
                $.get("{{ route('admin.line_chart') }}",{},function (result) {
                    if (result.code===0)
                    {
                        var data1 = result.data1,data2 = result.data2,series = [];
                        for(let i in data1.names){
                            series.push({
                                name: data1.names[i],
                                type: 'line',
                                stack: '总量',
                                areaStyle: {},
                                data: data1.acounts[i]
                            })
                        }

                         var n = [{title:{text:data1.charttitle},tooltip:{trigger:'axis',axisPointer:{type:'cross',label:{backgroundColor:'#6a7985'}}},legend:{data:data1.names},toolbox:{feature:{saveAsImage:{}}},grid:{left:'3%',right:'4%',bottom:'3%',containLabel:true},xAxis:[{type:'category',boundaryGap:false,data:data1.dates}],yAxis:[{type:'value'}],series:series},{title:{text:data2.charttitle,x:"center",textStyle:{fontSize:14}},tooltip:{trigger:"item",formatter:"{a} <br/>{b} : {c} ({d}%)"},legend:{orient:"vertical",x:"left",data:data2.names},series:[{name:"访问来源",type:"pie",radius:"55%",center:["50%","50%"],data:data2.datas}]}]
                             , o = function (e) {
                                    l[e] = i.init(r[e]), l[e].setOption(n[e]), t.resize(function () {
                                        l[e].resize();
                                    })
                                };
                        //layui.echartsTheme
                        if (r[0]) {
                            o(0); var d = 0;
                            g.on("change(LAY-index-dataview)", function (e) {
                                o(d = e.index);
                            }), layui.admin.on("side", function () {
                                setTimeout(function () {
                                    o(d);
                                }, 300);
                            }), layui.admin.on("hash(tab)", function () {
                                layui.router().path.join("") || o(d);
                            });
                        }
                    }
                });
            }

            //心跳检测
            var heartCheck = {
                timeout: 5000,//60秒
                timeoutObj: null,
                reset: function(){
                    clearInterval(this.timeoutObj);
                    return this;
                },
                start: function(){
                    this.timeoutObj = setInterval(function(){
                        //这里发送一个心跳，后端收到后，返回一个心跳消息，
                        ws.send('{"type": "systeminfo"}');
                    }, this.timeout)
                }
            };
            @if(config('custom.PUSH_MESSAGE_STATUS'))
            // 连接服务端
            ws = new WebSocket("{{config('custom.PUSH_MESSAGE_INFO')}}");
            // 连接后登录
            ws.onopen = function() {
                ws.send('{"type": "systeminfo"}');
                heartCheck.start();
            };
            // 后端推送来消息时
            ws.onmessage = function(e) {
                var data = JSON.parse(e.data);
                if(data){
                    element.progress('cpu_usage', data.cpu_usage+'%');
                    element.progress('percent_used', data.percent_used+'%');

                    if (data.cpu_usage>55) {
                        $(".layadmin-takerates").find('div[lay-filter=cpu_usage] div').addClass('layui-bg-red');
                    }else{
                        $(".layadmin-takerates").find('div[lay-filter=cpu_usage] div').removeClass('layui-bg-red');
                    }

                    if (data.percent_used>55) {
                        $(".layadmin-takerates").find('div[lay-filter=percent_used] div').addClass('layui-bg-red');
                    }else{
                        $(".layadmin-takerates").find('div[lay-filter=percent_used] div').removeClass('layui-bg-red');
                    }
                }
            };
            @endif
        });
    </script>
@endsection
