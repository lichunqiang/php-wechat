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

//获取客服聊天记录

$start_time = $_SERVER['REQUEST_TIME'] - 3600;
$data = array(
	'starttime' => $start_time,
	'endtime' => $_SERVER['REQUEST_TIME'],
	'pagesize' => 10,
	'pageidex' => 1,
	'openid' => 'xxxx'
);

$result = $client->getCustomerMsgRecord($data);

if(false === $result) {
	exit($client->errmsg);
}

var_dump($result);

?>