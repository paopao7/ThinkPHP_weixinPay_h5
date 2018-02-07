<?php
namespace Home\Controller;
use Think\Controller;
use Org\weixinPay;
class IndexController extends Controller {
	//支付页面
    public function index(){

       Vendor('weixinPay','ThinkPHP/Library/Org/weixinPay/','.class.php');

        $code = $_GET['code'];

        if($code) {
            $open_id = weixinPay::get_open_id($code);

            if($open_id) {
                session('open_id', $open_id);
            }
            
            $this->display();
        }else{
        	
        	//****************************************************************************************
        	//***此处需填写当前方法所在的完整地址，例如http://www.itinfor.cn/Home/Index/index*********
        	//***该地址需配置到公众号-开发者中心-功能服务-网页账号的授权回调页面域名，不包含“http:”***
        	//***只需填写"www.itinfor.cn/Home/Index/index" 即可***************************************
        	//***具体可参考该地址：http://blog.csdn.net/wyx100/article/details/46755143***************
        	//****************************************************************************************
        	
        	$base_url = "http://www.itinfor.cn/Home/Index/index";//此处以实际为准

            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7037e6f90176eb82&redirect_uri=".$base_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            redirect($url,0);
        }
    }

    //生成微信支付JS API接口参数
    public function get_js_api(){
        $price = $_POST['price']*100;

        Vendor('weixinPay','ThinkPHP/Library/Org/weixinPay/','.class.php');

        //调用统一下单接口
        $jsApiParameters = weixinPay::place_order($price);

        echo $jsApiParameters;
    }

    //微信支付回调地址
    public function weixin_pay_back(){
        Vendor('weixinPay','ThinkPHP/Library/Org/weixinPay/','.class.php');

        //接收微信回调传参数
        $back_data = file_get_contents('php://input');

        $data = weixinPay::pay_back($back_data);
        
        if($data['return_code'] == "SUCCESS" and $data['return_msg'] == "OK"){
            $new_data['return_code'] = "SUCCESS";
            $new_data['return_msg'] = "OK";            
        }else{
            $new_data['return_code'] = "FAIL";
            $new_data['return_msg'] = $data['return_msg'];
        }
       
        $xml = "<xml>";
        foreach ($new_data as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        
         
        
        echo $xml;exit;
    }
}