<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>支付页面</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta name="viewport" content="width=device-width,target-densitydpi=high-dpi,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <script type="text/javascript" src="/weixin_h5/Public/js/jquery.js"></script>
    <script type="text/javascript" src="/weixin_h5/Public/js/layer_mobile/layer.js"></script>
    <script type="text/javascript" src="/weixin_h5/Public/js/jquery.artDialog.js"></script>
    <script type="text/javascript" src="/weixin_h5/Public/js/iframeTools.js"></script>
    <link href="/weixin_h5/Public/css/skins/default.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        body{
            background-color: #f3f3f3;
            font-family: "Microsoft YaHei", 微软雅黑, Verdana, Arial, Helvetica, sans-serif;
        }

        .pay_box{
            color: #333333;
            margin-top: 33px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .price_box{
            background-color: #ffffff;
            border-radius: 10px;
            border: solid 1px #e5e5e5;
        }

        .order_price_box{
            height: 72px;
            margin-left: 10px;
            border-bottom: solid 1px #e5e5e5;
        }

        .order_price_key{
            font-size: 14px;
            height: 72px;
            line-height: 79px;
        }

        .order_price_value{
            float: right;
            width: 50%;
            height: 30px;
            line-height: 30px;
            font-size: 26px;
            text-align: right;
            padding-right: 5px;
            margin-top: 22px;
            margin-right: 10px;
            border: none;
            outline-style: none;
        }

        .no_join_box{
            height: 60px;
            margin-left: 10px;
        }

        .no_join_key{
            font-size: 14px;
            height: 65px;
            line-height: 65px;
        }

        .no_join_value{
            float: right;
            width: 45%;
            height: 25px;
            line-height: 25px;
            font-size: 26px;
            text-align: right;
            padding-right: 5px;
            margin-top: 14px;
            margin-right: 10px;
            border: none;
            outline-style: none;
        }

        .no_join_value::-webkit-input-placeholder{
            color: #cccccc;
            font-size: 12px;
            height: 36px;
            line-height: 36px;
        }

        .no_join_value::-moz-placeholder{
            color: #cccccc;
            font-size: 12px;
            height: 36px;
            line-height: 36px;
        }

        .no_join_value::-moz-placeholder{
            color: #cccccc;
            font-size: 12px;
            height: 36px;
            line-height: 36px;
        }

        .no_join_value:-ms-input-placeholder{
            color: #cccccc;
            font-size: 12px;
            height: 36px;
            line-height: 36px;
        }

        .true_pay_box{
            margin-top: 25px;
            line-height: 40px;
            height: 40px;
        }

        .true_pay_value{
            float: right;
            color: #fa6268;
            font-size: 26px;
        }

        .pay_btn_box{
            margin-top: 15px;
            text-align: center;
        }

        .pay_btn{
            width: 100%;
            color: #ffffff;
            font-size: 14px;
            padding: 12px 0;
            border-radius: 5px;
            background-color: #8CC31E;
            border: solid 1px #8CC31E;
        }
    </style>
</head>

<body>
    <div class="pay_box">
        <div class="price_box">
            <div class="order_price_box">
                <span class="order_price_key">订单金额(元)</span>
                <input type="number" class="order_price_value" />
            </div>
            <div class="no_join_box">
                <span class="no_join_key">不参与优惠金额(元)</span>
                <input type="number" class="no_join_value" value="" placeholder="请询问服务员后输入" />
            </div>
        </div>
        <div class="true_pay_box">
            <span class="discount_key">实际付款(元)</span>
            <span class="true_pay_value"></span>
        </div>
        <div class="pay_btn_box">
            <button type="button" class="pay_btn" onclick="pay()">点击支付</button>
        </div>
    </div>
    <script type="text/javascript">
        //支付
        function pay() {
            var price = $(".true_pay_value:eq(0)").text();
            var coupon_id = art.dialog.data('coupon_id');

            if(price == "" || !price){
                //提示
                layer.open({
                    content: '请先填写支付金额'
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }else {
                $.ajax({
                    url:'get_js_api',
                    type:'POST', //GET
                    async:true,    //或false,是否异步
                    data:{
                        'price':price,
                        'coupon_id':coupon_id,
                    },
                    timeout:5000,    //超时时间
                    dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
                    beforeSend:function(xhr){
                        console.log(xhr)
                        console.log('发送前')
                    },
                    success:function(data,textStatus,jqXHR){
                        //data:该参数为调用微信统一支付接口得到的参数，通过art.dialog.data方法先存入在下面的call()方法中使用
                        //此处我尝试过将call()、jsApiCall()方法写在此处，但实际操作过程中无任何效果
                        //也尝试过写入某个隐藏域，在call()方法中去获取再使用，但实际操作也不行
                        //所以只能借助于art.dialog.data方法来存储和读取
                        //若您有更好的方法欢迎提供给我，谢谢
                        
                        console.log(data);
                        art.dialog.data("js_api",data);
                        callpay();
                        console.log(textStatus)
                        console.log(jqXHR)
                    },
                    error:function(xhr,textStatus){
                        console.log('错误')
                        console.log(xhr)
                        console.log(textStatus)
                    },
                    complete:function(){
                        console.log('结束')
                    }
                })
            }
        }

        //调用微信JS api 支付
        function jsApiCall()
        {
            var jsApiParameters = art.dialog.data("js_api");
            
            WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',jsApiParameters,
                function(res){
                    if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                        //提示
                        layer.open({
                            content: '支付成功'
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                    }else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                        //提示
                        layer.open({
                            content: '支付已取消'
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                    }else{
                        //提示
                        layer.open({
                            content: '支付失败'
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                    }
                }
        );
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }

    </script>
</body>
</html>