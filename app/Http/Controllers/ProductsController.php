<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //创建一个查询构造器
        $builder = Product::query()->where('on_sale','=',1);
        /*判断是否有提交search参数*/
        if ($search = \request('search','')) {
            $like = '%'.$search.'%';
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like)
                      ->orWhereHas('productSkus', function ($query) use ($like) {
                          $query->where('title', 'like', $like)
                                ->orWhere('description', 'like', $like);
                      });
            });
        }

        // 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if ($order = \request('order','')) {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 根据传入的排序值来构造排序参数
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate(16);
        $filters = [
        'search' => $search,
        'order'  => $order,
    ];
        return view('products.index',compact('products','filters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $product = Product::query()->with('productSkus')->find($id);
        if (! $product->on_sale){
            throw new \Exception('商品未上架');
        }

        return view('products.show',compact('product'));
    }


}
