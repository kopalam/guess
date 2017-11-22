<?php
use Phalcon\Mvc\Controller;

class ShoplistController extends Controller
{

	/*

		获取商家的必要资料
		@token为每个方法的必填值，校验是否已经登录再进行下一步操作

	*/



		function getCardAction()
		{
			$data = VipCard::findFirst(1);
			$image = $data->images;

			Utils::jsonSuccess('ok',$image);

		}

		function getShopListAction()
		{
			//校验token，是否在缓存中，如果不在则重新登录
			$token 	=	$this->request->getPost('token');

			$author = new Authory( $token );
		    $author->loggingVerify();

		    //分页开始
		    $page 	=	$this->request->getPost('page');

		    if(empty($page))
		    	$page = 1;

		    $size = 8;//一次读取20条信息

				$skip = (intval($page)-1)*$size;

				$parameters	=	['limit' => $size,'offset'=>$skip,'order'=>'dates desc'];
				$parameters['conditions'] = 'status = 0 and amount >0';
				

		    $shop 	=	VipShop::find( $parameters )->toArray();
		    if(empty($shop))
		    	Utils::jsonError(1,'你触碰到我的底线了，没有更多了@_@');
		    $shopData = [];

		    foreach ($shop as $key => $value) {

		    	$shopData[$key]['shopId']	=	$value['id'];//商家id
		    	$shopData[$key]['shopName']	=	$value['shop_name'];//商家名称
		    	$shopData[$key]['location']	=	$value['lat'].','.$value['lng'];//商家坐标
		    	$shopData[$key]['useRule']	=	$value['use_rule'];//使用规则
		    	$shopData[$key]['telPhone']	=	$value['telphone'];//商家电话
		    	$shopData[$key]['sole']	=	$value['sole'];//商家电话
		    	$shopData[$key]['address']	=	$value['address'];//商家电话
		    	$shopData[$key]['dates']	=	date('Y-m-d H:i',$value['dates']);//发布日期
		    		$shopData[$key]['status']	=	$value['status'];//如果状态为1，则直接不显示
		    	$shopData[$key]['toper']	=	$value['toper'];//是否置顶
		    	$finder = ['conditions'=>'sid = '.$value['id']];
		    	$shopSlide = VipShopSlide::find( $finder )->toArray();
		    	foreach ($shopSlide as $k => $v) {
		    		$shopData[$key]['imageUrl'] = $v['slide'];
		    		$shopData[$key]['cons']		=	$v['cons'];//人均
		    		$shopData[$key]['dishes']		=	$v['dishes'];//人均
		    		$shopData[$key]['price']	=	$v['price'];//招牌菜价格
		    	}

		    }
		    
		    Utils::apiDisplay( array('status'=>0,'data'=>$shopData)  );
		   
		}


		function descShopListAction()
		{
			//低价排序
			$token 	=	$this->request->getPost('token');
			$order  = 	$this->request->getPost('order');

			$author = new Authory( $token );
		    $author->loggingVerify();

		    //分页开始
		    $page 	=	$this->request->getPost('page');

		    if(empty($page))
		    	$page = 1;

		    $size = 8;//一次读取20条信息

				$skip = (intval($page)-1)*$size;

				$parameters	=	['limit' => $size,'offset'=>$skip,'order'=>"price ".$order];
				$parameters['conditions'] = 'status = 0';
				

		    $shop 	=	VipShopSlide::find( $parameters )->toArray();
		    if(empty($shop))
		    	Utils::jsonError(1,'你触碰到我的底线了，没有更多了@_@');
		    $shopData = [];

		    foreach ($shop as $key => $v) {

		    	$shopData[$key]['imageUrl'] = $v['slide'];
		    		$shopData[$key]['cons']		=	$v['cons'];//人均
		    		$shopData[$key]['dishes']		=	$v['dishes'];//人均
		    		$shopData[$key]['price']	=	$v['price'];//招牌菜价格
		    		$shopData[$key]['toper']	=	$value['toper'];//是否置顶
		    	$finder = ['conditions'=>'id = '.$v['sid']];
		    	$shopSlide = VipShop::find( $finder )->toArray();
		    	foreach ($shopSlide as $k => $value) {
		    		$shopData[$key]['shopId']	=	$value['id'];//商家id
			    	$shopData[$key]['shopName']	=	$value['shop_name'];//商家名称
			    	$shopData[$key]['location']	=	$value['lat'].','.$value['lng'];//商家坐标
			    	$shopData[$key]['useRule']	=	$value['use_rule'];//使用规则
			    	$shopData[$key]['telPhone']	=	$value['telphone'];//商家电话
			    	$shopData[$key]['sole']	=	$value['sole'];//商家电话
			    	$shopData[$key]['address']	=	$value['address'];//商家电话
			    	$shopData[$key]['dates']	=	date('Y-m-d H:i',$value['dates']);//发布日期
			    	$shopData[$key]['status']	=	$value['status'];//如果状态为1，则直接不显示
			    	
		    	}

		    }
		    
		    Utils::apiDisplay( array('status'=>0,'data'=>$shopData)  );
		   
		}

