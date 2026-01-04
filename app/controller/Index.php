<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use app\model\LeagueGame;
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
        $arr = [236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255,256,257,258,259];
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

    /**
     * SSE 数据推送接口
     * @return void
     */
    public function sse()
    {
        // 1. 核心：设置跨域响应头（必须在输出任何内容前设置）
        $this->setCorsHeaders();

        // 1. 彻底关闭所有输出缓冲
        while (ob_get_level() > 0) {
            ob_end_clean(); // 逐层关闭并清空缓冲区
        }

        // 2. 关闭PHP默认输出缓冲（php.ini中的output_buffering）
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', 0); // 禁用GZIP压缩（可能导致缓冲）

        // 3. 设置SSE响应头（保持不变）
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Connection: keep-alive");
        header("X-Accel-Buffering: no");


        // 获取请求参数
        $type = $this->request->get('type', 'text'); // 推送类型：text/status
        $content = $this->request->get('content',
            '人民网北京10月27日电 （记者孙红丽）据商务部网站消息，商务部流通发展司负责人27日谈2025年1至9月我国批发和零售业发展情况时表示，今年以来，商务部深入贯彻落实党中央、国务院决策部署，加快完善现代商贸流通体系，多措并举推动批发和零售业高质量发展。据国家统计局数据，1至9月，我国批发和零售业增加值10.5万亿元，同比增长5.6%，占GDP比重为10.3%。我国批发和零售业发展总体向好，为全方位扩大国内需求、做强国内大循环提供有力支撑。

批零行业稳中有进，多元业态蓬勃发展。从批发业看，1至9月，商务部重点联系商品市场利润额同比增长8.2%。其中，工业消费品类、生产资料类市场利润总额同比分别增长17.9%和6.4%。从零售业看，据国家统计局数据，1至9月，商品零售额32.5万亿元，同比增长4.6%。限额以上便利店、专业店、超市、品牌专卖店、百货店等业态零售额均呈增长态势，同比分别增长6.4%、4.8%、4.4%、1.5%、0.9%，仓储会员店、集合店、无人值守商店等新兴业态零售额保持两位数增长。

城乡市场同步发展，客流消费稳定增长。城市商业稳中向好。1至9月，商务部重点监测的全国78个步行街（商圈）客流量、营业额同比分别增长4.3%、4.4%。全国210个试点地区累计建设6255个一刻钟便民生活圈，涉及养老、家政、零售等商业网点150.3万个，服务社区居民1.29亿人。县乡市场活力凸显。据国家统计局数据，1至9月，乡村消费品零售额4.9万亿元，同比增长4.6%，增速高于城镇0.2个百分点，包括镇区和乡村的县乡市场规模占社会消费品零售总额的比重达38.8%。农村电商蓬勃发展。商务大数据显示，1至9月，全国农村网络零售额同比增长7.7%；农产品网络零售额同比增长9.6%。

以旧换新潜能释放，国货“潮品”备受青睐。以旧换新政策效应持续显现。今年以来，超7600万名消费者购买12大类家电以旧换新产品超1.26亿台，超8100万名消费者购买手机等数码产品超8800万件，全国8.7万家销售门店开展电动自行车以旧换新，累计换购新车超1200万辆。据国家统计局数据，1至9月，限额以上单位家用电器和音像器材类零售额同比增长25.3%，限额以上通讯器材类零售额同比增长20.5%。老字号焕发活力。各地持续推进2025“老字号嘉年华”，举办特色活动220余场，实现直接销售38亿元，带动线上线下销售额169亿元。平台企业数据显示，1至9月，中华老字号餐饮企业堂食交易额同比增长23%。'
        );
        $interval = intval($this->request->get('interval', 100)); // 推送间隔(毫秒)

        try {
            if ($type === 'text') {
                // 文本逐字推送（支持中文）
                $chars = mb_str_split($content, 3, 'UTF-8');
                foreach ($chars as $char) {
                    $this->sendSseData([
                        'type' => 'text',
                        'content' => $char,
                        'time' => date('H:i:s')
                    ]);
                    usleep($interval * 1000);
                }
                // 推送完成标识
                $this->sendSseData([
                    'type' => 'complete',
                    'msg' => 'done'
                ]);
            } elseif ($type === 'status') {
                // 进度条推送
                for ($i = 0; $i <= 100; $i += 5) {
                    $this->sendSseData([
                        'type' => 'status',
                        'progress' => $i,
                        'msg' => "处理中...({$i}%)",
                        'time' => date('H:i:s')
                    ]);
                    usleep($interval * 100);
                }
                // 推送完成标识
                $this->sendSseData([
                    'type' => 'complete',
                    'msg' => '任务处理完成'
                ]);
            }
        } catch (\Exception $e) {
            $this->sendSseData([
                'type' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }
    /**
     * 设置跨域响应头（关键方法）
     */
    private function setCorsHeaders()
    {
        // 允许的前端域名（生产环境建议指定具体域名，如 'https://your-frontend.com'）
        $allowOrigin = '*';
        // 允许的请求方法
        $allowMethods = 'GET, OPTIONS';
        // 允许的请求头
        $allowHeaders = 'Origin, X-Requested-With, Content-Type, Accept';
        // 预检请求缓存时间（避免频繁OPTIONS请求）
        $maxAge = 86400; // 24小时

        // 1. 处理预检请求（OPTIONS请求）
        if ($this->request->method() === 'OPTIONS') {
            header("Access-Control-Allow-Origin: {$allowOrigin}");
            header("Access-Control-Allow-Methods: {$allowMethods}");
            header("Access-Control-Allow-Headers: {$allowHeaders}");
            header("Access-Control-Max-Age: {$maxAge}");
            header("Content-Length: 0");
            header("Content-Type: text/plain");
            exit; // 预检请求无需返回其他内容
        }

        // 2. 处理正常请求（GET请求）
        header("Access-Control-Allow-Origin: {$allowOrigin}");
        header("Access-Control-Allow-Credentials: true"); // 允许携带Cookie（如需）
    }

    /**
     * 发送 SSE 格式数据
     * @param array $data 要发送的数据
     */
    private function sendSseData(array $data)
    {
        // 输出SSE格式数据
        echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";

        // 安全刷新缓冲区（先检查是否存在缓冲区）
        if (ob_get_length() !== false) { // 检测缓冲区是否存在
            ob_flush(); // 刷新PHP自身缓冲区
        }
        flush(); // 刷新系统缓冲区（确保数据发送到客户端）
    }

    public function teamInfo(){

            // 查询全部数据
            $data = LeagueGame::select();

            // 转换为数组
            $array = $data->toArray();

            // 返回 JSON 数据
            return json($array);

    }

}
