<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'coupons', function ( Blueprint $table ) {
            $table->id();
            $table->string( 'name' )->nullable( false )->comment( '优惠券标题' );
            $table->string( 'code' )->nullable( false )->index( 'code' )->comment( '优惠码' );
            $table->string( 'type' )->nullable( false )->comment( '类型，百分比或直接固定金额' );
            $table->decimal( 'value', 10, 2 )->nullable( false )->comment( '折扣值' );
            $table->unsignedInteger( 'total' )->nullable( false )->comment( '次数' );
            $table->unsignedInteger( 'used' )->nullable( false )->default( 0 )->comment( '已使用次数' );
            $table->decimal( 'min_amount', 10, 2 )->nullable( false )->comment( '使用优惠券的最低金额' );
            $table->dateTime( 'not_before' )->nullable( true )->comment( '在此时间之前不能使用' );
            $table->dateTime( 'not_after' )->nullable( true )->comment( '在此之后时间不能使用' );
            $table->boolean( 'enabled' )->default( true )->comment( '优惠券是否生效，0为不生效，1为生效' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'coupons' );
    }
}
