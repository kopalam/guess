<?php

use Phalcon\Mvc\Model;

class BuyLogs extends Model
{
	


    public function initialize()
    {
        $this->setSource("buy_logs");
    }
}