<?php

use Phalcon\Mvc\Model;

class UseLogs extends Model
{
	


    public function initialize()
    {
        $this->setSource("use_logs");
    }
}