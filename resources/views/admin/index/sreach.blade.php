@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card layadmin-serach-main">
                <div class="layui-card-header">
                    <p style="font-size: 18px;">
                        <span style="color: #01AAED">{{request()->get('keywords')}}</span> 查询到
                        <strong>{{$list['total']??0}}</strong> 个结果
                    </p>
                    <p class="layadmin-font-em">耗时：{{round($time,2)??0}}ms</p>
                </div>
                <div class="layui-card-body">

                    <ul class="layadmin-serach-list layui-text">
                        @foreach($list['data'] as $row)
                        <li>
                            @if(!empty($row['thumb']))
                                <a href="JavaScript:;" class="layui-serachlist-cover">
                                    <img src="{{tomedia($row['thumb'])}}" alt="">
                                </a>
                            @endif
                            <div class="layui-serachlist-text">
                                <h3><a href="JavaScript:;">{{$row['title']??''}}</a></h3>
                                <p>{{$row['description']??''}}</p>
                                @if(!empty($row['tags'])&&is_array($row['tags']))
                                <p>
                                    @foreach($row['tags'] as $t)
                                    <span class="layui-badge layui-bg-green">{{$t}}</span>
                                    @endforeach
                                    <!--<span class="layui-badge layui-bg-blue">性别</span>
                                    <span class="layui-badge layui-bg-orange">谜</span>-->
                                </p>
                                @endif
                            </div>
                        </li>
                        @endforeach

                    </ul>
                    <div id="LAY-template-search-page" style="text-align: center;"></div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>

        layui.use(['index', 'laypage'], function(){
            var laypage = layui.laypage;

            laypage.render({
                elem: 'LAY-template-search-page'
                ,count: "{{$list['total']??0}}"
                ,jump: function(obj, first){
                    if(!first){
                        layer.msg('第'+ obj.curr +'页');
                    }
                }
            });
        });

    </script>
@endsection
