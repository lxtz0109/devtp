<?php
namespace app\controller;

use app\model\PunchDetail;
use think\facade\Cache;
use think\Response;

class Punch
{
    // 渲染打卡页面
    public function index()
    {
        return view('punch/index');
    }

    // 获取定位地址（前端调用）
    public function getLocation()
    {
        $lat = $this->request->param('lat'); // 纬度
        $lng = $this->request->param('lng'); // 经度
        
        // 调用第三方地图API逆地址解析（示例使用高德地图）
        $key = '你的高德地图API密钥';
        $url = "https://restapi.amap.com/v3/geocode/regeo?key={$key}&location={$lng},{$lat}";
        
        $result = json_decode(file_get_contents($url), true);
        
        if ($result['status'] == 1) {
            $address = $result['regeocode']['formatted_address'];
            return json(['code' => 200, 'address' => $address]);
        } else {
            return json(['code' => 500, 'msg' => '获取地址失败']);
        }
    }

    // 提交打卡
    public function submit()
    {
        $username = session('username'); // 从session获取用户名
        $address = $this->request->param('address');
        
        if (empty($username) || empty($address)) {
            return json(['code' => 400, 'msg' => '参数缺失']);
        }
        
        // 检查是否重复打卡（同一天内）
        $today = date('Y-m-d');
        $exists = PunchDetail::where('username', $username)
            ->whereTime('punch_time', 'today')
            ->find();
            
        if ($exists) {
            return json(['code' => 403, 'msg' => '今天已打卡']);
        }
        
        // 写入数据库
        $data = [
            'username' => $username,
            'address' => $address,
            'punch_time' => date('Y-m-d H:i:s')
        ];
        
        $result = PunchDetail::create($data);
        
        if ($result) {
            return json(['code' => 200, 'msg' => '打卡成功']);
        } else {
            return json(['code' => 500, 'msg' => '打卡失败']);
        }
    }
}
