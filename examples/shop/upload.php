<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat;

require __DIR__ . '/../init.php';
//get access_token

$client = new Client(APP_ID, APP_SECRET);

//获取access_token
$result = $client->getAccessToken();

if ($result === false) {
    exit($client->errmsg);
}

$access_token = $result['access_token'];
// echo $access_token = 'FOHLjiCkyvwbLMTGeaTQnuShsCDC0-dmkbZ7OLXyAPbEnk-NTgfP-tikm3_8l7Jf3_nYNAtIUv4I8Qbm4TQgyA';

//shop client

$shopClient = new Shop($access_token);

//upload file
$result = $shopClient->uploadImage(__DIR__ . '/../fixture/panda1.jpg');
var_dump($shopClient->errcode);
var_dump($shopClient->errmsg);
var_dump($result);
