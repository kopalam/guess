<?php

use Phalcon\Mvc\Model;

class VipCard extends Model
{
	


    public function initialize()
    {
        $this->setSource("vip_card");
    }
}