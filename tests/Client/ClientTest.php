<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    protected function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('curl is not supported!!');
        }
        $this->client = new Client($GLOBALS['app_id'], $GLOBALS['app_secret']);
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    /**
     * @test
     * @expectedException Light\Wechat\Exceptions\RuntimeException
     */
    public function initException()
    {
        $client = new Client();
    }

    /**
     * @test
     */
    public function getAccessToken()
    {
        $result = $this->client->getAccessToken();
        $result = array('access_token' => $GLOBALS['access_token']);
        $this->client->access_token = $result['access_token'];

        $this->assertNotEquals(false, $result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertNotNull($this->client->access_token);

        return $result['access_token'];
    }

    /**
     * @test
     * @depends getAccessToken
     */
    public function setAccessToken($access_token)
    {
        $this->assertEquals($this->client, $this->client->setAccessToken($access_token));
    }

    /**
     * @test
     * @depends getAccessToken
     */
    public function uploadMedia($access_token)
    {
        $this->assertEquals('test', $access_token);
    }
}
