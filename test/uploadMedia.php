<?php
	require '../wechat.class.php';
	require '../php_fast_cache.php';
	require '../wechat.helper.php';
	//use file cache data
	phpFastCache::$storage = 'files';
	phpFastCache::$securityKey = 'logs';
	//phpFastCache::$debugging = true;
	
	function cacheFunc($key, $data, $expire = 1800) {
	
		phpFastCache::set($key, $data, $expire);
	}
	
	//wechat request config
	$wechat_option = array(
		'appid' => 'XXX',
		'appsecret' => 'XXXX',
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
	} else {
		echo 'this is use cache! <br />';;
		$wechat->setAccessToken($access_token);
	}
	$video_file = '@/data/www/wechat-php-sdk/source/test.mp4';
	//$image_file = '@/data/www/wechat-php-sdk/source/test.jpg';
	//$voice_file = '@/data/www/wechat-php-sdk/source/test.mp3';
	
	$media_list = $wechat->uploadMedia($video_file, 'video');
	if($media_list === false) {
		exit($wechat->errCode . '::' . $wechat->errMsg);
	}
	debug_print($media_list);
	$media_id = $media_list['media_id'];
	$msg_video_data = array(
      "media_id" => $media_id,
      "title" => "TITLE",
      "description" => "DESCRIPTION"
	);
	/*
		$msg_text_data = array(
			'content' => 'text content'
		);
		$msg_image_data = array(
			'media_id' => 'xxxxxxxx'
		);
		$msg_voice_data = array(
			'media_id' => 'xxxxxxxx'
		);
		$msg_music_data = array(
			"title" => "TITLE",
			"description" => "MUSIC DESCRIPTION",
			"musicurl" => "MUSIC_URL",
			"hqmusicurl" => "HQ_MUSIC_URL",
			"thumb_media_id" => "xxxx"
		);
		$msg_news_data = array(
			"articles" => array(
				array(
					"title" => "title",
					"description" => "description",
					"url" => "URL",
					"picurl" => "PIC_URL"
				),
				array(
					"title" => "title",
					"description" => "description",
					"url" => "URL",
					"picurl" => "PIC_URL"
				),
			)
		);
	*/
	//test send custom message
	$msg = $wechat->sendCustomMessage('oQLNOt-93kaIYZFiilUZpPomriqA', 'video', $msg_video_data);
	if(false === $msg) {
		exit($wechat->errCode . '::' . $wechat->errMsg);
	}
	debug_print($msg);