<?php

namespace app\yjs;

class Message
{
    public int $type;
    public int $step;

    public string $data;

    private string $stream;
    private int $length;
    private int $i0;

    public function __construct(string $stream)
    {
        $this->i0 = 2;

        $this->type = ord($stream[0]);
        $this->step = ord($stream[1]);
        $this->stream = $stream;
        $this->length = strlen($stream);
        $this->data = $this->read_message();
    }

    private function read_var_uint(): int
    {
        if ($this->length <= 0) {
            throw new RuntimeException("Y protocol error");
        }
        $uint = 0;
        $i = 0;
        while (true) {
            $byte = ord($this->stream[$this->i0]);
            $uint += ($byte & 127) << $i;
            $i += 7;
            $this->i0 += 1;
            $this->length -= 1;
            if ($byte < 128) {
                break;
            }
        }
        return $uint;
    }

    private function read_message(): ?string
    {
        if ($this->length === 0) {
            return null;
        }
        $length = $this->read_var_uint();
        if ($length === 0) {
            return "";
        }
        $i1 = $this->i0 + $length;
        $message = substr($this->stream, $this->i0, $length);
        $this->i0 = $i1;
        $this->length -= $length;
        return $message;
    }
}
