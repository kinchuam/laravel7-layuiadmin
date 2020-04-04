{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">分类</label>
    <div class="layui-input-inline" style="width: 55%;">
        <select name="category_id" lay-verify="required">
            <option value=""></option>
            @foreach($categorys as $category)
                <option value="{{ $category->id }}" @if(isset($item->category_id) && $item->category_id == $category->id) selected @endif >{{ $category->name }}</option>
                @if(isset($category->allChilds) && !$category->allChilds->isEmpty())
                    @foreach($category->allChilds as $child)
                        <option value="{{ $child->id }}" @if(isset($item->category_id) && $item->category_id == $child->id) selected @endif >　┗━━ {{ $child->name }}</option>
                        @if(isset($child->allChilds) && !$child->allChilds->isEmpty())
                            @foreach($child->allChilds as $third)
                                <option value="{{ $third->id }}" @if(isset($item->category_id) && $item->category_id == $third->id) selected @endif >　　┗━━ {{ $third->name }}</option>
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">标题</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" name="title" value="{{isset($item->title) ? $item->title : old('title')}}" lay-verify="required" placeholder="请输入标题" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">作者</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" name="author" value="{{isset($item->author) ? $item->author: old('author')}}" placeholder="请输入作者" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">关键词</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" name="keywords" value="{{isset($item->keywords) ?$item->keywords: old('keywords')}}" placeholder="请输入关键词" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">描述</label>
    <div class="layui-input-inline" style="width: 55%;">
        <textarea name="description" placeholder="请输入描述" class="layui-textarea">{{isset($item->description) ?$item->description: old('description')}}</textarea>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">标签</label>
    <div class="layui-input-block" style="width: 55%;">
        <div class="tags" id="tags">
            <input type="text" id="keywords" placeholder="空格生成标签" class="layui-input" style="height: auto;">
        </div>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">缩略图</label>
    <div class="layui-input-inline" style="width: 70%;">
        {!! tpl_form_field_image('thumb', isset($item->thumb)?$item->thumb:'') !!}
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">内容</label>
    <div class="layui-input-inline" style="width: 70%;">
        <textarea name="content" id="content" class="layui-textarea">{!! isset($item['content'])?$item['content']:'' !!}</textarea>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">排序</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" name="sort" value="{{ isset($item->sort)?$item->sort: 0 }}"  placeholder="请输入数字" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">类型</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="checkbox" name="ishome" value="1" title="首页文章" {{ isset($item->ishome) && $item->ishome==1 ? 'checked' : '' }}>
        <input type="checkbox" name="ishelp" value="1" title="帮助文章" {{ isset($item->ishelp) && $item->ishelp==1 ? 'checked' : '' }}>
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">添加时间</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" class="layui-input" id="created_at" value="{{isset($item->created_at) ?$item->created_at: date('Y-m-d H:i:s') }}" name="created_at" readonly="" placeholder="yyyy-MM-dd HH:mm:ss">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">点击量</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="number" name="click" value="{{isset($item->click) ?$item->click: 0}}" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="checkbox" name="status"  value="1" lay-skin="switch" lay-text="已发布|待修改" @if(isset($item->status)&&$item->status==1) checked @endif>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block layui-hide">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
    </div>
</div>
