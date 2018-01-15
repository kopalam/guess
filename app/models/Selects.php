<?php

use Phalcon\Mvc\Model;

class Selects extends Model
{
	// public $id;

	// public $openId;

	// public $unionId;

	// public $nickName;

	// public $gender;

	// public $language;

	// public $city;

	// public $province;

	// public $country;

	// public $avaterUrl;

	// public $reg_time;


    public function initialize()
    {
    	// $this->setConnectionService('database');  
        $this->setSource("guess_selects");
    }
}