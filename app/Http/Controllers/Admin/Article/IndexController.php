<?php

namespace App\Http\Controllers\Admin\Article;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //分类
        $categorys = Article\Category::with('allChilds')->where('parent_id',0)->orderBy('sort','desc')->get();
        return view('admin.article.index',compact('categorys'));
    }

    public function data(Request $request)
    {

        $model = Article::query();
        if (!empty($request->get('recycle',''))){
            $model = $model->onlyTrashed();
        }
        if ($request->get('category_id')){
            $model = $model->where('category_id',$request->get('category_id'));
        }
        if ($request->get('title')){
            $model = $model->search($request->get('title'));
        }
        $res = $model->orderBy('created_at','desc')->with('category')->paginate($request->get('limit',30))->toArray();
        $res['data'] = set_medias($res['data'],'thumb');
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        //分类
        $categorys = Article\Category::with('allChilds')->where('parent_id',0)->orderBy('sort','desc')->get();
        return view('admin.article.create',compact('categorys'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['status'] = isset($data['status'])?$data['status']:0;
        $data['istop'] = isset($data['istop'])?$data['istop']:0;
        $data['content'] = isset($data['content'])?$data['content']:'';

        if (Article::create($data)){
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'message' => '添加成功'
            ]);
        }
        return response()->json([
            'status' => 'fail',
            'message' => '系统错误'
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $item = Article::findOrFail($id);
        //分类
        $categorys = Article\Category::with('allChilds')->where('parent_id',0)->orderBy('sort','desc')->get();
        return view('admin.article.edit',compact('item','categorys'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $data = $request->all();
        $data['status'] = isset($data['status'])?$data['status']:0;
        $data['istop'] = isset($data['istop'])?$data['istop']:0;
        $data['content'] = isset($data['content'])?$data['content']:'';
        if ($article->update($data)){
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'message' => '更新成功'
            ]);
        }
        return response()->json([
            'status' => 'fail',
            'message' => '系统错误'
        ]);
    }

    public function status(Request $request)
    {
        $data = $request->only(['id','status']);
        $item = Article::findOrFail($data['id']);

        if ($item->update($data)){
            return response()->json(['code' => 0, 'message' => '更新成功']);
        }
        return response()->json(['code' => 1, 'message' => '系统错误']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Article::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    public function recycle()
    {
        //分类
        $categorys = Article\Category::with('allChilds')->where('parent_id',0)->orderBy('sort','desc')->get();
        return view('admin.article.recycle',compact('categorys'));
    }

    public function recover(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择恢复项']);
        }
        if (Article::onlyTrashed()->whereIn('id',$ids)->restore()){
            return response()->json(['code'=>0,'msg'=>'恢复成功']);
        }
        return response()->json(['code'=>1,'msg'=>'恢复失败']);
    }

    public function expurgate(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Article::onlyTrashed()->whereIn('id',$ids)->forceDelete()){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
