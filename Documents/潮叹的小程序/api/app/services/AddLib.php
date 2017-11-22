<?php

Class AddLib{

	function findShop($shop_name,$lat,$lng,$telphone,$addreess)
	{
		$shopData 	=	Shop::findFirst(['conditions'=>"shop_name = '".$shop_name."'"])->toArray();
		$sid 	=	$shopData['id'];

		$location = new ShopLib;
		$geohash = $location->encode($lat,$lng); //转化成字符串

			if(!$shopData){
				$shop = new Shop();
				$shop->shop_name = $shop_name;
				$shop->lat 	=	$lat;
				$shop->lng 	=	$lng;
				$shop->geohash 	=	$geohash;
				$shop->telphone 	=	$telphone;
				$shop->addreess 	=	$addreess;
				$shop->status 	=	0;
				$shop->dates=	time();
				$shop->save();
				if($articleData->save() == false)
					throw new Exception("录入店铺失败", 10001);
				$sid 	=	$shop->id;
			}
				


		return  $sid;

	}

	function article($topic_id,$article_title,$image,$article_writer,$contents,$tips,$cons,$abstract,$tags_id)
	{

			$articleData = new Article();
			$articleData->topic_id = $topic_id;
			$articleData->article_title = $article_title;
			$articleData->article_writer = $article_writer;
			$articleData->contents 	=	$contents;
			$articleData->tips 		=	$tips;
			$articleData->cons 		=	$cons;
			$articleData->abstract	=	$abstract;
			$articleData->dates 	=	time();
			$articleData->tags_id 	=	$tags_id;
			$articleData->status 	=	0;
			$articleData->save();
				if($articleData->save() == false)
					throw new Exception("文章录入失败", 10001);
					// Utils::jsonError(1,'文章录入失败');

			$SlideData 	=	new ArticleSlide();
			$SlideData->article_id 	=	$articleData->id;
			$SlideData->article_slide	=	$image;
			$SlideData->save();
				if($SlideData->save() == false)
					throw new Exception("封面图片录入失败", 10001);

			return $articleData->id;
	}

		function addNewVip($shop_name,$lat,$lng,$use_rule,$telphone,$address,$amount,$cons,$price,$image,$dishes,$sole)
	{
			/*

				新的商家招牌菜添加

			*/

			$location = new ShopLib;
			$geohash = $location->encode($lat,$lng); //转化成字符串
				
			$articleData = new VipShop();
			$articleData->shop_name = $shop_name;
			$articleData->use_rule 	=	$use_rule;
			$articleData->telphone 		=	$telphone;
			$articleData->address 		=	$address;
			$articleData->lat	=	$lat;
			$articleData->lng	=	$lng;
			$articleData->geohash	=	$geohash;
			$articleData->amount	=	$amount;
			$articleData->dates 	=	time();
			$articleData->status 	=	0;
			$articleData->sole 	=	$sole;
			$articleData->save();
				if($articleData->save() == false)
					throw new Exception("文章录入失败", 10001);
					// Utils::jsonError(1,'文章录入失败');

			$SlideData 	=	new VipShopSlide();
			$SlideData->sid 	=	$articleData->id;
			$SlideData->slide	=	$image;

			$SlideData->dishes = $dishes;
			$SlideData->cons = $cons;
			$SlideData->price = $price;
			$SlideData->save();
				if($SlideData->save() == false)
					throw new Exception("封面图片录入失败", 10001);

			return $articleData->id;
	}

			function editVipShop($id,$shop_name,$lat,$lng,$use_rule,$telphone,$address,$amount,$cons,$price,$image,$dishes,$sole,$toper)
	{
			/*

				新的商家招牌菜添加

			*/

			$location = new ShopLib;
			$geohash = $location->encode($lat,$lng); //转化成字符串
				
			$shopData = VipShop::findFirst(['conditions'=>'id = '.$id]);
			if(empty($shopData))
				throw new Exception("不存在该id", 10001);

			$shopData->shop_name = $shop_name;
			$shopData->use_rule 	=	$use_rule;
			$shopData->telphone 		=	$telphone;
			$shopData->address 		=	$address;
			$shopData->lat	=	$lat;
			$shopData->lng	=	$lng;
			$shopData->geohash	=	$geohash;
			$shopData->amount	=	$amount;
			$shopData->dates 	=	time();
			$shopData->status 	=	0;
			$shopData->sole 	=	$sole;
			$shopData->save();
				if($shopData->save() == false)
					throw new Exception("更新文章录入失败", 10001);
					// Utils::jsonError(1,'文章录入失败');

			$SlideData 	=	$shopData = VipShopSlide::findFirst(['conditions'=>'sid = '.$id]);
			$SlideData->sid 	=	$id;
			$SlideData->slide	=	$image;

			$SlideData->dishes = $dishes;
			$SlideData->cons = $cons;
			$SlideData->price = $price;
			$SlideData->save();
				if($SlideData->save() == false)
					throw new Exception("更新封面图片录入失败", 10001);

			return $articleData->id;
	}


	function addCard($image)
	{
		$vip = VipCard::findFirst(1);
		$vip->images = $image;
		$vip->save();

				if($vip->save() == false)
					return false;

			return true;
	}
}