		function getLocationAction()
		{
			$location 	=	$this->request->getPost('location');

			$token 	=	$this->request->getPost('token');

			$author = new Authory( $token );
		    $author->loggingVerify();

		    //分页开始
		    $page 	=	$this->request->getPost('page');

		    if(empty($page))
		    	$page = 1;

		    $size = 8;//一次读取20条信息

				$skip = (intval($page)-1)*$size;

				$find	=	['limit' => $size,'offset'=>$skip,'order'=>'id desc'];


			$location =explode(',', $location);
			     $lat = $location[0];
			     $lng = $location[1];
			     // print_r($location);exit();
				  
			$geohash=new ShopLib;  
			    
			//经纬度转换成Geohash  
			    
			//获取附近的信息  
			  
			// $latArr = VipShop::find();
			// $find = $latArr->toArray();
			  
			// echo "当前位置为：经度108.7455，纬度34.3608<br/><br/>  
			// 以下网点离我最近：";  
			    
			// //开始  
			$b_time = microtime(true);  
			    
			//方案A，直接利用数据库存储函数，遍历排序  
			    
			//方案B geohash求出附近，然后排序  
			    
			//当前 geohash值  
			 $n_geohash = $geohash->encode($lat,$lng);  
			    
			//附近  
			$n = 3;  
			$like_geohash = substr($n_geohash, 0, $n);  
			 $find['conditions'] = "geohash LIKE '%$like_geohash%'";
			  $result = VipShop::find($find)->toArray();
			$shopData = [];

			if(empty($result))
				Utils::jsonError(1,'还没找到你附近的店呢-_-');


		    foreach ($result as $key => $value) {

		    	$shopData[$key]['shopId']	=	$value['id'];//商家id
		    	$shopData[$key]['shopName']	=	$value['shop_name'];//商家名称
		    	$shopData[$key]['location']	=	$value['lat'].','.$value['lng'];//商家坐标
		    	$shopData[$key]['lat']		=	$value['lat'];//商家坐标
		    	$shopData[$key]['lng']		=	$value['lng'];//商家坐标
		    	$shopData[$key]['useRule']	=	$value['use_rule'];//使用规则
		    	$shopData[$key]['telPhone']	=	$value['telphone'];//商家电话
		    	$shopData[$key]['sole']	=	$value['sole'];//商家电话
		    	$shopData[$key]['address']	=	$value['address'];//商家电话
		    	$shopData[$key]['toper']	=	$value['toper'];//是否置顶
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

		     foreach($shopData as $key=>$val)  
				{  
				    $distance = $this->getDistanceAction($lat,$lng,$val['lat'],$val['lng']);
				    $shopData[$key]['distance'] = $distance;  
				    $shopData[$key]['meters'] = $geohash->getmeters($lat,$lng,$val['lat'],$val['lng']);
				    // $shopData[$key]['meters'] = $meters;  
				    
				    //排序列  
				    $sortdistance[$key] = $distance;  
				}   
				array_multisort($sortdistance,SORT_ASC,$shopData);  
 			// print_r($shopData);
		    
		    Utils::apiDisplay( array('status'=>0,'data'=>$shopData)  );

		}

	function getDistanceAction($latitude1, $longitude1, $latitude2, $longitude2)   
	{  
	    $earth_radius = 6378137;   //approximate radius of earth in meters  
	      
	    $dLat = deg2rad($latitude2 - $latitude1);
	    $dLon = deg2rad($longitude2 - $longitude1);
	     /* 
	       Using the 
	       Haversine formula 
	  
	       http://en.wikipedia.org/wiki/Haversine_formula 
	       http://www.codecodex.com/wiki/Calculate_Distance_Between_Two_Points_on_a_Globe 
	       验证：百度地图  http://developer.baidu.com/map/jsdemo.htm#a6_1 
	       calculate the distance 
	     */   
	    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
	    $c = 2 * asin(sqrt($a));  
	    $d = $earth_radius * $c;  
	      
	    return round($d);   //四舍五入  
	}  

	


}