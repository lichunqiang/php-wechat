<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat;

class OauthClientTest extends \PHPUnit_Framework_TestCase
{
	protected $client;

	public function setUp()
	{
		$this->client = new OauthClient($GLOBALS['app_id'], $GLOBALS['app_secret']);
	}
	/**
	 * @expectedException \Light\Wechat\Exceptions\RuntimeException
	 */

	public function testInitException()
	{
		$client = new OauthClient();
	}

	/**
	 * @test
	 */
	public function getOauthRedirectUrl()
	{
		$redirect_uri = 'http://www.test.com';
		$scope = 'snsapi_base';
		$state = '';
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $GLOBALS['app_id'] . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';

		$this->assertEquals($url, $this->client->getOauthRedirect($redirect_uri, $state, $scope));
	}


}