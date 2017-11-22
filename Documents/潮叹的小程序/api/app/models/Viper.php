<?php

use Phalcon\Mvc\Model;

class Viper extends Model
{
	


    public function initialize()
    {
        $this->setSource("viper");
    }
}