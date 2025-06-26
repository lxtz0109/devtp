<?php
namespace app\controller;

use think\facade\Db;
use think\facade\Cache;
use think\Response;

class LotteryController
{
    // 奖项配置
    private $prizes = [
        'special' => ['name' => '特等奖', 'count' => 1],
        'first'   => ['name' => '一等奖', 'count' => 3],
        'second'  => ['name' => '二等奖', 'count' => 10],
        'third'   => ['name' => '三等奖', 'count' => 20],
        'none'    => ['name' => '未中奖', 'count' => 66] // 100-1-3-10-20
    ];

    // 姓名字库
    private $surnames = ['赵', '钱', '孙', '李', '周', '吴', '郑', '王', '冯', '陈'];
    private $names = ['伟', '芳', '娜', '秀英', '敏', '静', '强', '磊', '军', '洋'];

    /**
     * 执行抽奖接口
     */
    public function draw()
    {

        // 生成100个参与抽奖的用户
        $users = $this->generateUsers(100);

        // 执行抽奖
        $result = $this->lotteryDraw($users);

        // 记录中奖结果
        $this->recordResults($result);

        // 返回结果
        return Response::create([
            'code' => 200,
            'message' => '抽奖完成',
            'data' => $result
        ], 'json');
    }

    /**
     * 生成指定数量的随机用户
     */
    private function generateUsers($count)
    {
        $users = [];
        for ($i = 0; $i < $count; $i++) {
            $surname = $this->surnames[array_rand($this->surnames)];
            $name = $this->names[array_rand($this->names)];
            $users[] = [
                'id' => $i + 1,
                'name' => $surname . $name,
                'prize' => null
            ];
        }
        return $users;
    }

    /**
     * 执行抽奖逻辑
     */
    private function lotteryDraw($users)
    {
        // 打乱用户顺序
        shuffle($users);

        // 当前各奖项已中奖数量
        $currentCounts = array_fill_keys(array_keys($this->prizes), 0);

        // 遍历用户进行抽奖
        foreach ($users as &$user) {
            // 计算剩余奖项概率
            $availablePrizes = $this->getAvailablePrizes($currentCounts);

            // 如果没有可用奖项，直接设为未中奖
            if (empty($availablePrizes)) {
                $user['prize'] = 'none';
                continue;
            }

            // 随机选择一个奖项
            $prizeKey = $availablePrizes[array_rand($availablePrizes)];
            $user['prize'] = $prizeKey;
            $currentCounts[$prizeKey]++;
        }

        return $users;
    }

    /**
     * 获取当前还可抽取的奖项
     */
    private function getAvailablePrizes($currentCounts)
    {
        $available = [];
        foreach ($this->prizes as $key => $prize) {
            if ($currentCounts[$key] < $prize['count']) {
                $available[] = $key;
            }
        }
        return $available;
    }

    /**
     * 记录抽奖结果
     */
    private function recordResults($results)
    {
        // 按奖项分组
        $groupedResults = [];
        foreach ($results as $user) {
            $groupedResults[$user['prize']][] = $user;
        }

        // 写入数据库或日志
        foreach ($groupedResults as $prizeKey => $users) {
            $prizeName = $this->prizes[$prizeKey]['name'];

            // 批量插入数据库示例
            $data = [];
            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user['id'],
                    'user_name' => $user['name'],
                    'prize_name' => $prizeName,
                    'create_time' => date('Y-m-d H:i:s')
                ];
            }

            // 写入数据库
            Db::name('lottery_results')->insertAll($data);
        }

        // 缓存中奖名单（1天）
        Cache::set('lottery_results', $results, 86400);
    }
}