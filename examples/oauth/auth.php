<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
require __DIR__ . '/../init.php';
//code
$code = isset($_GET['code']) ? $_GET['code'] : '';

echo '返回的code' . $code;

//拿code换取access_token
$oauth_client = new Light\Wechat\OauthClient(APP_ID, APP_SECRET);

$res = $oauth_client->getOauthAccessToken($code);

if($res === false) {
  exit("[{$oauth_client->errcode}]: {$oauth_client->errmsg}");
}

?>
<html>
	<head>
		<title>授权页</title>
		<meta charset="utf-8" >
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" />
	</head>
	<body>
		<h3>基本信息</h3>
		<p>access_token：<?php echo $res['access_token']; ?></p>
		<p>过期时间: <?php echo $res['expires_in'] ?>秒</p>
		<p>refresh_token: <?php echo $res['refresh_token'] ?> </p>
		<p>OpenId: <?php echo $res['openid'] ?></p>
		<p>scope: <?php echo $res['scope'] ?></p>

		<h3>验证正确性</h3>
		<?php
			$check = $oauth_client->authAccessToken($res['openid']);
		?>
		<p><?php echo $oauth_client->errmsg; ?></p>
		<p>
		<?php
				//测试获取用户信息接口
			if($res['scope'] == 'snsapi_userinfo') {
				$user_info = $oauth_client->getUserInfo($res['openid']);
				var_dump($user_info);
			}
		?>
		</p>
		<h3>刷新token</h3>
		<?php
			$result = $oauth_client->refreshOauthAccessToken($res['refresh_token']);
			var_dump($result);
		?>
	</body>
</html>