<?php

class Authory  {

    protected $token;

    public function __construct( $token=null ){
        $this->token = $token;
    }

    public function loggingVerify(){

        if( !$this->token )
            Utils::apiDisplay(["status" => 1,"message" => "用户未登录"]);

        $cache = new Cache();
        $user  = $cache->get( $this->token );

        if( !$user )
            Utils::apiDisplay(["status" => 1,"message" => "用户未登录"]);
    }

}