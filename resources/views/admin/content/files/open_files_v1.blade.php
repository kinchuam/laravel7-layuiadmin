@extends('admin.base')

@section('content')
    <!-- 文件库模板 -->
    <div class="file-library"></div>
@endsection

@section('script')
    <script>
        layui.use(['Library'],function () {
            layui.Library.render({
                elem: '.file-library',
                FileUpload: '{{route('admin.FileUpload')}}',
                FileList: '{{route('admin.content.files.data')}}',
                DeleteFiles: '{{route('admin.content.files.destroy')}}',
                MoveFiles: '{{route('admin.content.files_group.moveFiles')}}',
                GroupList: '{!! json_encode($group_list) !!}',
                AddGroup: '{{route('admin.content.files_group.store')}}',
                EditGroup: '{{route('admin.content.files_group.update')}}',
                DeleteGroup: '{{route('admin.content.files_group.destroy')}}',
            });
        });
    </script>
@endsection
