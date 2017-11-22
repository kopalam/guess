<?php

use Phalcon\Mvc\Controller;


class WebpaysController extends Controller
{

    public function rechargeAction(){
        $token   = $this->request->getPost("token");
        $openid  = $this->request->getPost("openId");
        $cash    = $this->request->getPost("cash");
        $user_id = $this->request->getPost("uid");



        // $data['status']   = 0;
        // $data['message']  = '';


        // $openid  = "oTc4R0dkHHl4COHPA26nOAvmq9b0";
        // $uid = 1;
        // $cash   = 28;


        $author = new Authory( $token );
        $author->loggingVerify();

        try{
            $trade_sn = uniqid();
            $params['body']             = "成为潮叹Vip";
            $params['out_trade_no']     = $trade_sn;
            $params['total_fee']        = intval($cash)*100;
            $params['openid']           = $openid;
            $params['notify_url']       = 'https://sale.imchaotan.com/api/users/';
            $params['spbill_create_ip']  =   $_SERVER['REMOTE_ADDR'];    //终端IP
            $params['trade_type']       = 'JSAPI';
            $params['time_start']        =   date('YmdHis');
            $params['time_expire']       =   date('YmdHis',time()+8600); //交易结束时间
             // print_r($params);exit();
            $order = new Webpay();
            $result= $order->unifiedOrder($params);


           if(!empty($result['prepay_id']) || isset($result['prepay_id']))
                {
                    $data['appId'] = WebPay::Web_APP_ID;
                    $data['nonceStr'] = $result['nonce_str'];
                    $data['package'] = $result['prepay_id'];
					// $time=time();
                    $data['timeStamp'] = time();
                    $data['signType'] = 'MD5';
                    $data['sign'] = $result['sign'];
                    // $data['total_fee'] = $params['total_fee'];
                    $data['key'] =  Webpay::Web_APP_KEY;
                    $data['out_trade_no'] = $params['out_trade_no'];
                   
                   Utils::apiDisplay(['status'=>0,'data'=>$data]);
                }

        }catch(Exception $e){
            $data['status']  = 1;
            $data['message'] = $e->getMessage();
            Utils::apiDisplay( $data );
        }
    }


    // public function notifyAction(){

    //     $data['status']   = 0;
    //     $data['message']  = '';

    //     $xml    = $GLOBALS['HTTP_RAW_POST_DATA'];
    //     $notify = Utils::wxParseXml( $xml );

    //     $trade_sn = $notify['out_trade_no'];
    //     $number   = $notify['transaction_id'];
    //     Utils::apiDisplay( $data );

    //     try{
    //         $finance = new Finance();
    //         $finance->recharge( $trade_sn , $number );
    //     }catch(Exception $e){
    //         $data['status']  = 1;
    //         $data['message'] = $e->getMessage();
    //         Utils::apiDisplay( $data );
    //     }

    //     Utils::apiDisplay( $data );
    // }


    public function buySuccessAction(){

    	$token  = $this->request->getPost("token"); 
    	$author = new Authory( $token );
        $author->loggingVerify();
        $uid = $this->request->getPost("uid");
        $cash   = $this->request->getPost("total_fee");   //微信支付流水号
        $trade_sn = $this->request->getPost("out_trade_no"); //商品订单号



       
            $finance = new Finance();
            $update = $finance->createOrder( $uid, $cash, $trade_sn );
            // echo $update;exit();
        if($update == true){
           Utils::apiDisplay( ['status'=>0,'message'=>'充值成功'] );
        }else{
           Utils::jsonError(1,'充值出现了问题，返回再试试看？');
        }

    }

}
?>