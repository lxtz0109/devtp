<?php

namespace app\controller;

use app\BaseController;

class Pay extends BaseController
{
  public function index(){
      // 适配器模式使用示例
      $alipay = new Alipay();
      $alipayAdapter = new AlipayAdapter($alipay);
      echo $alipayAdapter->pay(3000) . PHP_EOL;

      $wechatPay = new WechatPay();
      $wechatPayAdapter = new WechatPayAdapter($wechatPay);
      echo $wechatPayAdapter->pay(366.55) . PHP_EOL;

  }
}

// 定义项目统一的支付接口
interface PaymentInterface
{
    public function pay($amount);
}

// 支付宝支付类，有自己的支付方法
class Alipay
{
    public function alipayPay($money)
    {
        return "使用支付宝支付了 $money 元";
    }
}

// 微信支付类，有自己的支付方法
class WechatPay
{
    public function wechatPay($cash)
    {
        return "使用微信支付了 $cash 元";
    }
}

// 支付宝支付适配器
class AlipayAdapter implements PaymentInterface
{
    private $alipay;

    public function __construct(Alipay $alipay)
    {
        $this->alipay = $alipay;
    }

    public function pay($amount)
    {
        return $this->alipay->alipayPay($amount);
    }
}

// 微信支付适配器
class WechatPayAdapter implements PaymentInterface
{
    private $wechatPay;

    public function __construct(WechatPay $wechatPay)
    {
        $this->wechatPay = $wechatPay;
    }

    public function pay($amount)
    {
        return $this->wechatPay->wechatPay($amount);
    }
}

