<?php

use Phalcon\Mvc\Model;

class Score extends Model
{
	


    public function initialize()
    {
        $this->setSource("score");
    }
}