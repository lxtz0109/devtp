<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;

class Index extends BaseController
{
    public function indexs()
    {
        echo  phpinfo();

        return '<style>*{ padding: 0; margin: 0; }</style><iframe src="https://www.thinkphp.cn/welcome?version=' . \think\facade\App::version() . '" width="100%" height="100%" frameborder="0" scrolling="auto"></iframe>';
    }
    public function index()
    {
        // 设置目标日期（2026年元旦）
        $targetDate = '2026-01-01 00:00:00';
        $targetTimestamp = strtotime($targetDate);

        // 当前时间戳
        $currentTimestamp = time();

        // 计算剩余时间（秒）
        $remainingSeconds = $targetTimestamp - $currentTimestamp;

        // 计算剩余天数、小时、分钟和秒
        if ($remainingSeconds > 0) {
            $data = [
                'days'    => floor($remainingSeconds / 86400),
                'hours'   => floor(($remainingSeconds % 86400) / 3600),
                'minutes' => floor(($remainingSeconds % 3600) / 60),
                'seconds' => $remainingSeconds % 60
            ];
        } else {
            $data = [
                'days'    => 0,
                'hours'   => 0,
                'minutes' => 0,
                'seconds' => 0
            ];
        }

        // 传递数据到视图
        return View::fetch('index', $data);
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }

    public function phpinfo(){
        phpinfo();
    }
}
