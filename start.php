<?php

use think\App;

require __DIR__ . '/vendor/autoload.php';

(new App())->initialize();

use app\yjs\RedisPool;
use app\yjs\Doc;
use app\yjs\Protocol;
use Swoole\WebSocket\Server;
use think\facade\Log;
use function app\yjs\read_message;

$conf = [
    'default' => ['127.0.0.1', 6379, '']
];
RedisPool::addServer($conf);

$server = new Server("0.0.0.0", 9501);

$server->on('open', function ($server, $request) {
    Log::debug("client {$request->fd} connected\n");

    $path = $request->server['path_info'] ?? '/';

    // 解析路径参数
    $pathParams = parsePathParams($path);
    $doc = Doc::new(end($pathParams));

    Log::debug("new completed....");

    $doc->join($request->fd);
});

$server->on('message', function ($server, $frame) {
    printStringBytes($frame->data);
//    Log::debug("received message: {$frame->data}\n\n\n\n");

    $message = Protocol::decode($frame->data);
    $doc = Doc::retrieve($frame->fd);
    switch ($message->type) {
        case Protocol::SYNC:
            if($doc == null){
                return;
            }
            $message = Protocol::decode($frame->data);
            switch ($message->step) {
                case Protocol::SYNC_STEP1:
                    $doc->sync($message->data, $frame->fd, $server);
                    break;
                case Protocol::SYNC_STEP2:
                case Protocol::SYNC_UPDATE:
                    $doc->apply($message->data, $server);
                    break;
                default:
                    break;
            }
            break;
        case Protocol::AWARENESS:
            $doc->broadcast($frame->data, $server);
            break;
        default:
            break;
    }
});

$server->on('close', function ($server, $fd) {
    $doc = Doc::retrieve($fd);
    if($doc == null){
        return;
    }
    $doc->quit($fd);
    Log::debug("client {$fd} closed\n");
});

$server->start();

function printStringBytes($str)
{
//    $length = strlen($str);
//    for ($i = 0; $i < $length; $i++) {
//        $byte = $str[$i];
//        $decimalValue = ord($byte);
//        Log::debug("Byte $i: $byte (Decimal: $decimalValue)\n");
//    }
}

function parsePathParams(string $path): array
{
    $path = trim($path, '/');
    if (empty($path)) {
        return [];
    }

    $segments = explode('/', $path);
    $params = [];

    foreach ($segments as $segment) {
        if (strpos($segment, '=') !== false) {
            list($key, $value) = explode('=', $segment, 2);
            $params[$key] = $value;
        } else {
            $params[] = $segment;
        }
    }

    return $params;
}
