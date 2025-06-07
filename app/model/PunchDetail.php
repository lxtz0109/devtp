<?php

// app/model/PunchDetail.php
namespace app\model;

use think\Model;

class PunchDetail extends Model
{
    protected $name = 'punch_detail';
    protected $autoWriteTimestamp = false; // 手动管理时间
}
