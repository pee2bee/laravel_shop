<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('title', __('标题'));

        $grid->on_sale('是否在售')->display(function ($value) {
            return $value=== 1 ? '在售' : '已下架';
        });

        $grid->column('rating','评分');
        $grid->column('sold_count', __('销量'));
        $grid->column('view_count', __('浏览量'));
        $grid->column('price','价格');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', '标题');
        $show->field('image','封面')->image();
        $show->field('description','描述');
        $show->field('rating', '评分');
        $show->field('sold_count', '销量');
        $show->field('view_count','浏览量');
        $show->field('price','价格');
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product());

        $form->text('title', '标题')->rules('required');
        $form->quill('description', __('描述'))->rules('required');
        $form->image('image','封面')->rules('required|image');
        $form->radio('on_sale', __('上架'))->options(['1'=>'是','0'=>'否'])->default(1)->rules('required');
        $form->hasMany('productSkus', '添加sku', function (Form\NestedForm $form) {
            $form->text('title','sku标题');
            $form->text('description','描述');
            $form->text('price','单价')->rules('required|numeric|min:0.01');
            $form->text('stock','剩余库存')->rules('required|integer|min:0');
        });
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('productSkus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });




        return $form;
    }
}
