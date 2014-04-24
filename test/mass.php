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
		'appid' => 'wxc0f8096d1a552819',//'wx35f0265338a63764',
		'appsecret' => '64e8ffd2017b9a4624464f7b2ed2996c',//'fb535bf1c45c8295d43bb0d8d893ccb7',
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
	$group_list = phpFastCache::get('group_list');
	
	if($group_list == null) {
		//获取group列表
		$group_list = $wechat->getGroup();
		if($group_list === false) {
			exit($wechat->errMsg);
		}		
		phpFastCache::set('group_list', $group_list, 60 * 60 * 24 );
		echo 'no cached <br />';
	}else {
		echo 'cached gropu list';
	}
	debug_print($group_list);
	
	$media_list = phpFastCache::get('media_list');
	if($media_list == null) {
		//上传缩略图
		$thumb_file = '@/data/www/wechat-php-sdk/test/test.jpg;type=image/jpg';
		$media_list = $wechat->uploadMedia($thumb_file, 'image');
		if($media_list === false) {
			exit($wechat->errCode . '::' . $wechat->errMsg);
		}
		
		phpFastCache::set('media_list', $media_list, 60 * 60 * 24 * 3);//3days
	} else {
		echo 'cached media data';
	}
	debug_print($media_list);	
	//K3oU-TqGUPGncPziWWoxiRnO_6XDQeVj4mh_Icr2LTq5zo_8JPSaO4TRlJbCnAUA
	$media_id = $media_list['media_id'];
	
	$articles = array(
		array(
			'thumb_media_id' => $media_id,
			'author' => 'test',
			'title' => 'test news',
			'content_source_url' => 'www.baidu.com',
			'content' => '<strong>test</strong>',
			'digest' => 'description'
		)
	
	);
	$test_article = phpFastCache::get('test_article');
	if($test_article == null) {
		//上传图文消息素材
		$test_article = $res = $wechat->uploadArticle($articles);
		if($res === false) {
			exit($wechat->errCode . '::' . $wechat->errMsg);
		}
		
		phpFastCache::set('test_article', $res, 60 * 60 * 24 * 3);
	}else{
		echo 'cached news data';
	}
	debug_print($test_article);	
	$media_id = $test_article['media_id'];
	
	
	//test access token is right to use
	//debug_print($wechat->getGroup());
	
	//发送消息
	//$res = $wechat->massSendByGroup($media_id, 0);
	//var_dump($res);
	$msg_id = $wechat->massSendByOpenId($media_id, array('ovH72jlpPNFetpVItqVEcyg4-Qm4'));
	if($msg_id == false) {
		exit($wechat->errCode . '::' . $wechat->errMsg);
	}
	debug_print($msg_id);
	var_dump($msg_id);
	//测试删除消息
	//$res = $wechat->massDelete(2347927027);//2347926987//2347927027
	debug_print($res);
	
/**
<xml><ToUserName><![CDATA[gh_c90c388e14fe]]></ToUserName>
<FromUserName><![CDATA[ovH72joK0On23hG7BqX1ySto695M]]></FromUserName>
<CreateTime>1398077195</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[MASSSENDJOBFINISH]]></Event>
<MsgID>2347927027</MsgID>
<Status><![CDATA[send success]]></Status>
<TotalCount>1</TotalCount>
<FilterCount>1</FilterCount>
<SentCount>1</SentCount>
<ErrorCount>0</ErrorCount>
</xml>

**/
	