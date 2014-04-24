<?php
	require '../wechat.class.php';
	require '../php_fast_cache.php';
	//use file cache data
	phpFastCache::$storage = 'files';
	phpFastCache::$securityKey = 'logs';
	//phpFastCache::$debugging = true;
	
	function cacheFunc($key, $data, $expire = 1800) {
		phpFastCache::set($key, $data, $expire);
	}
	
	//wechat request config
	$wechat_option = array(
		'appid' => 'wxc0f8096d1a552819',
		'appsecret' => '64e8ffd2017b9a4624464f7b2ed2996c',
		'token' => 'light',
		'cachecallback' => 'cacheFunc'
	);
	$wechat = new Wechat($wechat_option);
	//check if stored access token
	$access_token = phpFastCache::get('access_token');
	
	if($access_token == null) {
		//get the access token
		$access_token = $wechat->checkAuth(/*$wechat_option['appid'], $wechat_option['appsecret']*/);
		if($access_token === false) {
			exit($wechat->errMsg);
		}
		//phpFastCache::set('keyword', 'test', 10); //10 minutes
	} else {
		echo 'this is use cache! <br />';
		echo 'the access token is :: ' . $access_token;
		echo '<br />';
		$wechat->setAccessToken($access_token);
	}
	$scene_id = 12;
	$expire = 1800;//10s
	$res = $wechat->getQRCode($scene_id, 0, $expire);
	if($res === false) {
		exit($wechat->errMsg);
	}
	$ticket = $res['ticket'];
	print_r($res);
	$qrcode_url = $wechat->getQRUrl($ticket);
?>
create at:<?php echo date('Y-m-d H:i:s'); ?><br />
expiry at:<?php echo date('Y-m-d H:i:s', time() + $expire); ?><br />
<img src='<?php echo $qrcode_url; ?>'/>