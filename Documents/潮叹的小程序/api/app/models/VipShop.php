<?php

use Phalcon\Mvc\Model;

class VipShop extends Model
{
	


    public function initialize()
    {
        $this->setSource("vip_shop");
    }
}