<?php

use Phalcon\Mvc\Model;

class Cauth extends Model
{
	


    public function initialize()
    {
        $this->setSource("cauth");
    }
}