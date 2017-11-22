<?php

use Phalcon\Mvc\Controller;

class UserstestController extends Controller
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

			echo json_encode($userData);exit();

			/*检查user表是否存在该用户，如果存在，则返回session，如果不存在，写入表再发回session*/
			$openId = $userData['openId'];
			$unionId = $userData['unionId'];

			 $paramers = ['conditions' => "unionId = '".$unionId."'"];

			$has_user 	= 	Users::findFirst( $paramers );
			$getUser = ['user_id'=>$has_user->id,'unionId'=>$has_user->unionId,'openId'=>$has_user->openId];

			if($has_user->openId == $userData['openId'])
			{
				$getUser = ['user_id'=>$has_user->id,'unionId'=>$has_user->unionId,'openId'=>$has_user->openId];
			}

			if(!$unionId)
				Utils::jsonError(1,'参数错误');

			if(!$has_user)
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

				// $vip = new Viper;
				// $vip->levels 	=	0;
				// $vip->uid 		=	$user->id;
				// $vip->status 	=	0;
				// $vip->stime 	=	0;
				// $vip->etime 	=	0;
				// $vip->save();


			}

			$getUser['token']=$userData['session3rd'];

			/*查询用户在viper中的对应等级*/
			$find 	=	['conditions'=>'uid = '.$getUser['user_id']];
			echo json_encode($find);exit();
			$viper 	=	Viper::findFirst( $find );
			$getUser['vipStatus'] = ['level'=>intval($viper->levels),'stime'=>$viper->stime == 0?0:date('Y-m-d',$viper->stime),'etime'=>$viper->etime == 0?0:date('Y-m-d',$viper->etime)];

			$cache = new Cache;
			$cache->set($userData['session3rd'],json_encode($userData['sessionKey'].'='.$unionId),86400*10);
			$this->view->disable();
			Utils::apiDisplay(['status'=>0,'data'=>$getUser]);
 	

	}


	function provingAction()
	{

		$token = $this->request->getPost('token');
		$author = new Authory( $token );
		$author->loggingVerify();

		$uid = $this->request->getPost('uid');
		$sid = $this->request->getPost('sid');
		$sole = $this->request->getPost('sole');
		$formId = $this->request->getPost('formId');

		//确认是否会员，如果是会员则可以继续认证
		$finder = ['conditions'=>'levels > 0 and uid = '.$uid];
		$vip = Viper::findFirst( $finder )->toArray();
		if(empty($vip))
			Utils::jsonError(1,'你还不是会员，不能使用噢~_~');

		$use = new UseLogs;
		$use->uid = $uid;
		$use->sid = $sid;
		$use->dates = time();
		$use->status = 0;
		$use->sole = $sole;
		$use->save();

		if($use->save() == false)
			Utils::jsonError(1,'使用记录出现问题了，请再试一次-_-');

		//验证成功，vipshop 的amount-1
		$shop = VipShop::findFirst(['conditions'=>'id = '.$sid]);
		$shop->amount = $shop->amount - 1;
		$shop->save();
			if($shop->save() == false)
				Utils::jsonError(1,'验证出现未知错误，请再试一次-_-');

		$message = $this->sendMessageAction($uid,$formId,$sid);

		Utils::jsonSuccess('验证成功！祝您用餐愉快');
	}

	function checkVipAction()
	{
		$token = $this->request->getPost('token');
		$author = new Authory( $token );
		$author->loggingVerify();

		$uid = $this->request->getPost('uid');
		$vip = Viper::findFirst(['conditions'=>'uid ='.$uid])->toArray();

		Utils::apiDisplay(['status'=>0,'etime'=>$vip['etime']==0?0:date('Y-m-d',$vip['etime']),'levels'=>intval($vip['levels'])]);
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

	// function testAction()
	// {
	// 	$message = $this->sendMessageAction($uid,$formId);
	// }

	// function updateMessageAction($uid,$formId,$sid)
	// {	
	// 	$user = Users::findFirst(['conditions'=>'id = '.$uid])->toArray();
	// 	$openId = $user['openId'];

	// 	$shopSlide = VipShopSlide::findFirst(['conditions'=>'sid = '.$sid])->toArray();
	// 	$shop = VipShop::findFirst(['conditions'=>'id = '.$sid])->toArray();
	// 	$dishes = $shopSlide['dishes'];
	// 	$shopName = $shop['shop_name'];

	// 	$data_arr = ['keyword1'=>['value'=>date('Y-m-d H:i:s')],'keyword2'=>['value'=>'今天去哪几家小店打卡啦？成为VIP的你怎么能不好好享受这份闲暇时光！']];

	// 	$a = new WeixinUsers();
	// 	$access_token = $a->access_token();
	// 	$url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;

	// 	$post_data = array (
	// 	  "touser"           => $openId,
	// 	  //用户的 openID，可用过 wx.getUserInfo 获取
	// 	  "template_id"      => '7aOmHcFWtNRF92B-_tY1SJcIJWWCJw1bnTyhgXocMTE',
	// 	  //小程序后台申请到的模板编号
	// 	  // "page"             => "/pages/check/result?orderID=".$orderID,
	// 	  //点击模板消息后跳转到的页面，可以传递参数
	// 	  "form_id"          => $formId,
	// 	  //第一步里获取到的 formID
	// 	  "data"             => $data_arr,
	// 	  "emphasis_keyword" => "keyword2.DATA"
	// 	  //需要强调的关键字，会加大居中显示
	// 	);

	// 	$data = json_encode($post_data, true);  

	// 	$return = $this->send_postAction( $url, $data);
	// 	return $return;

	// }



	function send_postAction( $url, $post_data ) {
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


	function getAccessTokenAction()
	{
		$a = new WeixinUsers();
		$access_token = $a->access_token();
		echo json_encode($access_token);
	}

	// function getcacheAction()
	// {
	// 	$getCache = 'shopList';
	// 	$cache = new Cache;
	// 	$get = $cache->get($getCache);
	// 	echo json_encode($get);
		
	// }
	// function delcacheAction()
	// {
	// 	$getCache = 'shopList';
	// 	$cache = new Cache;
	// 	$get = $cache->remove($getCache);
	// 	echo json_encode($get);
		
	// }
	// function insertVipAction()
	// {
	//导入user的旧数据
	// 	$user 	=	Users::find()->toArray();
		

	// 	foreach ($user as $key => $value) {
	// 		$find = ['conditions'=>'uid = '.$value['id']];
	// 		$get  = Viper::find($find)->toArray();
	// 		if(empty($get))
	// 		{
	// 				$data = new Viper;
	// 				$data->levels 	=	0;
	// 				$data->uid 		=	$value['id'];
	// 				$data->stime 	=	0;
	// 				$data->etime 	=	0;
	// 				$data->status 	=	1;
	// 				$data->save();
	// 				// if($data->save()==false)
	// 				// 		echo '出错了';exit();
	// 		} 
					

			

	// 	}
	// 	echo 'ok';
	// }

	// 	public function userAddTopicAction()
	// {
	// 	/*
	// 		用户发布过的内容
	// 		@
	// 	*/
	// 			$token = $this->request->getPost('session3rd');
 // 				$author = new Authory( $token );
	// 	        $author->loggingVerify();

	// 	        //提取用户在issued表中的资料
	// 	        $userId 	=	$this->request->getPost('userId');
	// 	        $userData = array();
	// 	        $finder 	=	['conditions'=>"user_id = ".$userId.' and status = 0','order' => 'dates desc'];
	// 	        $issuedData =	Issued::find( $finder )->toArray();
	// 	        // $user = Users::findFirst($userId);
	// 	        // Utils::apiDisplay(['data'=>$issuedData]);
	// 	        if( !$issuedData )
	// 	        	Utils::apiDisplay(['message'=>'你还没有发布过小报告~']);

	// 	        foreach ($issuedData as $key => $value) {
		        	 
	// 	        	 $types = Types::findFirst( $value['types_id'] );

	// 	        	 $userData[$key]['userId'] = $value['user_id'];
	// 	        	 $userData[$key]['topicId'] = $value['id'];
	// 	        	 // $userData[$key]['title'] = $value['title'];
	// 	        	 $userData[$key]['contents'] = $value['contents'];
	// 	        	 $userData[$key]['types'] = $types->types_name;
	// 	        	 $userData[$key]['dates'] = $value['dates'];



	// 	        	 $findImg 	=	['conditions'=>"topic_id = ".$value['id']];
	// 	        	 $slide = IssuedSlide::find($findImg)->toArray();
	// 	        	 //重组数组
	// 	        	 foreach ($slide as $k => $v) {
	// 	        	 	$userData[$key]['images'] = $v['slide'];
	// 	        	 }
		        	
	// 	        }
	// 	        Utils::apiDisplay(['status'=>0,'message'=>'OK','data'=>$userData]);


	// }

	// public function userLikeAction()
	// {
	// 	/*

	// 		我点赞过的

	// 	*/
	// 		$token = $this->request->getPost('session3rd');
 // 			$author = new Authory( $token );
	// 	    $author->loggingVerify();

	// 	        //提取用户在like表中的资料
	// 	     $userId 	=	$this->request->getPost('userId');
	// 	      $page 	=	$this->request->getPost('page');
	// 			if(!$page)
	// 				$page = 1;

	// 			$size = 6;//一次读取20条信息

	// 			$skip = (intval($page)-1)*$size;

	// 		 $finder		=	['limit' => $size,'offset'=>$skip,'order'=>'dates desc'];
	// 	     $finder['conditions'] 	=	"uid = ".$userId.' and likes = 1';
	// 	     $likes  	=	ArticleLike::find( $finder )->toArray();
	// 	     $issuedLikes 	=	array();
	// 	     	if(!$likes)
	// 	     		Utils::apiDisplay(['status'=>0,'message'=>'你还没有赞过谁的小报告']);

	// 	     foreach ($likes as $key => $value) {
	// 	     	$paramers 	=	['conditions'=>"id = ".$value['article_id']];
	// 	     	$userIssued 	=	Issued::find( $paramers )->toArray(); 
	// 	     	foreach ($userIssued as $k => $v) {
	// 	     		$issuedLikes[$key]['userId'] 	=	$v['user_id'];
	// 	     		$issuedLikes[$key]['topicId'] = $v['id'];
	// 	     		$issuedLikes[$key]['contents'] = $v['contents'];
	// 	     		$issuedLikes[$key]['dates'] = date('Y-m-d H:i:s',$value['dates']);

	// 	     	 $findImg 	=	['conditions'=>"topic_id = ".$value['id']];
	// 	       	 $slide = IssuedSlide::find($findImg)->toArray();

	// 	       	  foreach ($slide as $i => $m) {
	// 	        	 	$issuedLikes[$key]['images'] = $m['slide'];
	// 	        	 }

	// 	     	}
		     	
	// 	     }

	// 	     Utils::apiDisplay(['status'=>0,'message'=>'OK','data'=>$issuedLikes]);
	// }


	// 		public function userCommentsAction()
	// 		{
	// 			/*我的评论*/
	// 			$token = $this->request->getPost('session3rd');
 // 				$author = new Authory( $token );
	// 	        $author->loggingVerify();

	// 	        $userId = $this->request->getPost('userId');
	// 	        $page 	=	$this->request->getPost('page');
	// 			if(!$page)
	// 				$page = 1;

	// 			$size = 6;//一次读取20条信息

	// 			$skip = (intval($page)-1)*$size;

	// 			$parameters		=	['limit' => $size,'offset'=>$skip,'order'=>'dates desc'];
	// 			$parameters['conditions'] 	=	"uid = ".$userId;

	// 			$commentsData = Comments::find( $parameters )->toArray();
	// 			$commentsList = array();
	// 			if(empty($commentsData))
	// 				Utils::apiDisplay( ['status'=>0,'message'=>'还没有高谈阔论过~'] );
	// 			// print_r($commentsData);exit();
	// 			foreach ($commentsData as $key => $value) {
					
	// 				$commentsList[$key]['dates'] 	=	$value['dates'];
	// 				$commentsList[$key]['comments'] 	=	$value['contents'];

	// 				$articleData 	=	['conditions'=>'id = '.$value['article_id']];
	// 				$topicData 		=	Issued::find( $articleData )->toArray();

	// 					foreach ($topicData as $k => $v) {
	// 						$commentsList[$key]['topicId'] 	=	$v['id'];
	// 						$commentsList[$key]['contents'] 	=	$v['contents'];
	// 						$commentsList[$key]['userId'] 	=	$userId;
							
	// 					}
	// 			}

	// 			Utils::apiDisplay(['status'=>0,'data'=>$commentsList]);

	// 		}










}
