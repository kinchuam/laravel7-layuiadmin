@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" action="{{route('admin.permission.update', ['id' => $permission['id']])}}" method="post" lay-filter="example">
            {{method_field('put')}}
            <input type="hidden" name="id" value="{{$permission['id']}}">
            @include('admin.system.permission._from')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.system.permission._js')
@endsection
