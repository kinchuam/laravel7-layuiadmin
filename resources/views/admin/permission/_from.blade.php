{{csrf_field()}}

<div class="layui-form-item">
    <label for="" class="layui-form-label">父级</label>
    <div class="layui-input-inline" style="width:65%;">
        <div id="category" data-parent_id="{{isset($permission->parent_id)?$permission->parent_id:request()->get("parentid",0)}}"></div>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block" style="width: 55%;">
        <input type="text" name="name" value="{{isset($permission->name)?$permission->name:old('name')}}" lay-verify="required" lay-vertype="tips" class="layui-input" placeholder="如：system.index">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">显示名称</label>
    <div class="layui-input-block" style="width: 55%;">
        <input type="text" name="display_name" value="{{isset($permission->display_name)?$permission->display_name:old('display_name')}}" lay-verify="required" lay-vertype="tips" class="layui-input" placeholder="如：系统管理">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">路由</label>
    <div class="layui-input-block" style="width: 55%;">
        <input class="layui-input" type="text" name="route" value="{{isset($permission->route)?$permission->route:old('route')}}" placeholder="如：admin.member" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">图标</label>
    <div class="layui-input-block">
        <input type="text" name="icon" lay-filter="icon" id="icon" value="{{isset($permission->icon)?$permission->icon:'layui-icon-set-fill'}}" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block layui-hide">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
    </div>
</div>

