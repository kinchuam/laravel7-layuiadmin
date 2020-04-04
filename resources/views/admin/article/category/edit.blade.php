@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" action="{{route('admin.article.category.update',['id' => $category->id])}}" method="post">
            {{ method_field('put') }}
            @include('admin.article.category._form')
        </form>
    </div>
@endsection
