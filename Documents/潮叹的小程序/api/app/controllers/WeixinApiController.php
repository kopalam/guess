<?php
use Phalcon\Mvc\Controller;

class WeixinApiController extends Controller
{

	$method = "GET";
	$url = "http://120.25.63.187/post/weixin?uid=rmrbwx&apikey=3askdEAshRTyyyCzUVNjMJNFc6Bj2ZtUCJYBhDTEFKkbwE6WkmDjxPJiju9Iqd6K";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	var_dump(curl_exec($curl));

}