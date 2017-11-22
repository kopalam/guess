<?php
use Phalcon\Mvc\Controller;

class ViptypeController extends Controller
{
	/*

		vip的类型
	*/

			function typesAction()
			{

				/*

					根据店名，搜索出对应商家

				*/

				$token 	=	$this->request->getPost('token');

				$author = new Authory( $token );
			    $author->loggingVerify();

			    $find 	=	['conditions'=>'status = 0'];
			    $find['order'] = 'money asc';
			    $vip 	=	VipPrice::find($find)->toArray();

			    foreach ($vip as $key => $value) {
			    	$vipData[$key]['id'] 	=	$value['id'];
			    	$vipData[$key]['typesName']	=	$value['types'];
			    	$vipData[$key]['oprice']	=	$value['oprice'];
			    	$vipData[$key]['price']	=	$value['money'];
			    	$vipData[$key]['contents']	=	$value['contents'];
			    	$vipData[$key]['day']	=	$value['days'];

			    }

			    Utils::apiDisplay(['status'=>0,'data'=>$vipData]);
			   
			}
}