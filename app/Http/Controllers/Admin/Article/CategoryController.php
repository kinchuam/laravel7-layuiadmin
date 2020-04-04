<?php

namespace App\Http\Controllers\Admin\Article;

use App\Models\Article\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.article.category.index');
    }

    public function data(Request $request)
    {
        $res = Category::where('parent_id',$request->get('parent_id',0))->orderBy('id','desc')->orderBy('sort','desc')->paginate($request->get('limit',30))->toArray();
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
        $categorys = Category::get()->toArray();
        return view('admin.article.category.create',compact('categorys'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'  => 'required|string',
        ]);
        if (Category::create($request->all())){
            return response()->json([
                'status' => 'success',
                'noRefresh' => false,
                'message' => '添加完成'
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
        $category = Category::findOrFail($id);
        $categorys = Category::get()->toArray();
        return view('admin.article.category.edit',compact('category','categorys'));
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
        $this->validate($request,[
            'name'  => 'required|string',
        ]);
        $category = Category::findOrFail($id);
        if ($category->update($request->all())){
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
        $category = Category::with(['childs','articles'])->find($ids);
        if (!$category){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (!$category->childs->isEmpty() || !$category->articles->isEmpty()){
            return response()->json(['code'=>1,'msg'=>'该分类下有子分类或者文章，不能删除']);
        }
        if ($category->delete()){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
