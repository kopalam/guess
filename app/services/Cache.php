<?php

class Cache  {

    protected $memcache = null;

    public function __construct(){
        $this->memcache = $this->_initMemcache();
    }

    protected function _initMemcache(){
        $memcache = new memcache();
        $memcache->connect("","11211");
        return $memcache;
    }

    public function set($key , $value, $expire ){
        $this->memcache->set($key , $value, MEMCACHE_COMPRESSED, $expire);
    }

    public function get( $key ){
        return $this->memcache->get( $key );
    }

    public function remove( $key ){
        return $this->memcache->delete( $key );
    }

    public function incr($key,$number ){
        return $this->memcache->increment($key,$number);
    }
}
