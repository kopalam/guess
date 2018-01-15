<?php

class Redis  {

    protected $redis = null;

    public function __construct(){
        $this->redis = $this->_initMemcache();
    }

    protected function _initMemcache(){
        $redis = new redis();
        $redis->connect("120.25.63.187","63796");
        $redis->select('guess');  
        return $redis;
    }

    public function set($key , $value, $expire ){
        $this->redis->set($key , $value);
        $this->$redis->expire($key, $expire);
    }

    public function get( $key ){
        return $this->redis->get( $key );
    }

    public function remove( $key ){
        return $this->redis->delete( $key );
    }

    public function incr($key,$number ){
        return $this->redis->increment($key,$number);
    }
}
