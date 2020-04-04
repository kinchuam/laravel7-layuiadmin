{{csrf_field()}}

<div class="layui-form-item">
    <label  class="layui-form-label">名称</label>
    <div class="layui-input-inline" style="width:55%;">
        <input type="text" name="name" value="{{isset($category->name) ? $category->name: old('name') }}" lay-verify="required" placeholder="请输入名称" class="layui-input" >
    </div>
</div>
<div class="layui-form-item">
    <label  class="layui-form-label">排序</label>
    <div class="layui-input-inline" style="width:55%;">
        <input type="text" name="sort" value="{{isset($category->sort) ?$category->sort: 0 }}"  placeholder="请输入排序" class="layui-input" >
    </div>
</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
    </div>
</div>