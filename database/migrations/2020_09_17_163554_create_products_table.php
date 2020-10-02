<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'products', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'title' )->nullable( false )->comment( '商品标签' );
            $table->text( 'description' )->nullable( false )->comment( '商品描述' );
            $table->string( 'image' )->nullable( true )->comment( '商品封面' );
            $table->tinyInteger( 'on_sale' )->nullable( false )->default( 1 )->comment( '是否在售' );
            $table->float( 'rating' )->nullable( false )->default( 5 )->comment( '平均评分' );
            $table->integer( 'sold_count' )->default( 0 )->nullable( false )->comment( '销量' );
            $table->integer( 'review_count' )->default( 0 )->nullable( false )->comment( '评价数量' );
            $table->decimal( 'price' )->nullable( true )->comment( 'sku最低价格，搜索用' );

            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'products' );
    }
}
