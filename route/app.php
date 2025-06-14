<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP8!';
});

Route::get('hello/:name', 'index/hello');


Route::get('indexadmin', 'index');
Route::get('phpinfo', 'index/phpinfo');

//适配器模式
Route::get('adapter', 'Pay/index');
//随机红包
Route::get('suiji', 'index/suiji');
//时辰
Route::get('suiji', 'index/shichen');
//定位打卡
Route::get('punch', 'Punch/index');
Route::get('punch/getLocation', 'Punch/getLocation');
Route::post('punch/submit', 'Punch/submit');

//分配固定金额红包到每一天
Route::get('bonus', 'index/distributeBonus');

