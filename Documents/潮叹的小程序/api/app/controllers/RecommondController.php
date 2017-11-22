<?php

use Phalcon\Mvc\Controller;

class RecommondController extends Controller
{

	/*推荐好友类*/
	function userSaleAction()
	{
		/*

			推荐有礼，user页面
			@uid  用户id
			@fid  朋友id
			@cash 充值金额
			@friendNo	朋友充值的out_trad_no

		*/
		$token = $this->request->getPost('token');
		$author = new Authory( $token );
		$author->loggingVerify();

		$uid 	=	$this->request->getPost('uid');
		$fid 	=	$this->request->getPost('fid'); //朋友的id
		$cash 	=	$this->request->getPost('cash');//朋友支付成功金额
		$friendNo = $this->request->getPost('out_trade_no');//friend out_trade_no

		$find = ['conditions'=>'uid = '.$uid.' and fid = '.$fid];
		$findSale = Sale::find( $find )->toArray(); //查找是否已经邀请过

		if(!empty($findSale))
			Utils::jsonError(1,'该朋友已邀请过');
// print_r($findSale);exit();
		$sale = new Sale();
		$sale->uid = $uid;
		$sale->fid = $fid;
		$sale->cash = $cash;
		$sale->out_trade_no = $friendNo;
		$sale->dates = time();
		$sale->status = 0;
		$sale->save();

		$parm = ['conditions'=>'status = 0 and uid = '.$uid];
		$userDates = Viper::findFirst($parm);
		$userDates->etime = $userDates->etime+3600*24*7;
		$userDates->save();

		if($userDates->save() == false)
			Utils::jsonError(1,'出错了');

		$userBuyLogs 	=	BuyLogs::findFirst($parm);
		$userBuyLogs->dates = $userBuyLogs->dates+3600*24*7;
		$userBuyLogs->days = $userBuyLogs->days+7;
		$userBuyLogs->rtime = $userBuyLogs->rtime+7;
		$userBuyLogs->save();
		if($userBuyLogs->save() == false)
			Utils::jsonError(1,'出错了');

		Utils::jsonSuccess('ok');

	}

	function helpFriendAction()
	{

		/*推荐过购买的朋友*/
		$token = $this->request->getPost('token');
		$author = new Authory( $token );
		$author->loggingVerify();

		$uid	=	$this->request->getPost('uid');
		$find 	=	['conditions'=>'status = 0 and uid = '.$uid];
		$help 	=	Sale::find($find)->toArray();
		$data = array();
		// print_r($help);exit();
		if(empty($help))
			Utils::jsonError(1,'还没有好友成为其中一员呢@-@');

		//查找已有好友fid的对应头像和名称
		foreach ($help as $key => $value) {
			$parm 	=	['conditions'=>'id = '.$value['fid'].' and status = 0'];
			$fData	=	Users::find($parm)->toArray();

			foreach ($fData as $k => $v) {
				$data[$key]['nickName'] = $v['nickName'];
				$data[$key]['headImg'] = $v['avatarUrl'];
			}
		}

		Utils::apiDisplay(['status'=>0,'data'=>$data]);



	}


















}