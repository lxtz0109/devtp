<?php

namespace app\yjs;

use think\facade\Log;

class Doc
{
    private $name;
    private $doc;
    private $transaction;
    private $t_lock;
    private $members = [];
    private $m_lock;
    private $store_time = null;
    private $destroyed = true;

    private static $ffi = null;
    private static $n_docs = [];
    private static $m_docs = [];
    private static $nd_lock;
    private static $md_lock;

    public static function new($name) : Doc {
        if(self::$ffi == null) {
            self::$ffi = \FFI::load(app()->getRootPath()."app/yjs/libyrs-php.h");
        }

        if(self::$nd_lock == null) {
            self::$nd_lock = new \Swoole\Lock(SWOOLE_RWLOCK);
        }

        if(self::$md_lock == null) {
            self::$md_lock = new \Swoole\Lock(SWOOLE_RWLOCK);
        }

        $doc = null;
        self::$nd_lock->lock_read();

        if (isset(self::$n_docs[$name])) {
            $doc = self::$n_docs[$name];
        }

        self::$nd_lock->unlock();

        if($doc != null) {
            return $doc;
        }

        self::$nd_lock->lock();

        $doc = new self($name);
        self::$n_docs[$name] = $doc;

        self::$nd_lock->unlock();

        return $doc;
    }

    public static function retrieve($socket) : Doc {
        self::$md_lock->lock_read();

        $doc = self::$m_docs[$socket];

        self::$md_lock->unlock();

        return $doc;
    }

    private function __construct($name) {
        $this->name = $name;
        $this->m_lock = new \Swoole\Lock(SWOOLE_RWLOCK);
    }

    private function recover(){
        if($this->destroyed) {
            Log::debug("recover.....".$this->name);

            $this->t_lock = new \Swoole\Lock(SWOOLE_RWLOCK);
            $this->members = [];
            $this->store_time = time();

            $options = self::$ffi->new("YOptions");
            $options->skip_gc=1;
            $this->doc = self::$ffi->ydoc_new_with_options($options);
            $this->transaction = self::$ffi->ydoc_write_transaction($this->doc,0, NULL);

            $redis = RedisPool::getRedis('default');
            if($redis->exists($this->name)){
                Log::debug("has file.....");

                $s = $redis->get($this->name);
                $len = strlen($s);
                $update_len = self::$ffi->new("uint32_t");
                $update_len->cdata = $len;
                $update = self::$ffi->new("char[{$len}]");
                \FFI::memcpy($update, $s, $len);

                self::$ffi->ytransaction_apply($this->transaction, $update, $update_len->cdata);
            }

            $this->destroyed = false;
        }
    }

    public function sync($sv, $socket, $server){
        $this->t_lock->lock_read();

        $update_len = self::$ffi->new("uint32_t");
        $update_len->cdata = 0;
        $update = self::$ffi->ytransaction_state_diff_v1($this->transaction, $sv, strlen($sv), \FFI::addr($update_len));
        $message = Protocol::create_sync_step2_message(\FFI::string($update, $update_len->cdata));
//        printStringBytes($message);
        Log::debug("\n\n\n\n");

        $server->push($socket, $message, WEBSOCKET_OPCODE_BINARY);

        $sv_len = self::$ffi->new("uint32_t");
        $sv = self::$ffi->ytransaction_state_vector_v1($this->transaction, \FFI::addr($sv_len));

        $message = Protocol::create_sync_step1_message(\FFI::string($sv, $sv_len->cdata));
        $server->push($socket, $message, WEBSOCKET_OPCODE_BINARY);

        $this->t_lock->unlock();
    }

    public function apply($update, $server) {
        $this->t_lock->lock();

        self::$ffi->ytransaction_apply($this->transaction, $update, strlen($update));

        $this->t_lock->unlock();

        $this->t_lock->lock_read();

        if(time() - $this->store_time >= 5){
            $this->store();
        }

        $this->t_lock->unlock();

        $this->broadcast(Protocol::create_update_message($update), $server);
    }

    public function join($socket) {
        $this->m_lock->lock();

        $this->recover();
        $this->members[] = $socket;

        $this->m_lock->unlock();


        self::$md_lock->lock();

        self::$m_docs[$socket] = $this;

        self::$md_lock->unlock();
    }

    public function quit($socket) {
        $this->m_lock->lock();

        foreach ($this->members as $index => $member) {
            if($member == $socket) {
                unset($this->members[$index]);
                break;
            }
        }

        if(count($this->members)==0){
            $this->destroy();
        }

        $this->m_lock->unlock();

        self::$md_lock->lock();

        unset(self::$m_docs[$socket]);

        self::$md_lock->unlock();
    }

    public function broadcast($message, $server) {
        $this->m_lock->lock_read();

        foreach ($this->members as $member) {
            $server->push($member, $message, WEBSOCKET_OPCODE_BINARY);
        }

        $this->m_lock->unlock();
    }

    private function store(){
        $this->store_time = time();

        $snapshot_len = self::$ffi->new("uint32_t");
        $snapshot_len->cdata = 0;
        $snapshot = self::$ffi->ytransaction_snapshot($this->transaction, \FFI::addr($snapshot_len));
        $update_len = self::$ffi->new("uint32_t");
        $update_len->cdata = 0;
        $update = self::$ffi->ytransaction_encode_state_from_snapshot_v1($this->transaction, $snapshot, $snapshot_len->cdata, \FFI::addr($update_len));
        $m = \FFI::string($update, $update_len->cdata);

        $redis = RedisPool::getRedis('default');
        Log::debug("set redis.....".$this->name);
        $redis->set($this->name, $m);
    }

    private function destroy() {
        Log::debug("destroy.....".$this->name);

        $this->store();

        self::$ffi->ydoc_destroy($this->doc);

        unset($this->t_lock);
        unset($this->members);

        $this->destroyed = true;
    }
}
