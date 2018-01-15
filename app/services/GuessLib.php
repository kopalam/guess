<?php

class GuessLib  {

	function quesstion($sid,$uid)
	{
		/*
			@题目缓存前序，sid+活动码+uid+用户uid
			@查询quesstion表对应的sid
			@查询selects表对应的sid
			@合并一道题目包含标题，图片，3道选项+1道正确的选项

		*/
		$findQuestion 	=	['conditions'=>'status=0 and sid = '.$sid];
		$questionData 	=	Question::find( $findQuestion )->toArray();
		$data 	=	array();
		$findSelects 	=	['conditions'=>'status=0 and sid = '.$sid];
		$selectsData 	=	Selects::find( $findSelects )->toArray();

		$data['question'] = $questionData;
		$data['selects']	=	$selectsData;


		return $data;

	}


	function finish($uid,$sid,$finishTime,$result)
	{
		//查找奖品
			$findPrize 	=	['conditions'=>' sid = '.$sid];
			$userPrize 	=	Prize::findFirst( $findPrize );

			if(empty($userPrize))
				 throw new Exception("奖品库中没有奖品记录", 10001);
			if($userPrize->prize_amount == 0)
				 throw new Exception("本期奖品已送光啦", 10002);

			$findLogs 	=	['conditions'=>'sid = '.$sid.' and uid = '.$uid];
			$userLogs 	=	PrizeLogs::findFirst( $findLogs );

			if($userLogs->prize_amount > 0)
				{
					$data = ['uid'=>$uid,'prize'=>$userPrize->prize_name,'finishTime'=>$userLogs->finish_time,'result'=>$userLogs->result];
					return $data;

				}


			$userLogs->pid 	=	$userPrize->id;
			$userLogs->prize_amount 	=	1;
			$userLogs->result 	=	$result;
			$userLogs->dates 			=	time();
			$userLogs->finish_time 		=	$finishTime;
			$userLogs->save();	

			$userPrize->prize_amount 	=	$userPrize->prize_amount - 1;
			$userPrize->save();

			$data = ['uid'=>$uid,'prize'=>$userPrize->prize_name,'finishTime'=>$userLogs->finish_time];

			return $data;
	}

}