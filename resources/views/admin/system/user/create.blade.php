@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" action="{{ route('admin.user.store') }}" method="post" lay-filter="example">
            @include('admin.system.user._form')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.system.user._js')
@endsection
