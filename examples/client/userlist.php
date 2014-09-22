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

if(!$_COOKIE['access_token']) {
	exit('重新访问client/index.php获取access_token');
}
$access_token = $_COOKIE['access_token'];

$client->setAccessToken($access_token);

$next_openid = isset($_GET['openid']) ? $_GET['openid'] : '';


$result = $client->getUserList($next_openid);

var_dump($result);

if(false === $result) {
	exit($client->errmsg);
}