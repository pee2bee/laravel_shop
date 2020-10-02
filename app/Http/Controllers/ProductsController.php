<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductsController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //创建一个查询构造器
        $builder = Product::query()->where( 'on_sale', '=', 1 );
        //判断是否有提交search参数
        //这里使用匿名函数来构建子查询 是为了用括号把子查询的条件包起来，不然会查错
        if ( $search = \request( 'search', '' ) ) {
            $like = '%' . $search . '%';
            $builder->where( function ( $query ) use ( $like ) {
                $query->where( 'title', 'like', $like )
                      ->orWhere( 'description', 'like', $like )
                      ->orWhereHas( 'productSkus', function ( $query ) use ( $like ) {
                          $query->where( 'title', 'like', $like )
                                ->orWhere( 'description', 'like', $like );
                      } );
            } );
        }

        // 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if ( $order = \request( 'order', '' ) ) {
            // 是否是以 _asc 或者 _desc 结尾
            if ( preg_match( '/^(.+)_(asc|desc)$/', $order, $m ) ) {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if ( in_array( $m[1], [ 'price', 'sold_count', 'rating' ] ) ) {
                    // 根据传入的排序值来构造排序参数
                    $builder->orderBy( $m[1], $m[2] );
                }
            }
        }


        $products = $builder->paginate( 16 );
        $filters  = [
            'search' => $search,
            'order'  => $order,
        ];

        return view( 'products.index', compact( 'products', 'filters' ) );
    }


    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws InvalidRequestException
     */
    public function show( $id ) {
        //获取商品
        $product = Product::query()->with( 'productSkus' )->find( $id );
        if ( ! $product->on_sale ) {
            throw new InvalidRequestException( '商品未上架' );
        }
        $user    = \request()->user();
        $favored = false;//默认未收藏
        //判断user是否登录，
        //登录就查询是否已收藏该商品
        if ( $user ) {
            //boolval()把值转化成Boolean格式
            $favored = boolval( $user->favoriteProducts()->find( $product->id ) );
        }
        //获取商品评价
        $reviews = OrderItem::query()
                            ->with( 'order.user', 'productSku' )//预加载关联
                            ->where( 'product_id', $product->id )//对应的商品的订单项
                            ->whereNotNull( 'reviewed_at' )//已评价
                            ->orderBy( 'reviewed_at', 'desc' )//评价的时间倒序
                            ->paginate( 10 );//分页10条


        return view( 'products.show', compact( 'product', 'favored', 'reviews' ) );
    }

    /**
     * 收藏商品
     *
     * @param Product $product
     *
     * @return array
     */
    public function favor( Product $product ) {

        $user = \request()->user();
        //已经收藏了
        if ( $user->favoriteProducts()->find( $product->id ) ) {
            //直接返回空
            return [];
        }
        //执行收藏
        $user->favoriteProducts()->attach( $product->id );

        //返回空
        return [];
    }

    public function disfavor( Product $product ) {
        $user = \request()->user();
        $user->favoriteProducts()->detach( $product->id );

        return [];
    }

    public function favorites() {
        $builder = \request()->user()->favoriteProducts();
        //判断是否有提交search参数
        if ( $search = \request( 'search', '' ) ) {
            $like = '%' . $search . '%';
            $builder->where( function ( $query ) use ( $like ) {
                $query->where( 'title', 'like', $like )
                      ->orWhere( 'description', 'like', $like )
                      ->orWhereHas( 'productSkus', function ( $query ) use ( $like ) {
                          $query->where( 'title', 'like', $like )
                                ->orWhere( 'description', 'like', $like );
                      } );
            } );
        }
        $products = $builder->paginate( 3 );
        $filters  = [ 'search' => $search ];
        $total    = \request()->user()->favoriteProducts()->count();

        return view( 'products.favorites', compact( 'products', 'filters', 'total' ) );
    }
}
