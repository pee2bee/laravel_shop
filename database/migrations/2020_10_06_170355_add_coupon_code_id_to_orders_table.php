<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCouponCodeIdToOrdersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'orders', function ( Blueprint $table ) {
            //
            $table->unsignedBigInteger( 'coupon_code_id' )->nullable( true )->comment( '使用的优惠券id' );
            //设置外键，关联的优惠券被删除时自动把coupon_code_id设置为null
            $table->foreign( 'coupon_code_id' )->references( 'id' )->on( 'coupons' )->onDelete( 'set null' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'orders', function ( Blueprint $table ) {
            //删除外键关联，再删除列，不然会报错,还有要用数组的方式包含着外键索引
            $table->dropForeign( [ 'coupon_code_id' ] );
            $table->dropColumn( 'coupon_code_id' );

        } );
    }
}
