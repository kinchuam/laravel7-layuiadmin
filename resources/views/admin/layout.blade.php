<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>{{ isset($website['title'])?$website['title']:'LaravelAdmin' }} - 主页</title>
    <meta name="keywords" content="{{ isset($website['keywords'])?$website['keywords']:'LaravelAdmin' }} - 主页">
    <meta name="description" content="{{ isset($website['description'])?$website['description']:'LaravelAdmin' }} - 主页">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ isset($website['icon'])?$website['icon']: asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('static/common/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/common/style/admin.css') }}" media="all">
    <style>
        body .layui-layout-admin .footer-demo{margin-top:90px;height: 40px; line-height: 40px; padding: 5px 0;}
        .footer{padding: 30px 0; line-height: 30px; text-align: center; color: #666; font-weight: 300;}
        .footer a{padding: 0 5px;}
        .site-union{color: #999;}
        .site-union>*{display: inline-block; vertical-align: middle;}
        .site-union a[sponsor] img{width: 80px;}
        .site-union span{position: relative; top: 5px;}
        .site-union span a{padding: 0; display: inline; color: #999;}
        .site-union span a:hover{text-decoration: underline;}
        .site-union .site-union-desc{display: block; margin-bottom: 10px;}

        .footer-demo p,
        .footer-demo .site-union,
        .footer-demo .site-union p{display: inline-block; vertical-align: middle; padding-right: 10px;}
        .footer-demo .site-union{position: relative; top: -5px;}
        .footer-demo .site-union .site-union-desc{margin-bottom: 0; padding-right: 0;}
        .footer-demo .site-union a[sponsor] img{position: relative; top: 3px;}

    </style>
</head>
<body class="layui-layout-body">

<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="{{route('home')}}" target="_blank" title="前台">
                        <i class="layui-icon layui-icon-website"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <input type="text" placeholder="搜索..." autocomplete="off" class="layui-input layui-input-search" layadmin-event="serach" lay-action="{{route('admin.search',['keywords'=>''])}}">
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="theme">
                        <i class="layui-icon layui-icon-theme"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="fullscreen">
                        <i class="layui-icon layui-icon-screen-full"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite>{{auth('admin')->user()->name}}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a lay-href="{{route('admin.set.index')}}">基本资料</a></dd>
                        <dd><a lay-href="{{route('admin.set.password')}}">修改密码</a></dd>
                        <hr>
                        <dd  style="text-align: center;"><a href="{{route('admin.logout')}}">退出</a></dd>
                    </dl>
                </li>

                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" ><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="{{route('admin.index')}}">
                    <span>{{ isset($website['webname'])?:'LaravelAdmin' }}</span>
                </div>

                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>主页</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="console" class="layui-this">
                                <a lay-href="{{route('admin.index')}}">控制台</a>
                            </dd>
                        </dl>
                    </li>
                    @foreach($menus as $menu)
                        @can($menu->name)
                        <li data-name="{{$menu->name}}" class="layui-nav-item">
                            <a href="javascript:;" lay-tips="{{$menu->display_name}}" lay-direction="2">
                                <i class="layui-icon {{$menu->icon?:''}}"></i>
                                <cite>{{$menu->display_name}}</cite>
                            </a>
                            @if($menu->childs->isNotEmpty())
                            <dl class="layui-nav-child">
                                @foreach($menu->childs as $subMenu)
                                    @can($subMenu->name)
                                    <dd data-name="{{$subMenu->name}}" >
                                        <a lay-href="{{ route($subMenu->route) }}">{{$subMenu->display_name}}</a>
                                    </dd>
                                    @endcan
                                @endforeach
                            </dl>
                            @endif
                        </li>
                        @endcan
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- 页面标签 -->
        <div class="layadmin-pagetabs" id="LAY_app_tabs">
            <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-down">
                <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;"></a>
                        <dl class="layui-nav-child layui-anim-fadein">
                            <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                            <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                            <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
            <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                <ul class="layui-tab-title" id="LAY_app_tabsheader">
                    <li lay-id="{{route('admin.index')}}" lay-attr="{{route('admin.index')}}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>

        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="{{route('admin.index')}}" frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>
        @if(isset($website['copyright']))

        <div class="layui-footer footer footer-demo">
            <div class="layui-main">
                {!! $website['copyright'] !!}
            </div>
        </div>
        <style> .layui-layout-admin .layui-body{bottom: 50px;} </style>
        @endif
        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>
<script src="https://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script src="{{ asset('static/common/layui/layui.js') }}"></script>

<script>
    layui.config({
        base: "{{ asset('static/common').'/' }}" //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use('index');

</script>
</body>
</html>


