<?php

use Phalcon\Mvc\Model;

class VipPrice extends Model
{
	


    public function initialize()
    {
        $this->setSource("vip_price");
    }
}