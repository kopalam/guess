<?php

use Phalcon\Mvc\Controller;
// Phalcon\Debug::log("DEBUG Message");

class GuessController extends Controller
{

	function getListAction()
	{
		/*
			活动列表
			@获取collection表中status=0的活动
			@获取prize表中，sid = collection中的id

		*/
		$token 	=	$this->request->getPost('token');

			$author = new Authory( $token );
			$author->loggingVerify();

		$uid 	=	$this->request->getPost('uid');

		$collection 	=	Collection::find(['conditions'=>'status = 0'])->toArray();

		foreach ($collection as $key => $value) {
			$prize 		=	Prize::find(['conditions'=>'status = 0 and sid = '.$value['id']])->toArray();
			$data[$key]['onlineUser']=	PrizeLogs::count('uid');
			$data[$key]['prizeName'] 	=	$value['name'];
			$data[$key]['sid'] 	=	$value['id'];


			foreach ($prize as $k => $v) {
				$data[$key]['prizeName'] 	=	$v['prize_name'];
				$data[$key]['prizeAmount'] 	=	$v['prize_amount'];

			}

		}

			Utils::apiDisplay(['status'=>0,'data'=>$data]);


	}

	function getQuestionAction()
	{
		/*
		
			@获取题目方法
			@必传参数： token uid sid
			先查询memcached中是否已经缓存该sid的题目，如果已经缓存，则直接读取缓存中的题目，如果没有，则查询一遍，并缓存进memcached，并输出。

		*/
			$token 	=	$this->request->getPost('token');

			$author = new Authory( $token );
			$author->loggingVerify();

			$uid 	=	$this->request->getPost('uid');
			$sid 	=	$this->request->getPost('sid');

			$findPrizeLogs 	=	['conditions'=>'sid = '.$sid.' and uid = '.$uid];//检测该玩家是否已经参与过此sid的游戏，如果是，则跳转到结果页面，否，则写入prize表，并继续
			$userLogs 	=	PrizeLogs::find( $findPrizeLogs )->toArray();

			if(empty($userLogs))
			{
				//先创建用户记录
				$userPrize 	=	new PrizeLogs;
				$userPrize->sid 	=	$sid;
				$userPrize->uid 	=	$uid;
				$userPrize->status 	=	0;
				$userPrize->save();
			}

			if($userLogs[0]['finish_time']>0)
			{
				Utils::jsonError(1,'已参与',['finishTime'=>$userLogs[0]['finish_time'],'result'=>$userLogs[0]['result']]);
			}

				$questionData 	=	new GuessLib;
				$data	=	$questionData->quesstion($sid,$uid);


		
			Utils::apiDisplay(['status'=>0,'data'=>$data]);


	}

	function finishQuestionAction()
	{	
		/*

			@答题结束
			@必传参数： token uid sid finishTime result(最后结果，全对是1，错了传 0 )
			先查询该用户在logs表中的记录，查找对应时间的奖品，更新logs表中。
		*/

			$token 	=	$this->request->getPost('token');
			$author = new Authory( $token );
			$author->loggingVerify();

			$uid 	=	$this->request->getPost('uid');
			$sid 	=	$this->request->getPost('sid');
			$finishTime 	=	$this->request->getPost('finishTime');
			$result 	=	$this->request->getPost('result');

			if($result == 0)
			{
				$findPrizeLogs 	=	['conditions'=>'sid = '.$sid.' and uid = '.$uid];//检测该玩家是否已经参与过此sid的游戏，如果是，则跳转到结果页面，否，则写入prize表，并继续
				$userLogs 	=	PrizeLogs::findFirst( $findPrizeLogs );
				$userLogs->finish_time 	=	$finishTime;
				$userLogs->prize_amount 	=	0;
				$userLogs->pid 	=	0;
				$userLogs->result 	=	0;
				$userLogs->dates 	=	time();
				$userLogs->save();

				Utils::apiDisplay(['status'=>0,'message'=>'距离成功还有一段的距离啊-_-']);
			}
			try{
				$prizeData = new GuessLib;
			$data 	=	$prizeData->finish($uid,$sid,$finishTime,$result);
			}catch(Exception $e){
	            $data['status']  = 1;
	            $data['message'] = $e->getMessage();
	            Utils::apiDisplay( $data );
        }
			

			Utils::apiDisplay(['status'=>0,'data'=>$data]);

	}





}








