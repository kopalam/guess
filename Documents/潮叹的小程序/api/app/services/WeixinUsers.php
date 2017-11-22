<?php

	Class WeixinUsers {

		protected $appId;
		protected $appSecret;



		public function __construct()
		{
			 $id = 1;
        	 $cauth = Cauth::findFirst( $id );
        	 $appid = base64_decode($cauth->appid);
        	 $appsecret = base64_decode( $cauth->appsecret );
        	 $this->appId = $appid;
        	 $this->appSecret = $appsecret;

		}

		public function getCode($redirect_uri)
		{
			$code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appId&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect ";

			return $code;
		}

		public function access_token()
		{
			/*获取到的code换取access_token和openid*/
			$id = 1;

			$post_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx68d51a56026931d3&secret=59578d7d3c3bf4592ac6ca8e1e046dc2";
			// $return = $this->postdata($post_url);
			$return = json_decode(file_get_contents($post_url));
			// return $return->access_token;exit();
			
			$access_token = $return->access_token;
			/*如果token未过期，则继续使用token，过期后再重新求新的token*/
			$check = Cauth::findFirst( $id );//查询token是否存在或是否过期
			$token = $check->token;
			$dates = $check->dates+3600;
			$nowTime = time();
			if(empty($token) || $dates < $nowTime)
			{
				$check->access_token = $access_token;
				$check->dates = time();
				$check->save();
			}

			// $openid = $return['openid'];
			
			/*获取微信用户数据*/
			// $get_userinfo = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
			// $userinfo = json_decode(file_get_contents($get_userinfo));
			
			return $access_token;
		}

		function get_subscribe($openid)
		{
			/*
				通过用户openid拉取用户信息中subscribe,0为未关注，1为已关注
			*/

				$acc_token = json_decode(file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}"));
				$acc_token = $acc_token->access_token;
				// return $acc_token;
				$get_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$acc_token}&openid={$openid}&lang=zh_CN";
				$subscribe_obj = json_decode(file_get_contents($get_url));
				$subscribe 	   =	$this->objtoarr($subscribe_obj);

				return $subscribe['subscribe'];

		}

		public function postdata($url)
		{
			 header('Content-Type:text/html;charset=utf-8');
		
		    $curl = curl_init();
		    curl_setopt($curl, CURLOPT_URL, $url);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		    curl_setopt($curl, CURLOPT_SSLVERSION, 1);
		
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    $output = curl_exec($curl);
		    curl_close($curl);
		   
		    $access = json_decode($output,true);
		    return $access;
		}

		public function objtoarr($data)
		{
			// 进行转换成数组 使用 obj_to_arr方式
			$data = is_object($data)?get_object_vars($data):$data;
				foreach ($data as $key => $value) 
				{
					
					$arr[$key] = $value;
				}
				return $arr;
		}

		public function findUser($unionid)
		{
			$find 	=	['conditions'=>"unionid = '".$unionid."'"];
			$userData 	=	Users::findFirst( $find );
			$userData   = empty($userData) ? 0 : $userData;
			return $userData;
		}

	}