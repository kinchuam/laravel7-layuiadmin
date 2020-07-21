@extends('admin.base')

@section('content')
    <link rel="stylesheet" href="{{ asset('static/common/style/template.css') }}" media="all">
    <style>
        .layui-serachlist-cover img {
            width: 155px;
            height: auto;
        }
    </style>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card layadmin-serach-main">
                <div class="layui-card-header">
                    <p style="font-size: 18px;">
                        <span style="color: #01AAED">{{request()->get('keywords')}}</span> 查询到
                        <strong>0</strong> 个结果
                    </p>
                    <p class="layadmin-font-em">耗时：<span>0</span>ms</p>
                </div>
                <div class="layui-card-body">

                    <ul class="layadmin-serach-list layui-text"></ul>
                    <div id="LAY-template-search-page" style="text-align: center;"></div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @verbatim
        <script type="text/html" id="sreachtpl">
            {{#  layui.each(d.data, function(index, item){ }}
            <li>
                {{#  if(item.thumb){ }}
                <a href="JavaScript:;" class="layui-serachlist-cover">
                    <img src="{{ item.thumb }}" alt="" onerror="this.src='{{ d.nopic }}'">
                </a>
                {{#  } }}
                <div class="layui-serachlist-text">
                    <h3><a href="JavaScript:;">{{ item.title }}</a></h3>
                    <p>{{ item.description }}</p>
                    {{#  if(item.tags){ }}
                    <p>
                        {{#  layui.each(item.tags, function(index1, item1){ }}
                        {{#  var $a = ['layui-bg-green','layui-bg-orange','layui-bg-blue','layui-bg-black', 'layui-bg-cyan', 'layui-bg-red']; function random(lower, upper) { return Math.floor(Math.random() * (upper - lower)) + lower;} }}
                        <span class="layui-badge {{ $a[random(0,$a.length)] }}">{{ item1 }}</span>
                        {{#  }); }}
                    </p>
                    {{#  } }}
                </div>
            </li>
            {{#  }); }}

            {{#  if(d.data.length === 0){ }}
            <div  style="text-align: center;">无数据</div>
            {{#  } }}
        </script>
    @endverbatim

    <script>

        layui.use(['index', 'laypage','laytpl'], function() {
            var keywords = '{{request()->get('keywords')??''}}';
            get_list(keywords);
            function get_list(keywords='',page=1,isupdate=true) {
                var loadindex = layer.load(2);
                var data = {keywords:keywords,page:page};
                var t1 = (new Date()).getTime();
                $.get("{{route('admin.search')}}",data,function (res) {
                    layer.close(loadindex);

                    data.nopic = "{{asset('static/admin/img/nopic.png')}}";
                    data.data = data.data||[];
                    layui.laytpl(sreachtpl.innerHTML).render(data, function(html) {
                        $(".layadmin-serach-list").html(html);
                    });

                    var t2 = (new Date()).getTime();
                    var t3 = t2 - t1;
                    $(".layui-card-header p strong").text(data.total);
                    $(".layui-card-header .layadmin-font-em span").text(t3.toFixed(2));

                    if (isupdate) {
                        layui.laypage.render({
                            elem: 'LAY-template-search-page'
                            ,count: data.total||0
                            ,jump: function(obj, first){
                                if(!first){
                                    get_list(keywords,obj.curr,false);
                                    //layer.msg('第'+ obj.curr +'页');
                                }
                                $("html,body").animate({
                                    scrollTop: 0
                                }, 200)
                            }
                        });
                    }

                })

            }

        });

    </script>
@endsection
