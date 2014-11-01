<?php
namespace Light\Wechat\Utils;

class MediaFileTest extends \PHPUnit_Framework_TestCase
{
	protected $file_obj;

	public function setUp()
	{
		$this->file_obj = new MediaFile('./fixtures/1.txt');
	}

	public function testName()
	{
		$this->assertEquals('1.txt', $this->file_obj->getBasename());
	}
}