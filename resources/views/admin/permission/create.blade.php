@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.permission.store')}}" method="post">
            @include('admin.permission._from')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.permission._js')
@endsection