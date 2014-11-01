<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
require __DIR__ . '/../init.php';

$file = new \Light\Wechat\Utils\MediaFile('../fixture/panda1.jpg', 'thumb');

//echo $file->getSize();

// echo 1024 * 1024;
var_dump((string) $file);
