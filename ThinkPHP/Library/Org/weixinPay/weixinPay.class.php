<?php
namespace Org;
class weixinPay{
	//获取open_id接口
	public function get_open_id($code){
		ini_set('date.timezone','Asia/Shanghai');
		//error_reporting(E_ERROR);
		require_once dirname(__FILE__)."/lib/WxPay.Api.php";
		require_once dirname(__FILE__)."/example/WxPay.JsApiPay.php";
		require_once dirname(__FILE__).'/example/log.php';

		//①、获取用户openid
		$tools = new \JsApiPay();
		$openId = $tools->GetOpenid($code);

		return $openId;
	}

	//统一下单接口
	public function place_order($coupon_id,$price){

        ini_set('date.timezone','Asia/Shanghai');
        //error_reporting(E_ERROR);
        require_once dirname(__FILE__)."/lib/WxPay.Api.php";
        require_once dirname(__FILE__)."/lib/WxPay.Data.php";
        require_once dirname(__FILE__)."/example/WxPay.JsApiPay.php";
        require_once dirname(__FILE__).'/example/log.php';

        //①、获取用户openid
        $tools = new \JsApiPay();

        $openId = session('open_id');

        //②、统一下单
        $out_trade_no = \WxPayConfig::MCHID.date("YmdHis");      
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("H5支付测试");
        $input->SetAttach("test");
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee((string)$price);
//        $input->SetTime_start(date("YmdHis"));
//        $input->SetTime_expire(date("YmdHis", time() + 600));
//        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://www.itinfor.cn/Home/Index/weixin_pay_back");//此处会异步通知地址,以实际为准
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
//        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';

        $jsApiParameters = $tools->GetJsApiParameters($order);

        //******************************************************
        //***此处可以编写具体的业务了逻辑，例如写入消费记录表***
        //******************************************************
        

        return $jsApiParameters;exit;

	}

	//企业支付接口回调
    public function pay_back($back_data){
        require_once dirname(__FILE__)."/lib/WxPay.Data.php";

        $result = \WxPayResults::Init($back_data);

        //如果返回成功则验证签名
        try {
            $result = \WxPayResults::Init($back_data);

            //编写实际逻辑代码
            if($result['result_code'] == "SUCCESS" and $result['return_code'] == "SUCCESS"){

                //******************************************************************
                //***此处可以编写具体的业务了逻辑，例如更新消费表中的是否回调字段***
                //******************************************************************

                $data['return_code'] = "SUCCESS";
                $data['return_msg'] = "OK";
                $data['out_trade_no'] = $result['out_trade_no'];
            }
        } catch (WxPayException $e){
            $msg = $e->errorMessage();

            $data['return_code'] = "FAIL";
            $data['return_msg'] = $msg;
        }

        return $data;
    }
}