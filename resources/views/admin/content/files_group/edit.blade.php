@extends('admin.from')

@section('content')
<div class="layui-card-body">
    <form class="layui-form layui-form-pane" action="{{ route('admin.content.files_group.update',['id' => $item['id']]) }}" method="post" lay-filter="example">
        {{ method_field('put') }}
        @include('admin.content.files_group._form')
    </form>
</div>
@endsection

@section('script')
    @include('admin.content.files_group._js')
@endsection
