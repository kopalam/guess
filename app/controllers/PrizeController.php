<?php

use Phalcon\Mvc\Controller;
// Phalcon\Debug::log("DEBUG Message");

class PrizeController extends Controller
{
	function prizeCountAction()
	{
		/*

			通过该接口，获取对应活动sid最新的奖品总数

		*/
			$token 	=	$this->request->getPost('token');

				$author = new Authory( $token );
				$author->loggingVerify();

			$sid 	=	$this->request->getPost('sid');

			$prize 	=	Prize::findFirst(['conditions'=>'id = '.$sid]);

			$prizeAmount 	=	$prize->prize_amount;

			$onlineUser 	=	PrizeLogs::count("sid = ".$sid);
			intval($onlineUser);
			$data 	=	['amount'=>$prizeAmount,'onlineUser'=>$onlineUser];

			Utils::apiDisplay(['status'=>0,'data'=>$data]);

	}
}