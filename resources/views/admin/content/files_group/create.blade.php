@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" action="{{ route('admin.content.files_group.store') }}" method="post" lay-filter="example">
            @include('admin.content.files_group._form')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.content.files_group._js')
@endsection
