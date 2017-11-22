<?php

use Phalcon\Mvc\Controller;


class ClearController extends Controller
{
	function oldvipAction()
	{
		/*
			检索所有会员，即Vip表中，条件是 etime!==0.status==0
			etime - stime <= 0  
			就update
		*/
			$find	=	['conditions'=>'levels > 0 and status =0'];
			$check	= 	Viper::find( $find )->toArray();
			$time = time();
			

			foreach ($check as $key => $value) {
					//当前时间大于结束时间
						
						if($time <= $value['stime'])
							$day[$key]['days'] 	=	round(($time - $value['stime'])/3600/24);

						$day[$key]['etime'] = $value['etime'];
						$day[$key]['uid']	=	$value['uid'];
						$day[$key]['vid']	=	$value['id'];


					}

				
				

			foreach ($day as $k => $v) {
				if($time > $v['etime']){
					// print_r($v);exit();
					$finder['conditions'] = ' uid = '.$v['uid'];
					$update = Viper::findFirst($finder);
					$update->levels = 0;
					$update->stime 	= 0;
					$update->etime 	= 0;
					$update->uid 	= $v['uid'];
					$update->save();
				}

				//更新到buylog表中还剩几天
						$f = ['conditions'=>'vid ='.$v['vid']];
						$buy = BuyLogs::findFirst($f);
						if(!empty($buy))
						{
							$buy->rtime = $v['days'];
							$buy->status = $v['days'] == 0 ?1:0;
							$buy->save();
						}
								
			}
	}
}