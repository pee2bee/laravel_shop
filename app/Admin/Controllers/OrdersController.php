<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
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
        $grid->column( 'address', __( '地址' ) );
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

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail( $id ) {
        $show = new Show( Order::findOrFail( $id ) );

        $show->field( 'id', __( 'Id' ) );
        $show->field( 'no', __( 'No' ) );
        $show->field( 'user_id', __( 'User id' ) );
        $show->field( 'address', __( 'Address' ) );
        $show->field( 'total_amount', __( 'Total amount' ) );
        $show->field( 'remark', __( 'Remark' ) );
        $show->field( 'paid_at', __( 'Paid at' ) );
        $show->field( 'payment_method', __( 'Payment method' ) );
        $show->field( 'payment_no', __( 'Payment no' ) );
        $show->field( 'refund_status', __( 'Refund status' ) );
        $show->field( 'refund_no', __( 'Refund no' ) );
        $show->field( 'closed', __( 'Closed' ) );
        $show->field( 'reviewed', __( 'Reviewed' ) );
        $show->field( 'ship_status', __( 'Ship status' ) );
        $show->field( 'ship_data', __( 'Ship data' ) );
        $show->field( 'extra', __( 'Extra' ) );
        $show->field( 'created_at', __( 'Created at' ) );
        $show->field( 'updated_at', __( 'Updated at' ) );

        return $show;
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
