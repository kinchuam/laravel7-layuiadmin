@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.article.store')}}" method="post">
            @include('admin.article._form')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.article._js')
@endsection
