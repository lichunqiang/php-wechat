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

$openid = isset($_GET['openid']) ? $_GET['openid'] : '';

empty($openid) and exit('请求缺少openid参数');

$user_info = $client->getUserInfo($openid);

if(false === $user_info) {
	exit($client->errmsg);
}

var_dump($user_info);
?>