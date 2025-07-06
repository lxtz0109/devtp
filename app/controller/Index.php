<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
class Index extends BaseController
{
    public function indexadmin()
    {
        try {
            // 原始字符串
            $string = '10a';


            // 转换为二进制（字符串本身就是二进制的文本表示）
            $binaryData = $this->stringToBinary($string);

            // 写入文件
            $filePath = public_path() . 'storage/a.txt';

            $this->writeToFile($filePath, $binaryData);

            return json([
                'code' => 200,
                'message' => '数据写入成功',
                'file_path' => $filePath,
                'original_string' => $string,
                'binaryData' => $binaryData,
                'binary_size' => strlen($binaryData) . ' 字节'
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }

        echo  phpinfo();exit;

        return '<style>*{ padding: 0; margin: 0; }</style><iframe src="https://www.thinkphp.cn/welcome?version=' . \think\facade\App::version() . '" width="100%" height="100%" frameborder="0" scrolling="auto"></iframe>';
    }

    /**
     * 将字符串转换为二进制格式
     */
    private function stringToBinary($string)
    {

        $bytes = unpack('C*', $string);
        $binary = '';
        foreach ($bytes as $byte) {
            $binary .= " ".str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }


        return $binary;

    }

    /**
     * 写入文件
     */
    private function writeToFile($filePath, $data)
    {
        // 确保目录存在
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // 写入二进制数据（使用FILE_BINARY标志确保二进制安全）
        file_put_contents($filePath, $data,FILE_APPEND );
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
        $year = [2020];
        $month = [1,2,3,4,5,6,7,8,9,10,11,12];

        foreach ($year as $y){
            echo "===========================".$y."年================================="."<br>";
            $total = 0;
            foreach ($month as $m){
                echo "==========================".$y."年".$m."月================================"."<br>";
                if (in_array($m, [1,3,5,7,8,10,12])){
                    $days = 31;
                }else{
                    $days = 30;
                }
                if ($m == 2){
                    if($y%4 == 0){
                        $days = 28;
                    }else{
                        $days = 29;
                    }
                }
                $sum = 0;
                $i = 1;
                while ($i < $days+1){
                    $rs  = rand(1,100000);
                    echo($y."年".$m."月".$i."号收入数据：".$rs."<br>");
                    $i++;
                    $sum += $rs;
                }
                echo $y."年".$m."月总收入为：".$sum."<br>";
                $total += $sum;

            }
            echo($y."年"."收入数据：".$total."<br>");
            echo "<br>";

        }

     /*
      echo date("Y-m-d H:i:s")."<br>";
        $sum = 0;
       $i = 1;
        while ($i < 31){
            $rs  = rand(1,100000);
            echo("月".$i."号收入数据：".$rs."<br>");
            $i++;
            $sum += $rs;
        }
        echo "6月总收入为：".$sum."<br>";*/




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
    public function distributeBonus()
    {
        // 总奖金(单位：元)
        $totalBonus = 3000000;
        // 天数
        $days = 30;
        // 每天最高奖金
        $maxDailyBonus = 200000;

        // 生成一个足够大的候选奖金池
        $candidatePool = [];
        // 为了确保有足够的候选值，我们生成更多的数
        $minBonus = floor($totalBonus / $days / 2); // 假设最小值为平均值的一半

        // 生成候选奖金池，确保每个值都不同
        for ($i = $minBonus; $i <= $maxDailyBonus; $i += 10) { // 以100为步长，确保金额为整数
            $candidatePool[] = $i;
        }

        // 打乱候选池
        shuffle($candidatePool);

        // 从候选池中选择30个不同的奖金值
        $selectedBonuses = [];
        $currentSum = 0;

        // 先选择29个不同的奖金值
        for ($i = 0; $i < $days - 1; $i++) {
            // 确保候选池中还有值
            if (empty($candidatePool)) {
                // 如果候选池为空，需要重新生成
                $candidatePool = $this->regenerateCandidatePool($selectedBonuses, $minBonus, $maxDailyBonus);
                shuffle($candidatePool);
            }

            // 从候选池中取出一个值
            $bonus = array_shift($candidatePool);
            $selectedBonuses[] = $bonus;
            $currentSum += $bonus;
        }

        // 计算最后一天的奖金，确保总和为300万
        $lastDayBonus = $totalBonus - $currentSum;

        // 检查最后一天的奖金是否满足条件
        if ($lastDayBonus <= 0 || $lastDayBonus > $maxDailyBonus || in_array($lastDayBonus, $selectedBonuses)) {
            // 如果不满足条件，需要重新分配
            return $this->distributeBonus(); // 递归调用，直到找到有效分配
        }

        // 添加最后一天的奖金
        $selectedBonuses[] = $lastDayBonus;

        // 再次打乱顺序，使分配更随机
        shuffle($selectedBonuses);

        // 输出每天的奖金
        $result = [];
        $total = 0;
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime('2025-06-01 +' . $i . ' days'));

            $result[] = [
                'date' => $date,
                'bonus' => $selectedBonuses[$i],
                'dates' =>  date('Y-m-d H:i:s',strtotime('2025-06-01 +' . $i . ' days') + rand(0,86400) )

            ];
            $total += $selectedBonuses[$i];
        }

        // 记录到数据库
        foreach ($result as $item) {
            Db::name('bonus_distribution')->insert([
                'date' => $item['date'],
                'amount' => $item['bonus'],
                'created_at' => $item['dates'],
            ]);
        }

        return json([
            'status' => 1,
            'message' => '奖金分配成功',
            'data' => $result,
            'total' => $total
        ]);
    }

    // 重新生成候选奖金池，排除已选择的值
    private function regenerateCandidatePool($selected, $min, $max)
    {
        $pool = [];
        for ($i = $min; $i <= $max; $i += 100) {
            if (!in_array($i, $selected)) {
                $pool[] = $i;
            }
        }
        return $pool;
    }

}
