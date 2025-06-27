<?php
namespace app\controller;

use think\facade\Cache;

class RedisController
{
    // 使用缓存助手操作 Redis
    public function cacheExample()
    {

        // 设置缓存
        Cache::set('user_info_215004', [
            'id'   => 215004,
            'name' => '德罗赞',
            'score'   => 159,
            'create_date'   =>date('Y-m-d h:i:s',time()),
            'update_date'   =>date('Y-m-d h:i:s',time()),

        ], 3600);

        // 获取缓存
        $user = Cache::get('user_info_215004');

        return json([
            'user' => $user,
            'data' => 1
        ]);
    }

    // 直接使用 Redis 类操作
    public function redisExample()
    {
        $redis = new \Redis();
        $redis->connect(config('cache.stores.redis.host'), config('cache.stores.redis.port'));

        if (config('cache.stores.redis.password')) {
            $redis->auth(config('cache.stores.redis.password'));
        }

        if (config('cache.stores.redis.select')) {
            $redis->select(config('cache.stores.redis.select'));
        }

        // 字符串操作
        $redis->set('counter', 1);
        $redis->incr('counter');
        $counter = $redis->get('counter');

        // Hash 操作
        $redis->hMSet('user:1', [
            'name'  => 'John',
            'age'   => 30,
            'email' => 'john@example.com'
        ]);
        $user = $redis->hGetAll('user:1');

        // List 操作
        $redis->rPush('tasks', 'task1', 'task2', 'task3');
        $tasks = $redis->lRange('tasks', 0, -1);

        return json([
            'counter' => $counter,
            'user'    => $user,
            'tasks'   => $tasks
        ]);
    }
}