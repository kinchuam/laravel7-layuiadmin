@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form action="{{route('admin.role.store')}}" method="post" class="layui-form layui-form-pane">
            @include('admin.role._form')
        </form>
    </div>
@endsection
