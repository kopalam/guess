<?php

use Phalcon\Mvc\Controller;


class UsersController extends Controller
{
	public function loginAction()
	{
		/*接收code，获取用户资料
			'code',
			'rawData',
			'signature',
			'encryptedData',
			'iv'
		*/
			$code = $this->request->getPost('code');
			$rawData = $this->request->getPost('rawData');
			$signature = $this->request->getPost('signature');
			$encryptedData = $this->request->getPost('encryptedData');
			$iv = $this->request->getPost('iv');
			$getData = new WXLoginHelper;
			$userData = $getData->checkLogin($code, $rawData, $signature, $encryptedData, $iv);
			// echo json_encode($userData);exit();

			/*检查user表是否存在该用户，如果存在，则返回session，如果不存在，写入表再发回session*/
			$openId = $userData['openId'];
			$unionId = $userData['unionId'];

			 $paramers = ['conditions' => "openId = '".$openId."'"];

			$has_user 	= 	Users::findFirst( $paramers );


			$getUser = ['user_id'=>$has_user->id,'unionId'=>$has_user->unionId,'openId'=>$has_user->openId];



			if($has_user->openId == $openId)
			{
				$getUser = ['user_id'=>$has_user->id,'unionId'=>$has_user->unionId,'openId'=>$has_user->openId];
			}

			if(!$unionId)
				Utils::jsonError(1,'参数错误');

			if(empty($has_user))
			{


				$user = new Users;
				$user->openId = $userData['openId'];
				$user->unionId = $userData['unionId'];
				$user->nickName = preg_replace('/[\x{10000}-\x{10FFFF}]/u','',$userData['nickName']);
				$user->gender = $userData['gender'];
				$user->language = $userData['language'];
				$user->city = $userData['city'];
				$user->province = $userData['province'];
				$user->country = $userData['country'];
				$user->avatarUrl = $userData['avatarUrl'];
				$user->reg_time = time();
				$user->save();

				$getUser = ['user_id'=>$user->id,'openId'=>$user->openId,'unionId'=>$user->unionId];

			}

			$getUser['token']=$userData['session3rd'];

			
			

			$cache = new Cache;
			$cache->set($userData['session3rd'],json_encode($userData['sessionKey'].'='.$unionId),86400*10);
			$this->view->disable();
			Utils::apiDisplay(['status'=>0,'data'=>$getUser]);
 	

	}

	function testAction()
	{
		$redis = new Redis(); 
	   // $redis->connect('120.25.63.187', "63796"); 
	   // echo "Connection to server sucessfully"; 
	   $redis->set("tutorial-name", "Redis tutorial",86400*10); 
	   echo "Stored string in redis:: " .$redis→get("tutorial-name"); 
	}

	function infoAction()
	{
		echo phpinfo();
	}

	function sendMessageAction($uid,$formId,$sid)
	{
		$user = Users::findFirst(['conditions'=>'id = '.$uid])->toArray();
		$openId = $user['openId'];

		$shopSlide = VipShopSlide::findFirst(['conditions'=>'sid = '.$sid])->toArray();
		$shop = VipShop::findFirst(['conditions'=>'id = '.$sid])->toArray();
		$dishes = $shopSlide['dishes'];
		$shopName = $shop['shop_name'];

		$data_arr = ['keyword1'=>['value'=>$shopName],'keyword2'=>['value'=>$dishes],'keyword3'=>['value'=>date('Y-m-d H:i:s')],'keyword4'=>['value'=>time()],
						'keyword5'=>['value'=>'已使用'],'keyword6'=>['value'=>'尊敬的Vip会员，本次专享将由'.$shopName.'提供，精选'.$dishes.'供您享用,感谢您的支持!']];

		$a = new WeixinUsers();
		$access_token = $a->access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;

		$post_data = array (
		  "touser"           => $openId,
		  //用户的 openID，可用过 wx.getUserInfo 获取
		  "template_id"      => 'p2gqXzyAz5JpErMZfWR0Smgi1bbGNZd60Z832Y53d8M',
		  //小程序后台申请到的模板编号
		  // "page"             => "/pages/check/result?orderID=".$orderID,
		  //点击模板消息后跳转到的页面，可以传递参数
		  "form_id"          => $formId,
		  //第一步里获取到的 formID
		  "data"             => $data_arr,
		  "emphasis_keyword" => "keyword1.DATA"
		  //需要强调的关键字，会加大居中显示
		);

		$data = json_encode($post_data, true);

		$return = $this->send_postAction( $url, $data);
		return $return;

	}



	function send_postAction( $url, $post_data ) {
		/*服务通知，需要通过formid启动*/
	  $options = array(
	    'http' => array(
	      'method'  => 'POST',
	      'header'  => 'Content-type:application/json',
	      //header 需要设置为 JSON
	      'content' => $post_data,
	      'timeout' => 60
	      //超时时间
	    )
	  );

	  $context = stream_context_create( $options );
	  $result = file_get_contents( $url, false, $context );

	  return $result;
}










}
