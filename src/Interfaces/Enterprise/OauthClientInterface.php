<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Interfaces\Enterprise;

interface OauthClientInterface
{
	const API_URL_PREFIX = 'https://qyapi.weixin.qq.com/cgi-bin';

	const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
	const OAUTH_AUTHORIZE_URL = '/authorize?';

	//用户信息
	const USER_GET_INFO = '/user/getuserinfo?';

	/**
	 * 获取oauth授权地址
	 *
	 * @param string $redirect_uri 授权回调地址
	 * @param string $state 重定向后带上state参数，取值a-zA-Z0-9
	 * @param string $scope 应用授权作用域，取值：snsapi_base
	 * @return string
	 */
	public function getOauthRedirect($redirect_uri, $state='', $scope='snsapi_base');

	/**
	 * 获取成员信息
	 *
	 * @param string $code 通过员工授权获取到的code
	 * @return mixed
	 */
	public function getUserInfoByCode($code);
}