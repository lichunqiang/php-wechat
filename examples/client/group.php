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

$method = isset($_GET['method']) ? $_GET['method'] : 'get';
$openid = isset($_GET['openid']) ? $_GET['openid'] : '';

switch ($method) {
	case 'create':
		$result = $client->createGroup('测试组');
		break;
	case 'getuser':
		$result = $client->getUserGroupId($openid);
		break;
	case 'update':
		$result = $client->updateGroupName(101, '测试分组');
		break;
	case 'updateuser':
		$result = $client->updateUserRemark($openid, '测试标签');
		break;
	case 'move':
		$result = $client->moveUserGroup($openid, 101);
		break;
	case 'get':
	default:
		$result = $client->getGroup();
		break;
}

var_dump($result);

if(false === $result) {
	exit($client->errmsg);
}


?>