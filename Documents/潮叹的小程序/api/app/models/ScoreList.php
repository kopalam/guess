<?php

use Phalcon\Mvc\Model;

class ScoreList extends Model
{
	


    public function initialize()
    {
        $this->setSource("score_list");
    }
}