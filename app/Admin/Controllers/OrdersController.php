<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrdersController extends AdminController {
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Order';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid( new Order() );

        $grid->model()->orderBy( 'created_at', 'desc' );

        $grid->column( 'id', __( 'Id' ) );
        $grid->column( 'no', __( '订单号' ) );
        $grid->column( 'user.name', __( '买家' ) );

        $grid->column( 'total_amount', __( '总金额' ) );
        $grid->column( 'remark', __( '备注' ) );
        $grid->column( 'paid_at', __( '支付时间' ) )->display( function ( $value ) {
            $date = new Carbon( $value );

            return $date->format( 'Y-m-s H:i' );
        } );
        $grid->column( 'payment_method', __( '支付方式' ) );
        $grid->column( 'payment_no', __( '支付流水号' ) );
        $grid->ship_status( '物流状态' )->display( function ( $value ) {
            return Order::$shipStatusMap[ $value ];
        } );
        $grid->refund_status( '退款状态' )->display( function ( $value ) {
            return Order::$refundStatusMap[ $value ];
        } );
        $grid->column( 'refund_no', __( 'Refund no' ) );
        $grid->closed( '订单状态' )->display( function ( $value ) {
            return $value ? '已关闭' : '';
        } );
        $grid->reviewed( '评价' )->display( function ( $value ) {
            return $value ? '已评价' : '待评价';
        } );

        return $grid;
    }


    public function show( $id, Content $content ) {
        return $content
            ->header( '查看订单' )
            // body 方法可以接受 Laravel 的视图作为参数
            ->body( view( 'admin.orders.show', [ 'order' => Order::find( $id ) ] ) );
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form( new Order() );

        $form->text( 'no', __( 'No' ) );
        $form->number( 'user_id', __( 'User id' ) );
        $form->textarea( 'address', __( 'Address' ) );
        $form->decimal( 'total_amount', __( 'Total amount' ) );
        $form->textarea( 'remark', __( 'Remark' ) );
        $form->datetime( 'paid_at', __( 'Paid at' ) )->default( date( 'Y-m-d H:i:s' ) );
        $form->text( 'payment_method', __( 'Payment method' ) );
        $form->text( 'payment_no', __( 'Payment no' ) );
        $form->text( 'refund_status', __( 'Refund status' ) )->default( 'pending' );
        $form->text( 'refund_no', __( 'Refund no' ) );
        $form->switch( 'closed', __( 'Closed' ) );
        $form->switch( 'reviewed', __( 'Reviewed' ) );
        $form->text( 'ship_status', __( 'Ship status' ) )->default( 'pending' );
        $form->textarea( 'ship_data', __( 'Ship data' ) );
        $form->textarea( 'extra', __( 'Extra' ) );

        return $form;
    }
}
