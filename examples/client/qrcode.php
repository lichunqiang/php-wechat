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

//生成二维码

$result = $client->createQRCodeTicket('123');

if(false === $result) {
	exit($client->errmsg);
}

//那ticket换取二维码
$qrcode_url = $client->getQRCode($result['ticket']);

?>

<img src="<?php echo $qrcode_url ?>" alt="">