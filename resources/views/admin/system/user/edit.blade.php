@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" action="{{ route('admin.user.update',['id' => $user['id']]) }}" method="post" lay-filter="example">
            <input type="hidden" name="id" value="{{ $user['id'] }}">
            {{ method_field('put') }}
            @include('admin.system.user._form')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.system.user._js')
@endsection
