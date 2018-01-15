<?php

	/**
	* 
	*/
	class AnswerAjax extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			$this->load->library(array('weixin','session','Jssdk'));
        	$this->load->helper(array('url','form'));
			$this->load->model(array('/vip/VipModel','/answer/AnswerModel','General'));
		}

		 function loadGame()
	        {
	        	/*
	        		加载游戏所需资源
	        		@首次加载，五道题目都读取出来,减少频繁请求数据库
	        		@随机读取题库中五道不重复的题目
	        		@随机读取random库的15个答案
	        		@首次加载，15个随机答案都读取出来,并插入到5道题目的键值中
	        	*/
	        		$userData = $this->input->post(array('collection_id','uid','nickname','subscribe'),TRUE);

	        		/*把用户记录写入prize_data表,初始化用户*/
	        		$userId = array('score'=>0,'right'=>0,'status'=>0,'click'=>1,'dates'=>date('Y-m-d H:i:s'));
	        		$userData =  array_merge_recursive($userData,$userId);
	        		//查询该用户是否已经存在prize_data表，如果已经存在，则update clik次数
	        		$userMatch = $this->General->query('prize_data',array('collection_id'=>$userData['collection_id'],'uid'=>$userData['uid']));
	        		if(!empty($userMatch))
	        		{
	        			$userMatch = $userMatch[0];
	        			$user = $this->General->update('prize_data',array('collection_id'=>$userData['collection_id'],'uid'=>$userData['uid']),array('click'=>$userMatch['click']+1));//$tablename,$general,$table,$data,$update
	        		}else{
	        			$user = $this->General->insert('prize_data',$userData);
	        			}
	        		if($user < 1 ) echo json_encode('信息录入失败，请重新再试');

	        		//读取题目信息

	        		// $random	=	array(rand(1,2),rand(3,4),rand(5,6),rand(7,8),rand(9,10));
	        		$random = array('collection_id'=>$userData['collection_id']);

	        		$quesstion	=	$this->AnswerModel->quesstion('quesstion',$random,5); //随机读取问题库中的id段问题

	        		// for ($i=1; $i < 16 ; $i++) { 
	        		// 	/*随机读取15道错误答案*/
	        		// 	$randSearch[] = $this->AnswerModel->randomQuesstion('random',rand(1,30));

	        		// }
	        		$randSearch = $this->AnswerModel->quesstion('random',$random,15);
	        		
	        		// foreach ($randSearch as $key => $value) {
	        		// 	/*把错误答案转换为2维数组*/
	        		// 	foreach ($value as $k => $v) {
	        		// 		$anserRandom[] = $v;
	        		// 	}
	        		// }

	        		
	        			$quesstion[] = $randSearch;
	        			echo json_encode($quesstion);


	        }

	        function finishGame()
	        {
	        	/*
	        		完成游戏后的返回方法
	        	*/
	        	$userData = $this->input->post(array('uid','collection_id','score','right'));
	

	        	


	        		$findScore = $this->General->query('prize_data',array('collection_id'=>$userData['collection_id'],'uid'=>$userData['uid']));
	        		//
	        		$findScore = $findScore[0];
	        		$score = $findScore['score'];
	        		$right = $findScore['right'];
	        		//如果prize_num为空，则读取答题正确数对应奖品编号，并update到用户对应collection_id，再输出。如果奖品为0，显示 送光啦
	        		// if($findScore['prize_num'] == 0)
	        		// {

	        		// 	$prize_num = $this->General->query('prize_name',array('collection_id'=>$userData['collection_id'],'right'=>$right));

	        		// 	$prize_num = $prize_num[0]; //数组，collection_id,'prize_num','prize_name','total','right'
	        		// 	//更新到prize_data中，并在prize_name奖品中-1
	        		// 	$prize_name_total = $this->General->update('prize_name',array('collection_id'=>$userData['collection_id'],'right'=>$right),array('total'=>$prize_num['total']-1));
	        			
	        		// 	$this->General->update('prize_data',array('collection_id'=>$userData['collection_id'],'uid'=>$userData['uid']),
	        		// 									array('prize_num'=>$prize_num['prize_num'],'prize_name'=>$prize_num['prize_name']));
	        		// 	// $findScore['prize_num'] = $updatePrize['prize_num'];
	        		// 	// $findScore['prize_name'] = $updatePrize['prize_name'];

	        		// }
	        		
	        	
	        	
	        	switch ($right) {
	        		case 5:
	        				$num = 5;
	        			break;
	        		case 4:
	        				$num = 4;
	        			break;
	        		case 3:
	        				$num = 3;
	        			break;
	        		default:
	        				$num = 2;
	        			break;
	        	}


	        		

	        		
	        		$image = $this->General->query('title_image',array('right '=>$num)); //return 对应的id数组

	        		$image = $image[0];
	        		$updateData['image_id'] = $findScore['image_id'];
	        		$updateData['uid'] = $userData['uid'];
	        		if(empty($findScore['image_id']))
	        		{
	        		$this->General->update('prize_data',array('collection_id'=>$userData['collection_id'],'uid'=>$userData['uid']),array('image_id'=>$image['id']));
	        		}
	        		/*集合元素输出,$right,$score,prize_name,image_id,status*/
	        		if($num <= 2) 
	        			{
	        				$userFinishData['prize_name'] = '就差一点啊！！';
	        			}else{
	        				$userFinishData['prize_name']	=	$findScore['prize_name'];
	        			}
	        		$userFinishData['right'] = $right;
	        		$userFinishData['score'] = $score;
	        		
	        		$userFinishData['status']		=	$findScore['status'];
	        		$userFinishData['image_id']		=	$findScore['image_id'];
	        		echo  json_encode($userFinishData); //输出给前端
	        			


	        }

	        function getScore()
	        {
	        	/*游戏完成时，更新当前分数*/
	        	$userData = $this->input->post(array('uid','collection_id','score','right'));



	        	$score	=	$userData['score'];
	        	$right	= $userData['right'];
	        	// $updateData = array('click'=>1,'status'=>1);
	        	
	        	switch ($right) {
	        		case 5:
	        				$num = 5;
	        			break;
	        		case 4:
	        				$num = 4;
	        			break;
	        		case 3:
	        				$num = 3;
	        			break;
	        		default:
	        				$num = 2;
	        			break;
	        	}
	        	//根据分数插入image_id
	        	$image = $this->General->query('title_image',array('right'=>$num));
	        	$image = $image[0];
	        	//根据分数插入奖品
	        	$prize_num = $this->General->query('prize_name',array('collection_id'=>$userData['collection_id'],'right'=>$num));

	        	$prize_num = $prize_num[0]; 

	        			//更新到prize_data中，并在prize_name奖品中-1
	        	

	        	$user = $this->VipModel->GeneralUpdate('prize_data',array('uid'=>$userData['uid'],'collection_id'=>$userData['collection_id']),array('status'=>1,'right'=>$right,'score'=>$score,'image_id'=>$image['id'],'prize_num'=>$prize_num['prize_num'],'prize_name'=>$prize_num['prize_name'],'dates'=>date('Y-m-d H:i:s'))); //更新状态，题目，秒数，图片id

	        	// print_r($user);exit();
	        	if($user == 1)
	        	{	
	        		if($prize_num['total'] == 0) {
	        			$this->VipModel->GeneralUpdate('prize_data',array('uid'=>$userData['uid'],'collection_id'=>$userData['collection_id']),array('prize_num'=>$prize_num['prize_num'],'prize_name'=>'已被抢光了..')); 
	        		}else{
	        			$this->General->update('prize_name',array('collection_id'=>$userData['collection_id'],'right'=>$num),array('total'=>$prize_num['total']-1));
	        		}
	        		
	        		echo json_encode(array('message'=>'更新成功','status'=>1));
	        	}else{
	        		echo json_encode(array('message'=>'出现了未知的小问题哦','status'=>0));
	        	}
	        	
				
	        }

	        function rankingList()
			{
				$collection_id = $this->input->post('collection_id');
				// $uid = 3581;
				// $user = $this->AnswerModel->userRanking('prize_data',$uid);
				// print_r($user);
				//查询 prize_data表中，6个时间最少，正确数最多的用户
				$rankUser = $this->AnswerModel->rankingList('prize_data',array('score <'=>20,'right<='=>5,'collection_id'=>$collection_id));
				
			

				array_multisort($rankUser,SORT_ASC);

				echo json_encode($rankUser);
			}


	        function General($tablename,$general,$table,$data)
			{
				/*
					通用数据库查询
					@tablename model名
					@general   model的方法名
					@table 数据表名
					@data 查询内容
				*/
				$data = $this->$tablename->$general($table,$data);
				return $data;
			}

			function Update($tablename,$general,$table,$data,$update)
			{
				/*
					通用数据库查询
					@tablename model名
					@general   model的方法名
					@table 数据表名
					@data 查询内容
				*/
				$data = $this->$tablename->$general($table,$data,$update);
				return $data;
			}

			function fidReturn($data)
			{
				//检测表中是否存在该关系，如果是，update clicknum+1
				$data['dates'] = date('YmdHis');
				$data['click_num'] = 1;
				$match = $this->General->query('friend_power_user',array('uid'=>$data['uid'],'fid'=>$data['fid'],'collection_id'=>$data['collection_id']));
				if(!empty($match))
				{
					$match = $match[0];

					//更新clicknum
					$user = $this->General->update('friend_power_user',$match,array('click_num'=>$match['click_num']+1,'dates'=>date('YmdHis')));
					return $user;
				}else{
					$friendInsert =  $this->General('VipModel','GeneralInsert','friend_power_user',$data);
	        	return $friendInsert;
				}

	        	
			}

			function userStatusAjax()
			    {


			    	$user_password	=	$this->input->post(array('uid','collection_id','password'),TRUE);

			    	
						// $userPassword 	=	'888888';

						switch ($user_password['password']) {
							case '888888':
								$update = $this->General->update('prize_data',array('collection_id'=>$user_password['collection_id'],'uid'=>$user_password['uid']),array('status'=>2));
								// echo $update;
								echo json_encode(array('status'=>0,'message'=>'核销成功'));
								break;
							
							default:
								echo(json_encode(array('status'=>1,'message'=>'密码不正确')));
								break;
						}

						/*if($user_password['password'] != $userPassword ) echo(json_encode(array('status'=>1,'message'=>'密码不正确')));
						
						 $update =	$this->General->update('prize_data',array('collection_id'=>$user_password['collection_id'],'openid'=>$user_password['openid']),array('status'=>2));
						 if($update == 1){

						 	echo json_encode(array('status'=>0,'message'=>'核销成功'));
						 }else{
						 	echo json_encode(array('status'=>2,'message'=>'网络出错了'));
						 }*/
						
		}

			

	}
















