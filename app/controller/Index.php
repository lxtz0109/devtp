<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
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


    public function suiji(){

        echo date("Y-m-d H:i:s")."<br>";
        $sum = 0;
        $i = 1;
        while ($i < 31){
            $rs  = rand(1,60000);
            echo("6月".$i."号收入数据：".$rs."<br>");
            $i++;
            $sum += $rs;
        }
        echo "6月总收入为：".$sum."<br>";




    }

    public function shichen(){
        $shicheng = ["子时","丑时","寅时","卯时","辰时","巳时","午时","未时","申时","酉时","戌时","亥时",];
        // $arr = [110,111,112,113,114,115,116];
        $arr = [117,118,119,120,121,122,123];
        for ($i = 0; $i < count($arr); $i++) {

            for ($j = 0; $j < count($shicheng); $j++) {
                $data['shichen'] = $shicheng[$j];
                $data['relation_id'] = $arr[$i];
                $data['datetime'] =date("Y-m-d H:i:s");

                $result = Db::name('desmond_happy_detail')->insert($data);
                echo "done succ".date("Y-m-d H:i:s")."<br>";
                echo $result."<br>";
            }

        }
    }


}
