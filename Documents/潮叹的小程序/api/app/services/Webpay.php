<?php

class Webpay{
    //微信支付（jspai专用）
    const TRADETYPE_JSAPI = 'JSAPI',TRADETYPE_NATIVE = 'NATIVE',TRADETYPE_APP = 'APP';
    const URL_UNIFIEDORDER = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    const URL_ORDERQUERY = "https://api.mch.weixin.qq.com/pay/orderquery";
    const URL_CLOSEORDER = 'https://api.mch.weixin.qq.com/pay/closeorder';
    const URL_REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    const URL_REFUNDQUERY = 'https://api.mch.weixin.qq.com/pay/refundquery';
    const URL_DOWNLOADBILL = 'https://api.mch.weixin.qq.com/pay/downloadbill';
    const URL_REPORT = 'https://api.mch.weixin.qq.com/payitil/report';
    const URL_SHORTURL = 'https://api.mch.weixin.qq.com/tools/shorturl';
    const URL_MICROPAY = 'https://api.mch.weixin.qq.com/pay/micropay';
    const Web_APP_ID  = "wx68d51a56026931d3";

    const Web_APP_KEY = "qwertyuiopasdfghjklzxcvbnm123456";

    const Web_MCH_ID  = 1347270501;

    const Web_SSLCERT_PATH = "/app/ssl/apiclient_cert.pem";

    const Web_SSLKEY_PATH  = "/app/cert/apiclient_key.pem";

    const PAY_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";

    protected $weixin_order;

    protected $response;

    // public function __construct( $weixin_order ){
    //     $this->weixin_order = $weixin_order;
    // }

    // public function requestPay(){
    //     $xml_params     = $this->weixin_order->getXmlData();
    //     $this->response = $this->postXml($xml_params);
    //     // return [$this->parseResponse( $xml_params ),$this->parseResponse($this->response)];
    //     return $this->parseResponse( $this->response );
    // }



    public function getPrepayId($body,$out_trade_no,$total_fee,$notify_url,$openid) {
        $data = array();
        $data["nonce_str"]    = $this->get_nonce_string();
        $data["body"]         = $body;
        $data["out_trade_no"] = $out_trade_no;
        $data["total_fee"]    = $total_fee;
        $data["spbill_create_ip"] = $_SERVER["REMOTE_ADDR"];
        $data["notify_url"]   = $notify_url;
        $data["trade_type"]   = self::TRADETYPE_JSAPI;
        $data["openid"]   = $openid;
        $result = $this->unifiedOrder($data);
        if ($result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return $result["prepay_id"];
        } else {
            $this->error = $result["return_code"] == "SUCCESS" ? $result["err_code_des"] : $result["return_msg"];
            $this->errorXML = $this->array2xml($result);
            return null;
        }
    }

    /**
     * 统一下单接口
     */
    public function unifiedOrder($params) {
        $data = array();
        $data["appid"] = 'wx68d51a56026931d3';
        $data["mch_id"] = 1347270501;
        $data["device_info"] = (isset($params['device_info'])&&trim($params['device_info'])!='')?$params['device_info']:null;
        $data["nonce_str"] = $this->get_nonce_string();
        $data["body"] = $params['body'];
        $data["detail"] = isset($params['detail'])?$params['detail']:null;//optional
        $data["attach"] = isset($params['attach'])?$params['attach']:null;//optional
        $data["out_trade_no"] = isset($params['out_trade_no'])?$params['out_trade_no']:null;
        $data["fee_type"] = isset($params['fee_type'])?$params['fee_type']:'CNY';
        $data["total_fee"]    = $params['total_fee'];
        $data["spbill_create_ip"] = $params['spbill_create_ip'];
        $data["time_start"] = isset($params['time_start'])?$params['time_start']:null;//optional
        $data["time_expire"] = isset($params['time_expire'])?$params['time_expire']:null;//optional
        $data["goods_tag"] = isset($params['goods_tag'])?$params['goods_tag']:null;
        $data["notify_url"] = $params['notify_url'];
        $data["trade_type"] = $params['trade_type'];
        $data["product_id"] = isset($params['product_id'])?$params['product_id']:null;//required when trade_type = NATIVE
        $data["openid"] = isset($params['openid'])?$params['openid']:null;//required when trade_type = JSAPI
        $result = $this->post(self::URL_UNIFIEDORDER, $data);
        
        return $result;
    }
    private function get_nonce_string() {
        return substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm123456"),0,32);
    }

    private function post($url, $data,$cert = false) {
        $data["sign"] = $this->sign($data);
        $xml = $this->array2xml($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        if($cert == true){
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, Web_SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY,Web_SSLKEY_PATH);
        }
        $content = curl_exec($ch);
        $array = $this->xml2array($content);
        return $array;
    }
/**
     * 数据签名
     * @param $data
     * @return string
     */
    private function sign($data) {
        ksort($data);
        $string1 = "";
        foreach ($data as $k => $v) {
            if ($v && trim($v)!='') {
                $string1 .= "$k=$v&";
            }
        }
        $stringSignTemp = $string1 . "key=qwertyuiopasdfghjklzxcvbnm123456" ;
        $sign = strtoupper(md5($stringSignTemp));
        return $sign;
    }

    private function array2xml($array) {
        $xml = "<xml>" . PHP_EOL;
        foreach ($array as $k => $v) {
            if($v && trim($v)!='')
                $xml .= "<$k><![CDATA[$v]]></$k>" . PHP_EOL;
        }
        $xml .= "</xml>";
        return $xml;
    }

    private function xml2array($xml) {
        $array = array();
        $tmp = null;
        try{
            $tmp = (array) simplexml_load_string($xml);
        }catch(Exception $e){}
        if($tmp && is_array($tmp)){
            foreach ( $tmp as $k => $v) {
                $array[$k] = (string) $v;
            }
        }
        return $array;
    }

    public function postXml($xml){
        $ch  = curl_init();
        curl_setopt( $ch, CURLOPT_TIMEOUT, 200);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt( $ch, CURLOPT_ENCODING, 'gzip,deflate');

        curl_setopt( $ch, CURLOPT_URL, self::PAY_URL );
        curl_setopt( $ch, CURLOPT_HEADER, FALSE);

        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST,2);//严格校验

        // curl_setopt( $ch, CURLOPT_SSLCERTTYPE, 'PEM');
        // curl_setopt( $ch, CURLOPT_SSLCERT, self::Web_SSLCERT_PATH);
        // curl_setopt( $ch, CURLOPT_SSLKEYTYPE, 'PEM');
        // curl_setopt( $ch, CURLOPT_SSLKEY, self::Web_SSLKEY_PATH);


        curl_setopt( $ch, CURLOPT_POST,true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml);

        $response      = curl_exec( $ch );
        curl_close( $ch );
        return $response;
    }
}


?>