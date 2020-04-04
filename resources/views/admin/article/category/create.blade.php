@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.article.category.store')}}" method="post">
            @include('admin.article.category._form')
        </form>
    </div>
@endsection

