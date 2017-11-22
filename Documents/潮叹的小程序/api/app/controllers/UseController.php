<?php
use Phalcon\Mvc\Controller;

class UseController extends Controller
{
	/*

		我的使用记录
		UseLogs 购买记录
	*/

			function myUseListAction()
			{

				/*

					我的使用记录，记录我去过哪些餐厅

				*/

				$token 	=	$this->request->getPost('token');

				$author = new Authory( $token );
			    $author->loggingVerify();

			    $uid = $this->request->getPost('uid');
			    //查询uselog表中，关于该uid的记录
			    $find = ['conditions'=>'uid = '.$uid,'order' => 'dates desc'];
			    $logs = UseLogs::find($find)->toArray();

			    if(empty($logs))
			    	Utils::JsonError(1,'这里什么都没有！赶快去享受招牌菜吧@_@~');

			    foreach ($logs as $key => $value) {
			    	$shopName = VipShop::find( $value['sid'] )->toArray();

			    		foreach ($shopName as $k => $v) {
			    			$data[$key]['shopName'] =	$v['shop_name'];
			    			$data[$key]['dates'] 	=	date('Y-m-d H:i:s',$value['dates']);//消费时间
			    		}
			    }

			    Utils::apiDisplay(['status'=>0,'data'=>$data]);
			   
			}

			function useLogsAction()
			{
				$token 	=	$this->request->getPost('token');

				$author = new Authory( $token );
			    $author->loggingVerify();

				$sid = $this->request->getPost('sid');
				$uid = $this->request->getPost('uid');

				
					/*如果查找的数据，sole等于0，则可以继续使用*/
					$useData = UseLogs::find(['conditions'=>'sid = '.$sid.' and uid ='.$uid])->toArray();
					// print_r($useData);exit();
					if(empty($useData))
						Utils::jsonSuccess('ok');

					$time = date('Y-m-d',time());
					foreach ($useData as $key => $value) {
							if($value['sole']==1)
								Utils::jsonError(1,'该商品只能使用一次哦-_-');

					}

					Utils::jsonSuccess('ok');
				
			}
}