<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Event;
use Swoole\WebSocket\Server as WebSocketServer;

class WebSocket extends Command
{
    protected function configure()
    {
        $this->setName('websocket:serve')
            ->setDescription('Start WebSocket server');
    }

    protected function execute(Input $input, Output $output)
    {
        $config = config('swoole');

        $server = new WebSocketServer($config['host'], $config['port'], $config['mode'], $config['sock_type']);

        $server->set($config['option']);

        // 注册WebSocket事件
        $server->on('Open', function (WebSocketServer $server, $request) {
            //$output->writeln("New connection established: {$request->fd}");
            echo "New connection: {$request->fd}\n";
        });

        $server->on('Message', function (WebSocketServer $server, $frame) {
            // 触发事件处理消息
            var_dump("收到消息");
            try {
                Event::trigger('WebSocketMessage', [$server, $frame]);
            } catch (\Exception $e) {
                \think\facade\Log::error('事件触发失败: ' . $e->getMessage());
            }
        });

        $server->on('Close', function (WebSocketServer $server, $fd) {
           // $output->writeln("Connection closed: {$fd}");
            echo "Connection closed: {$fd}\n";
            // 通知其他用户
            $user = $server->connection_info($fd)['user'] ?? null;
            var_dump($user);
            if ($user) {
                $message = [
                    'type' => 'system',
                    'content' => "User {$user['name']} left the chat"
                ];

                foreach ($server->connections as $clientFd) {
                    if ($clientFd != $fd && $server->isEstablished($clientFd)) {
                        $server->push($clientFd, json_encode($message));
                    }
                }
            }
        });

        // 注册任务处理
        $server->on('Task', function (WebSocketServer $server, $task) {
            //$output->writeln("Task processing: {$task->id}");
            echo "Task processing: {$task->id}";
            // 处理异步任务
            $result = call_user_func_array($task->data['callback'], $task->data['params']);

            $server->finish($result);
        });

        $server->on('Finish', function (WebSocketServer $server, $taskId, $data) {
            //$output->writeln("Task completed: {$taskId}");
            echo "Task completed: {$taskId}";
        });

        $output->writeln("WebSocket server started at ws://{$config['host']}:{$config['port']}");

        $server->start();
    }
}    