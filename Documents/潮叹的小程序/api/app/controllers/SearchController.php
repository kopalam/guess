<?php
use Phalcon\Mvc\Controller;

class SearchController extends Controller
{
	/*

		我的使用记录
		BuyLogs 购买记录
	*/

			function keyWordListAction()
			{

				/*

					根据店名，搜索出对应商家

				*/

				$token 	=	$this->request->getPost('token');

				$author = new Authory( $token );
			    $author->loggingVerify();


			    $keyWord = $this->request->getPost('keyword');
			    //查询uselog表中，关于该uid的记录
			    $find = ['conditions'=>"shop_name LIKE '%$keyWord%'"];

			    $result = VipShop::find($find)->toArray();

			    if(empty($result))
			    	Utils::JsonError(1,'这里什么都没有找到呢！请输入 商家名 看看@_@~');
			    
			    $shopData = [];

		    foreach ($result as $key => $value) {

		    	$shopData[$key]['shopId']	=	$value['id'];//商家id
		    	$shopData[$key]['shopName']	=	$value['shop_name'];//商家名称
		    	$shopData[$key]['location']	=	$value['lat'].','.$value['lng'];//商家坐标
		    	$shopData[$key]['useRule']	=	$value['use_rule'];//使用规则
		    	$shopData[$key]['telPhone']	=	$value['telphone'];//商家电话
		    	$shopData[$key]['address']	=	$value['address'];//商家电话
		    	$shopData[$key]['dates']	=	date('Y-m-d H:i',$value['dates']);//发布日期
		    	$shopData[$key]['status']	=	$value['status'];//如果状态为1，则直接不显示
		    	$finder = ['conditions'=>'sid = '.$value['id']];
		    	$shopSlide = VipShopSlide::find( $finder )->toArray();
		    	foreach ($shopSlide as $k => $v) {
		    		$shopData[$key]['imageUrl'] = $v['slide'];
		    		$shopData[$key]['cons']		=	$v['cons'];//人均
		    		$shopData[$key]['dishes']		=	$v['dishes'];//人均
		    		$shopData[$key]['price']	=	$v['price'];//招牌菜价格
		    	}

		    }

			    Utils::apiDisplay(['status'=>0,'data'=>$shopData]);
			   
			}
}