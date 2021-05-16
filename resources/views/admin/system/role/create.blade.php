@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form action="{{route('admin.role.store')}}" method="post" class="layui-form layui-form-pane" lay-filter="example">
            @include('admin.system.role._form')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.system.role._js')
@endsection
