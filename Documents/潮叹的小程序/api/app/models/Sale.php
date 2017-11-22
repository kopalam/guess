<?php

use Phalcon\Mvc\Model;

class Sale extends Model
{
	


    public function initialize()
    {
        $this->setSource("sale");
    }
}