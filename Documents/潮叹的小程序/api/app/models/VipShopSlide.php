<?php

use Phalcon\Mvc\Model;

class VipShopSlide extends Model
{
	


    public function initialize()
    {
        $this->setSource("vip_shop_slide");
    }
}