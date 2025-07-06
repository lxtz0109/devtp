<?php

namespace app\yjs;

class Protocol
{
    const SYNC        = 0;
    const AWARENESS   = 1;
    const SYNC_STEP1  = 0;
    const SYNC_STEP2  = 1;
    const SYNC_UPDATE = 2;

    private static function write_var_uint(int $num): string
    {
        $res = [];
        while ($num > 127) {
            $res[] = chr(128 | ($num & 127));
            $num   >>= 7;
        }
        $res[] = chr($num);
        return implode('', $res);
    }

    private static function create_message(string $data, int $msg_type): string
    {
        return chr(self::SYNC) . chr($msg_type) . self::write_var_uint(strlen($data)) . $data;
    }

    public static function create_sync_step1_message(string $data): string
    {
        return self::create_message($data, self::SYNC_STEP1);
    }

    public static function create_sync_step2_message(string $data): string
    {
        return self::create_message($data, self::SYNC_STEP2);
    }

    public static function create_update_message(string $data): string
    {
        return self::create_message($data, self::SYNC_UPDATE);
    }

    public static function decode($message): Message
    {
        return new Message($message);
    }
}
