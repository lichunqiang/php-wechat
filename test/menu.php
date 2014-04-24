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
	$menu = $wechat->getMenu();
	var_dump($menu);
	
	//set menu
	$menu_data = array(
		'button' => array(
			array('type' => 'click', 'name' => 'click', 'key' => 'click'),
			array('name' => 'subutton', 'sub_button' => array(
				array('type' => 'click', 'name' => 'hello', 'key' => 'click'),
				array('type' => 'view', 'name' => 'world', 'url' => 'www.baidu.com')
			))
		)
	);
	
	$res = $wechat->createMenu($menu_data);
	if($res === false) {
		echo "menu create error.code::{$wechat->errCode}>>>{$wechat->errMsg}";
	}
	$menu = $wechat->getMenu();
	var_dump($menu);
