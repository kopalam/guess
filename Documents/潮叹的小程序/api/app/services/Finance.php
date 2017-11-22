<?php

	Class Finance{
		
	public function createOrder($uid, $cash, $trade_sn){

       
            // $manager     = new TxManager();
            // $transaction = $manager->get();
        	// $cach = intval($cash) == 66 ? 30 : 7; 
        	switch ($cash) {
        		case 38:
        			 $days = 30;
                     $etime = time()+3600*24*31;
                     $level = 2;
        			break;
        		case 28:
                     $days = 7;
                     $etime = time()+3600*24*7;
                     $level = 1;
                    break;
        		default:
        			 $days = 7;
                     $etime = time()+3600*24*7;
                     $level = 1;
        			break;
        	}
        	$find  = ['conditions'=>'uid = '.$uid];
        	$viper = Viper::findFirst( $find );


            if(!$viper)
            {
                $vip = new Viper;
                $vip->levels    =   0;
                $vip->uid       =   $uid;
                $vip->status    =   0;
                $vip->stime     =   0;
                $vip->etime     =   0;
                $vip->save();

                // $orderLogs = new BuyLogs();
                // $orderLogs->uid       = $uid;
                // $orderLogs->vid      = $vip->id;
                // $orderLogs->dates    = $etime;
                // $orderLogs->days     = $days;
                // $orderLogs->out_trade_no = $trade_sn;
                // $orderLogs->status   = 1;

                // $orderLogs->save();

            }



            $orderLogs = new BuyLogs();
            $orderLogs->uid  	 = $uid;
            $orderLogs->vid      = $viper->id;
            $orderLogs->dates    = $etime;
            $orderLogs->days     = $days;
            $orderLogs->rtime     = $days;
            $orderLogs->out_trade_no = $trade_sn;
            $orderLogs->status   = 0;

            $orderLogs->save();

            $updateVip = Viper::findFirst( $find );

            $updateVip->stime = time();
            $updateVip->etime =  $etime;
            $updateVip->levels = $level;
            $updateVip->save();

           
              if($orderLogs->save()==false)
                return false;

            return true;

       
    }
}