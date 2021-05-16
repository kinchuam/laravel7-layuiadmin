@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" action="{{route('admin.permission.store')}}" method="post" lay-filter="example">
            @include('admin.system.permission._from')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.system.permission._js')
@endsection
