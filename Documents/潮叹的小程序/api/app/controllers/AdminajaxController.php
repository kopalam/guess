<?php

use Phalcon\Mvc\Controller;

class AdminajaxController extends Controller
{
	//与前端文章相关的的控制器



	function loginAction()
	{
		$name = $this->request->getPost('name');
		$passwd = $this->request->getPost('passwd');
		$passwd = sha1($passwd);

		$find = Admin::findFirst(['conditions'=>"name = '".$name."'"])->toArray();
		if(!$name)
			Utils::jsonError(1,'不存在该用户');
		if($find['passwd'] == $passwd){
			$this->session->set('username', $name);
			 $this->response->redirect("admin/vipShop");
		}else{
			Utils::jsonError(1,'密码错误');
		}
	}

	function addVipShopAction()
	{
		$shop_name = $this->request->getPost('shop_name');
		$image = $this->request->getPost('image');
		$address = $this->request->getPost('address');
		$dishes = $this->request->getPost('dishes');
		$price = $this->request->getPost('price');
		$amount = $this->request->getPost('amount');
		$location = $this->request->getPost('location');
		$telphone = $this->request->getPost('telphone');
		$use_rule = $this->request->getPost('use_rule');
		$cons = $this->request->getPost('cons');
		$sole = $this->request->getPost('sole');

		if(empty($sole) || $sole == null)
		{
			$sole = 0;
		}

		// echo $sole;exit();

		$location =explode(',', $location);
		$lat = $location[0];
		$lng = $location[1];

		try{

			
			$add = new AddLib();
			$addVip 	=	$add->addNewVip($shop_name,$lat,$lng,$use_rule,$telphone,$address,$amount,$cons,$price,$image,$dishes,$sole);

			}catch(Exception $e){
	            $data['status']  = 1;
	            $data['message'] = $e->getMessage();
	            Utils::apiDisplay( $data );
       		 }
       		  Utils::apiDisplay( ['status'=>0,'message'=>'ok','sole'=>$sole] );
	}

	function editVipShopAction()
	{
		$id = $this->request->getPost('id');
		$shop_name = $this->request->getPost('shop_name');
		$image = $this->request->getPost('image');
		$address = $this->request->getPost('address');
		$dishes = $this->request->getPost('dishes');
		$price = $this->request->getPost('price');
		$amount = $this->request->getPost('amount');
		$location = $this->request->getPost('location');
		$telphone = $this->request->getPost('telphone');
		$use_rule = $this->request->getPost('use_rule');
		$cons = $this->request->getPost('cons');
		$sole = $this->request->getPost('sole');
		$toper = $this->request->getPost('toper');

		if(empty($sole) || $sole == null)
		{
			$sole = 0;
		}

		// echo $sole;exit();

		$location =explode(',', $location);
		$lat = $location[0];
		$lng = $location[1];

		try{

			
			$add = new AddLib();
			$addVip 	=	$add->editVipShop($id,$shop_name,$lat,$lng,$use_rule,$telphone,$address,$amount,$cons,$price,$image,$dishes,$sole);

			}catch(Exception $e){
	            $data['status']  = 1;
	            $data['message'] = $e->getMessage();
	            Utils::apiDisplay( $data );
       		 }
       		  Utils::apiDisplay( ['status'=>0,'message'=>'ok','sole'=>$sole] );
	}

	function addArticleAction()
	{
		/*
			使用 Add库来处理文章的添加和店铺的查询
			AddLib.php
		*/
		$article_title = $this->request->getPost('article_title');
		$article_writer = $this->request->getPost('article_writer');
		$shop_name = $this->request->getPost('shop_name');
		$topic_id = $this->request->getPost('topic_id');
		$image = $this->request->getPost('image');
		$address = $this->request->getPost('address');
		$location = $this->request->getPost('location');
		$telphone = $this->request->getPost('telphone');
		$abstract = $this->request->getPost('abstract');
		$tips = $this->request->getPost('tips');
		$cons = $this->request->getPost('cons');
		$contents = $this->request->getPost('contents');
		$tags_id = $this->request->getPost('tags_id');

		$topic_id = empty($topic_id) ? 0 : $topic_id;

		$location =explode(',', $location);
			     $lat = $location[0];
			     $lng = $location[1];
		/*
			
			写入 article表的字段
			@article_title
			@article_writer
			@abstract
			@tips
			@cons
			@contents
		*/
			// echo json_encode(['article_title'=>$article_title,
			// 	'writer'=>$article_writer,'shop_name'=>$shop_name,'topic_id'=>$topic_id,
			// 	'image'=>$image,'location'=>$location,'telphone'=>$telphone,'abstract'=>$abstract,'tipc'=>$tips,
			// 	'cons'=>$cons,'contents'=>$contents,'tags_id'=>$tags_id]);exit();
		try{
			$articleData = new AddLib();
			$article 	=	$articleData->article($topic_id,$article_title,$image,$article_writer,$contents,$tips,$cons,$abstract,$tags_id);

			$shopData 	=	$articleData->findShop($shop_name,$lat,$lng,$telphone,$address);

			$group 		=	new ShopGroup();	//把对应商家的对应文章相关联
			$group->aid =	$article;
			$group->sid =	$shopData;
			$group->save();
			Utils::jsonSuccess('ok');
			}catch(Exception $e){
	            $data['status']  = 1;
	            $data['message'] = $e->getMessage();
	            Utils::apiDisplay( $data );
       		 }
			
			//
	}

	function VipCardAction()
	{
		$image = $this->request->getPost('image');

		$vipCard = new AddLib();
		$card = $vipCard->addCard($image);


		if($card==false)
            Utils::jsonError(1,"修改失败");

		 Utils::jsonSuccess("修改成功~");
	}



	function deleteVipShopAction()
	{
		$sid = $this->request->getPost('sid');
		$shop = VipShop::findFirst(['conditions'=>'id = '.$sid]);
		$shop->delete();
		$shopSlide = VipShopSlide::findFirst(['conditions'=>'sid = '.$sid]);
		if($shopSlide->delete()==false)
            Utils::jsonError(1,"删除失败");

        Utils::jsonSuccess("删除成功~");
	}



}