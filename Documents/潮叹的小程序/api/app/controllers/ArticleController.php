<?php

use Phalcon\Mvc\Controller;

class ArticleController extends Controller
{
	//与前端文章相关的的控制器
	function shopLocationAction()
	{
		/*

			文章列表
			@需要先post用户的session3rd，验证该用户是否存在。
			@传递location，获取用户当前的位置
			@

		*/
			$token 	= $this->request->getPost('token');
 			$author = new Authory( $token );
		    $author->loggingVerify();

		    // $page 	=	$this->request->getPost('page');
		    // $userId =	$this->request->getPost('userId');
		    $location 	=	$this->request->getPost('location');
		   

			$getShopData 	=	new ShopLib(); //调取商家插件
			// $shopListData 	=	$getShopData->shopList($location);
			

			$geohash=new Geohash;
  
			//经纬度转换成Geohash
			  
			//获取附近的信息
			
			  
			//开始
			$b_time = microtime(true);
			  
			//方案A，直接利用数据库存储函数，遍历排序
			  
			//方案B geohash求出附近，然后排序
			  
			//当前 geohash值
			$n_geohash = $geohash->encode($location);
			  
			//附近
			$n = $_GET['n'];
			$like_geohash = substr($n_geohash, 0, $n);
			  
			$sql = 'select * from mb_shop_ext where geohash like "'.$like_geohash.'%"';
			  
			echo $sql;
			  
			$data = $mysql->queryAll($sql);
			  
			//算出实际距离
			foreach($data as $key=>$val)
			{
			    $distance = getDistance($n_latitude,$n_longitude,$val['latitude'],$val['longitude']);
			  
			    $data[$key]['distance'] = $distance;
			  
			    //排序列
			    $sortdistance[$key] = $distance;
			}
			  
			//距离排序
			array_multisort($sortdistance,SORT_ASC,$data);
			  
			//结束
			$e_time = microtime(true);
			  
			echo $e_time - $b_time;
			  
			var_dump($data);
			  
			//根据经纬度计算距离 其中A($lat1,$lng1)、B($lat2,$lng2)
			function getDistance($lat1,$lng1,$lat2,$lng2)
			{
			    //地球半径
			    $R = 6378137;
			  
			    //将角度转为狐度
			    $radLat1 = deg2rad($lat1);
			    $radLat2 = deg2rad($lat2);
			    $radLng1 = deg2rad($lng1);
			    $radLng2 = deg2rad($lng2);
			  
			    //结果
			    $s = acos(cos($radLat1)*cos($radLat2)*cos($radLng1-$radLng2)+sin($radLat1)*sin($radLat2))*$R;
			  
			    //精度
			    $s = round($s* 10000)/10000;
			  
			    return  round($s);




		  //   if(!$page)
				// 	$page = 1;

				// $size = 6;//一次读取20条信息

				// $skip = (intval($page)-1)*$size;

				// $parameters	=	['limit' => $size,'offset'=>$skip,'order'=>'dates desc'];
				// $parameters['conditions'] = 'status = 0';
				// $listData = array();
				// $getSlide = array();
				// $topicList = array();


				// 	$list = Article::find( $parameters )->toArray();
				// 	if(empty($list))
				// 		Utils::jsonError(1,'还没有新的文章-_-');

				// 	// print_r($list);

				// 	foreach ($list as $key => $value) 
				// 		{

				// 			$listData[$key]['articleId']	=	$value['id'];
				// 			$listData[$key]['title']	=	$value['article_title'];
				// 			$listData[$key]['abstruct']	=	$value['abstruct']; //描述
				// 			$listData[$key]['price']	=	$value['cons'];
				// 			$listData[$key]['dates'] = date('Y-m-d',$value['dates']);

				// 			// $getSlide = ArticleSlide::find(array('condition'=>'article_id ='.$value['id']))->toArray();//查找对应幻灯片
				// 			// 	if(empty($getSlide)){
				// 			// 		$listData[$key]['images']	=	0;//商家名称
				// 			// 	}else{
				// 			// 		foreach($getSlide as $k =>$slide)
				// 			// 			{
				// 			// 				$listData[$key]['images']	=	$slide['slide'];//商家名称
				// 			// 			}
				// 			// 	}

				// 				//查找对应的商家sid
				// 				$shopFinder 	=	['conditions'=>"aid = ".$value['id']];
				// 				$shopList 		=	ShopGroup::find($shopFinder)->toArray();
				// 				//通过sid查找对应商家名称
				// 						foreach ($shopList as $s => $n) {
				// 							$shop = Shop::find(['conditions'=>'id = '.$n['sid']])->toArray();

				// 								foreach ($shop as $to => $sid) {
				// 									$listData[$key]['shopName']	=	$sid['shop_name'];
				// 									$listData[$key]['address']	=	$sid['address'];
				// 									$listData[$key]['telphone']	=	$sid['telphone'];
				// 									$listData[$key]['address']	=	$sid['address'];
				// 									$listData[$key]['shopSlide']	=	empty($sid['shop_slide'])?0:$sid['shop_slide'];
				// 								}
				// 						}
								

				// 					// $findShop  =	ShopGroup::find(array('condition'=>'article_id ='.$value['id']))->toArray();//查找对应的商家编号
				// 					// foreach ($findShop as $f => $shop) {
				// 					// 	$getShop = Shop::find(array('condition'=>'id ='.$shop['sid']))->toArray();

				// 					// 		foreach ($getShop as $find => $name) {
				// 					// 			$listData[$key]['shopName']	=	$name['name'];//商家名称
				// 					// 			$listData[$key]['address']	=	$name['address'];//商家名称
				// 					// 			$listData[$key]['telphone']	=	$name['telphone'];//商家名称

				// 					// 		}
				// 					// }


				// 		}

						
    //         			Utils::ApiDisplay(['status'=>0,'data'=>$listData]);

		}	


/*
		function checkShopGroupAction()
		{
			//关联文章列表与店铺表的数据
			$find 	=	Article::find()->toArray();
			foreach ($find as $key => $value) {
				// $shopGroup 	=	ShopGroup()
				$shop = Shop::find(['conditoin'=>"shop_name = '".$value['shop_name']."'"])->toArray();
					foreach ($shop as $k => $v) {
						if($v['shop_name'] == $value['shop_name'])
						{
							$data = new ShopGroup;
							$data->aid 	=	$value['id'];
							$data->sid 	=	$v['id'];
							$data->save();
							if($data->save() == false)
								echo 'error';
						}
					}
			}
			echo 'ok';
		}*/

		

	}
