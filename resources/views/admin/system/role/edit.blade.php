@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form action="{{route('admin.role.update', ['id' => $role['id']])}}" method="post" class="layui-form layui-form-pane" lay-filter="example">
            {{method_field('put')}}
            <input type="hidden" name="id" value="{{$role['id']}}">
            @include('admin.system.role._form')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.system.role._js')
@endsection
