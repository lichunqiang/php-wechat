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
		'appid' => 'wx35f0265338a63764',
		'appsecret' => 'fb535bf1c45c8295d43bb0d8d893ccb7',
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
	$open_id = 'oQLNOt-93kaIYZFiilUZpPomriqA';
	/*	
	$user_info = $wechat->getUserInfo($open_id);	
	var_dump($user_info);
	*/
	
	//get user list
	/*$user_list =  $wechat->getUserList();
	
	var_dump($user_list);
	*/
	$type = 'text';
	
	$text_message = array('touser' => $open_id, 'msgtype' => 'text', 'text' => array('content' => 'hello world'));
	//send custom message
	$res = $wechat->sendCustomMessage($text_message);
	if($res === false) {
		exit( $wechat->errMsg );
	}
	
	$article_message = array(
		'touser' => $open_id,
		'msgtype' => 'news',
		'news' => array(
			'articles' => array(
				array('title' => 'text', 'description' => 'heiehi', 'url' => 'www.baidu.com', 'picurl' => '')
			)
		)
	);
	$res = $wechat->sendCustomMessage($article_message);
	if($res === false) {
		print_r( $wechat->errMsg );
	}
	var_dump($res);
	$link_message = array(
		'touser' => $open_id,
		'msgtype' => 'link',
		'link' => array(
			'title' => 'est',
			'description' => 'aa',
			'url' => 'www.baidu.com'
		)
	);
	
	$res = $wechat->sendCustomMessage($link_message);
	if($res === false) {
		print_r( $wechat->errMsg );
	}
	var_dump($res);
	$music_message = array(
		'touser' => $open_id,
		'msgtype' => 'music',
		'music' => array(
			'title' => 'Test',
			'description' => 'description',
			'musicurl' => 'http://y.qq.com/#type=song&mid=000gxXWE0Sr4jy',
			'hqmusicurl' => 'http://y.qq.com/#type=song&mid=000gxXWE0Sr4jy',
			'thumb_media_id' => '000gxXWE0Sr4jy'
		)
	);
	
	$res = $wechat->sendCustomMessage($music_message);
	if($res === false) {
		print_r( $wechat->errMsg );
	}
	var_dump($res);
	