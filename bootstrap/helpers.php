<?php
/**
 *
 * @author woojuan
 * @email woojuan163@163.com
 * @copyright GPL
 * @version
 */
use Illuminate\Support\Facades\Route;

function test_helper() {
    return 'OK';
}

/**
 * 返回当前路由名称代表的类名（把点转为-，用作前端类样式）
 * @return mixed
 *
 */
function route_class() {
    return str_replace('.', '-',Route::currentRouteName());
}
