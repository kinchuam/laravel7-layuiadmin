@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.permission.update',['id'=>$permission->id])}}" method="post">
            {{method_field('put')}}
            <input type="hidden" name="id" value="{{ $permission->id }}">
            @include('admin.permission._from')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.permission._js')
@endsection