<?php

namespace Light\Wechat\Utils;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNonceStr()
    {
        $this->assertEquals(16, strlen(Helper::getNonceStr(16)));
        $this->assertNotEquals(16, strlen(Helper::getNonceStr(13)));

        $this->assertEquals(16, strlen(Helper::getNonceStr('this is string')));
        $this->assertEquals(16, strlen(Helper::getNonceStr(89)));

        $this->assertEquals('', Helper::getNonceStr(0));
    }

    public function testJsonencode()
    {
        $mock_arr = array('name' => "微信", 'inner' => array('age' => 12, 'sex' => '男'));
        $target_json = '{"name":"微信","inner":{"age":12,"sex":"男"}}';

        $this->assertJsonStringEqualsJsonString($target_json, Helper::jsonEncode($mock_arr));
        $this->assertJsonStringEqualsJsonString(json_encode($mock_arr), Helper::jsonEncode($mock_arr));
    }

    public function testArrayToXml()
    {
        $arr = array('age' => 1, 'name' => 'mock name');
        $target = '<xml><age>1</age><name><![CDATA[mock name]]></name></xml>';

        $this->assertEquals('', Helper::arrayToXml('test'));
        $this->assertXmlStringEqualsXmlString($target, Helper::arrayToXml($arr));
    }

    public function testXmlToArray()
    {
        $xml = '<xml><OpenId><![CDATA[oRptouFiIwDyrK0BzDKEDKwC8ess]]></OpenId></xml>';
        $target = array('OpenId' => 'oRptouFiIwDyrK0BzDKEDKwC8ess');

        $this->assertEquals($target, Helper::xmlToArray($xml));
    }

    public function testXmlToArrayException()
    {
        $xml = '<xml><OpenId><![CDATA[oRptouFiIwDyrK0BzDKEDKwC8ess]]></OpenId></xml>';

        Helper::xmlToArray($xml);

        // $this->markTestIncomplete('help wanted, and this is not incomplete');
        //$this->markTestSkipped('skipped this');
    }

    public function testFormatQueryParamMap()
    {
        $param = array('name' => 'light li', 'age' => 12, 'body' => null);

        $this->assertEquals('name=light%20li&age=12', Helper::formatQueryParamMap($param, true));
        $this->assertEquals('name=light li&age=12', Helper::formatQueryParamMap($param));
    }

    public function testFormatBizQueryParamMap()
    {
        $param = array('name' => 'light li', 'age' => 12);
        $this->assertEquals('name=light%20li&age=12', Helper::formatBizQueryParamMap($param, true));
        $this->assertEquals('name=light li&age=12', Helper::formatBizQueryParamMap($param));
    }

    // public function testMd5Sign()
    // {
    //     $this->markTestIncomplete('not complete');
    // }
}
