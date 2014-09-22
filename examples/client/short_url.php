<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
require __DIR__ . '/../init.php';

$client = new Light\Wechat\Client(APP_ID, APP_SECRET);

var_dump($_COOKIE);

if(!$_COOKIE['access_token']) {
	exit('重新访问client/index.php获取access_token');
}
$access_token = $_COOKIE['access_token'];

$client->setAccessToken($access_token);

//
$url = 'http://www.baidu.com';

$short_url = $client->convertShortUrl($url);

if(false === $short_url) {
	exit($client->errmsg);
}
var_dump($short_url);

?>