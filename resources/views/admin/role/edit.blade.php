@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form action="{{route('admin.role.update',['id'=>$role->id])}}" method="post" class="layui-form">
            {{method_field('put')}}
            <input type="hidden" name="id" value="{{$role->id}}">
            @include('admin.role._form')
        </form>
    </div>
@endsection
