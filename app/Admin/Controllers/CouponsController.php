<?php

namespace App\Admin\Controllers;

use App\Models\Coupon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CouponsController extends AdminController {
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Coupon';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid( new Coupon() );

        //按照时间倒序
        $grid->model()->orderBy( 'created_at', 'desc' );

        $grid->column( 'id', __( 'Id' ) )->sortable();//可排序
        $grid->column( 'description', __( '描述' ) );
        $grid->column( 'code', __( '优惠码' ) );
        $grid->column( 'type', __( '类型' ) )->display( function ( $value ) {
            return Coupon::$typeMap[ $value ];
        } );
        $grid->column( 'value', __( '折扣' ) );
        $grid->column( 'total', __( '总次数' ) );
        $grid->column( 'used', __( '已使用' ) );
        $grid->column( 'not_before', __( '开始时间' ) );
        $grid->column( 'not_after', __( '过期时间' ) );
        $grid->column( 'enabled', __( '是否启用' ) )->display( function ( $value ) {
            return $value ? '是' : '否';
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
        $show = new Show( Coupon::findOrFail( $id ) );

        $show->field( 'id', __( 'Id' ) );
        $show->field( 'name', __( '名称' ) );
        $show->field( 'code', __( '优惠码' ) );
        $show->field( 'type', __( '类型' ) );
        $show->field( 'value', __( '折扣' ) );
        $show->field( 'total', __( '总次数' ) );
        $show->field( 'used', __( '已使用' ) );
        $show->field( 'min_amount', __( '最低金额' ) );
        $show->field( 'not_before', __( '生效时间' ) );
        $show->field( 'not_after', __( '过期时间' ) );
        $show->field( 'enabled', __( '是否启用' ) );
        $show->field( 'created_at', __( 'Created at' ) );

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form( new Coupon() );

        $form->text( 'name', __( '名称' ) )->rules( 'required' );
        //$form->text( 'code', __( '优惠码' ) )->rules( 'nullable|unique:coupon' );//允许用户不填，系统填充
        $form->radio( 'type', __( '类型' ) )->options( Coupon::$typeMap )->rules( 'required' )
             ->default( Coupon::TYPE_FIXED );
        $form->decimal( 'value', __( '折扣值' ) );
        $form->number( 'total', __( '可用次数' ) );
        $form->decimal( 'min_amount', __( '最低使用金额' ) );
        $form->datetime( 'not_before', __( '开始时间' ) )->default( date( 'Y-m-d H:i:s' ) );
        $form->datetime( 'not_after', __( '结束时间' ) )->default( date( 'Y-m-d H:i:s' ) );
        $form->switch( 'enabled', __( '是否启用' ) )->default( 1 );

        $form->hidden( 'code' );
        $form->saving( function ( Form $form ) {
            /*//如果不填写优惠码，系统自动生成
            if ( ! $form->code ) {
                $form->code = Coupon::createCouponCode();
            }*/
            $form->code = Coupon::createCouponCode();
        } );

        return $form;
    }
}
