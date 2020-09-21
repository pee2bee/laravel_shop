<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false)->comment('sku标题');
            $table->text('description')->nullable(false)->comment('sku描述');
            $table->decimal('price',10,2)->nullable(false)->comment('sku价格');
            $table->integer('stock')->unsigned()->nullable(false)->comment('sku库存');
            $table->unsignedBigInteger('product_id')->unsigned()->nullable(false)->comment('所属商品id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('product_skus');

    }
}
