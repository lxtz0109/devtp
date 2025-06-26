<?php
namespace app\listener;

class WebSocketMessage
{
    public function handle($event)
    {
       var_dump('hall');
        list($server, $frame) = $event;
        
        // 解析客户端消息
        $data = json_decode($frame->data, true);
        var_dump("websockt 收到消息");
        var_dump($data);
        if (!$data) {
            $server->push($frame->fd, json_encode(['code' => 400, 'message' => 'Invalid message format']));
            return;
        }
        
        // 消息类型分发
        switch ($data['type'] ?? '') {
            case 'chat':
                // 广播消息给所有客户端
                $this->broadcastMessage($server, $frame->fd, $data);
                break;
                
            case 'login':
                // 处理用户登录
                $this->handleLogin($server, $frame->fd, $data);
                break;
                
            default:
                $server->push($frame->fd, json_encode(['code' => 405, 'message' => 'Unsupported message type']));
        }
    }
    
    protected function broadcastMessage($server, $senderFd, $data)
    {
        $message = [
            'type' => 'chat',
            'from' => $senderFd,
            'content' => $data['content'] ?? '',
            'time' => time()
        ];
        
        // 遍历所有连接的客户端
        foreach ($server->connections as $fd) {
            // 排除发送者
            if ($fd != $senderFd && $server->isEstablished($fd)) {
                $server->push($fd, json_encode($message));
            }
        }
    }
    
    protected function handleLogin($server, $fd, $data)
    {
        // 这里可以实现用户认证逻辑
        $user = [
            'fd' => $fd,
            'name' => $data['name'] ?? "Guest_{$fd}",
            'login_time' => time()
        ];
        
        // 存储用户会话信息
        $server->connection_info($fd)['user'] = $user;
        
        // 返回登录成功消息
        $server->push($fd, json_encode([
            'code' => 200,
            'message' => 'Login success',
            'user' => $user
        ]));
        
        // 通知其他用户有新用户加入
        $this->broadcastMessage($server, $fd, [
            'type' => 'system',
            'content' => "用户： {$user['name']} 加入了聊天"
        ]);
    }
}    