<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
require __DIR__ . '/../init.php';

$oauth_client = new Light\Wechat\OauthClient(APP_ID, APP_SECRET);

$scope = isset($_GET['scope']) ? $_GET['scope'] : 'snsapi_base';


$path = pathinfo('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//获取链接
$redirect_uri = $path['dirname'] . '/auth.php';
$url = $oauth_client->getOauthRedirect($redirect_uri, '123', $scope);
// echo $url;
// exit;
//生成二维码，扫描进入
// header("Content-Type: application/force-download");
// header("Content-Transfer-Encoding: binary");
header('Content-Type: image/png');
// header('Content-Disposition: attachment; filename=qrcode_'. $filename_subfix .'.png');
\PHPQRCode\QRcode::png($url, false, 'L', 6, 4);