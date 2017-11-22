<?php
use Phalcon\Mvc\Controller;

class OrdersController extends Controller
{
	/*

		我的购买记录与订单列表
		BuyLogs 购买记录
	*/

			function myOrdersAction()
			{
				$token 	=	$this->request->getPost('token');

				$author = new Authory( $token );
			    $author->loggingVerify();

			    //分页开始
			    $uid 	=	$this->request->getPost('uid');
				$page 	=	$this->request->getPost('page');

			    if(empty($page))
			    	$page = 1;

			    $size = 6;//一次读取20条信息

				$skip = (intval($page)-1)*$size;

				$parameters	=	['limit' => $size,'offset'=>$skip,'order'=>'id desc'];
				$parameters['conditions'] = 'uid = '.$uid;

				$findOrder 	=	BuyLogs::find( $parameters )->toArray();
				if(empty($findOrder))
						Utils::jsonError(1,'没有更多记录了 ~_~');
				$orderData = [];

				

				foreach ($findOrder as $key => $value) {
					$orderData[$key]['id']  =	$value['id'];
					$orderData[$key]['dates'] = date('Y-m-d h:i:s',$value['dates']);
					$orderData[$key]['day']  =	$value['days'];
					$orderData[$key]['status']  =	$value['status'];
				}

			
				    Utils::apiDisplay( ['status'=>0,'data'=>$orderData] );
			}
}