<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Utils;

class MediaFile extends \SplFileInfo
{
    /**
     * 微信支持的文件类型
     */
    protected $valid_ext = array('jpg', 'arm', 'mp3', 'mp4');

    /**
     * 文件类型对应的mime_type
     */
    protected $mime_types = array('mp4' => 'video/mp4', 'arm' => 'audio/arm', 'jpg' => 'image/jpeg', 'mp3' => 'audio/mp3');

    /**
     * 文件大小限制(k)
     */
    protected $file_limit = array(
        'image' => 128,
        'thumb' => 64,
        'voice' => 256,
        'video' => 1024,
    );

    public function __construct($file_name, $file_type = 'image')
    {
        parent::__construct($file_name);
        if (!in_array($file_type, array_keys($this->file_limit))) {

            throw new \UnexpectedValueException('不支持的文件类型');
        }
        $this->file_type = $file_type;
    }

    /**
     * 获取上传媒体
     * PHP5.5.0 废弃了@file 形式发送文件，用curl_file_create(CURLFile Obj)
     * @see http://cn2.php.net/manual/en/function.curl-file-create.php
     * @return mixed file related thing
     */
    public function media()
    {
        $this->checker();
        return $this->mediaString();
    }

    private function checker()
    {
        //检查文件是否存在
        if (!$this->isFile()) {
            throw new \InvalidArgumentException('文件不存在');
        }
        //文件格式是否合法
        if (!in_array($this->getExtension(), $this->valid_ext)) {
            throw new \UnexpectedValueException('不支持的文件类型');
        }
        $file_size = $this->getSize() / 1024;//k
        if ($file_size > $this->file_limit[$this->file_type]) {

            throw new \UnexpectedValueException('文件大小超过' . $this->file_limit[$this->file_type] . ' K');
        }
    }

    /**
     * 返回媒体文件上传标识
     * @/data/www/1.jpg;type=image/jpg
     *
     * @return string
     */
    private function mediaString()
    {
        $path = $this->getRealPath();
        $ext = $this->getExtension();
        $mime_type = isset($this->mime_types[$ext]) ? $this->mime_types[$ext] : 'application/octet-stream';
        if (function_exists('curl_file_create')) {
            return curl_file_create($path, $mime_type);
        }
        return '@' . $path . ';type=' . $mime_type;
    }

    public function __toString()
    {
        return $this->mediaString();
    }

}
