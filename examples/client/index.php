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

//获取access_token
$result = $client->getAccessToken();

if($result === false) {
	exit($client->errmsg);
}

var_dump($result);

//保存
setcookie('access_token', $result['access_token'], ($_SERVER['REQUEST_TIME'] + $result['expires_in']), '/');
