@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.article.update',['id'=>$item->id])}}" method="post">
            {{ method_field('put') }}
            @include('admin.article._form')
        </form>
    </div>
@endsection

@section('script')
    @include('admin.article._js')
@endsection
