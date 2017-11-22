<?php

use Phalcon\Mvc\Controller;

class AdminController extends Controller
{


	// public function initialize(){
	// 	if(empty($this->session->get('username')))
	// 	{
	// 		 $this->response->redirect('admin/login');
	// 	}
           
	// }
	
	//与前端文章相关的的控制器
	function loginAction()
	{
		$this->view->pick('manager/login');
	}

	// 	function navAction()
	// {
	// 	if(empty($this->session->get('username')))
	// 	{
	// 		 $this->response->redirect('admin/login');
	// 	}

	// 	$this->view->pick('manager/nav');
		$this->view->pick('manager/addArticle');
	// }

	function vipShopAction()
	{
			if(empty($this->session->get('username')))
		{
			 $this->response->redirect('admin/login');
		}


		 // $this->view->disable();
		//查找vipshop表中的商家，组合输出
		$vipShop = VipShop::find(['conditions'=>'status = 0','order'=>'id desc'])->toArray();

		$shopData = [];
		foreach ($vipShop as $key => $value) {
			$dishes = VipShopSlide::find(['conditions'=>'sid = '.$value['id']])->toArray();

				foreach ($dishes as $k => $v) {
					$shopData[$key]['dishes'] = $v['dishes'];
					$shopData[$key]['cons'] = $v['cons'];
					$shopData[$key]['price'] = $v['price'];
				}

			$shopData[$key]['sid'] = $value['id'];
			$shopData[$key]['shopName'] = $value['shop_name'];
			$shopData[$key]['rule'] = $value['use_rule'];
			$shopData[$key]['telphone'] = $value['telphone'];
			$shopData[$key]['amount'] = $value['amount'];

		}
		// print_r($shopData);exit();
		$this->view->shopData      = $shopData;
		$this->view->pick('manager/vipshop');
	}

	function addArticleAction()
	{	
		/*添加文章*/
			if(empty($this->session->get('username')))
		{
			 $this->response->redirect('admin/login');
		}
		$this->view->pick('manager/addArticle');
	}
	function addVipShopAction()
	{
		/*添加商家*/
			if(empty($this->session->get('username')))
		{
			 $this->response->redirect('admin/login');
		}
		$this->view->pick('manager/add_vipshop');
	}

		function editVipCardAction()
	{
		/*添加商家*/
			if(empty($this->session->get('username')))
		{
			 $this->response->redirect('admin/login');
		}
		$this->view->pick('manager/editvipcard');
	}

	function editshopAction()
	{
		/*编辑商家*/
			if(empty($this->session->get('username')))
		{
			 $this->response->redirect('admin/login');
		}
		$sid = $this->request->getQuery("shopId");

		$shopData = VipShop::findFirst(['conditions'=>'id = '.$sid])->toArray();
		$shopSlide = VipShopSlide::findFirst(['conditions'=>'sid ='.$sid])->toArray();

		$shopData['dishes'] = $shopSlide['dishes'];
		$shopData['price'] = $shopSlide['price'];
		$shopData['cons'] = $shopSlide['cons'];
		$shopData['slide'] = $shopSlide['slide'];
		$shopData['location'] = $shopData['lat'].','.$shopData['lng'];
		
		

		$this->view->shopData = $shopData;
		$this->view->pick('manager/editVipShop');

	}


	function usersAction()
	{
			if(empty($this->session->get('username')))
		{
			 $this->response->redirect('admin/login');
		}
		$users = Users::find(['conditions'=>'status = 0','order'=>'id desc'])->toArray();
		$usersData = [];

		foreach ($users as $key => $value) {
			$vip = Viper::find(['conditions'=>'uid = '.$value['id']])->toArray();

				$usersData[$key]['uid'] = $value['id'];
				$usersData[$key]['nickName'] = $value['nickName'];
				$usersData[$key]['regTime'] = $value['reg_time'];

				foreach ($vip as $k => $v) {
					$usersData[$key]['levels'] = $v['levels'];
					$usersData[$key]['stime'] = $v['stime'] == 0 ? 0 :date('Y-m-d H:i:s',$v['stime']);
					$usersData[$key]['etime'] = $v['etime'] == 0 ? 0 :date('Y-m-d H:i:s',$v['etime']);
				}
		}
		// print_r($usersData);exit();
		$this->view->usersData = $usersData;
		$this->view->pick('manager/userList');
	}






















}