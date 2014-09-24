<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Interfaces;

interface OauthClientInterface
{
	//常量

	const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
	const OAUTH_AUTHORIZE_URL = '/authorize?';
	const OAUTH_TOKEN_PREFIX = 'https://api.weixin.qq.com/sns/oauth2';
	const OAUTH_TOKEN_URL = '/access_token?';
	const OAUTH_REFRESH_URL = '/refresh_token?';
	const OAUTH_SNS_PREFIX = 'https://api.weixin.qq.com/sns';
	const OAUTH_USERINFO_URL = '/userinfo?';
	const OAUTH_AUTH_URL = '/auth?';

	/**
	 * 获取oauth授权地址
	 *
	 * @param string $redirect_uri 授权回调地址
	 * @param string $state 重定向后带上state参数，取值a-zA-Z0-9
	 * @param string $scope 应用授权作用域，取值：snsapi_base | snsapi_userinfo
	 * @return string
	 */
	public function getOauthRedirect($redirect_uri, $state='', $scope='snsapi_userinfo');

	/**
	 * 获取oauth授权的access_token
	 * 根据重定向返回的code值，请求微信获取oauth的access_token
	 *
	 * @param string 用户同意授权后，重定向带过来的code参数
	 * @return mixed
	 */
	public function getOauthAccessToken($code);

	/**
	 * 设置oauth的access_token
	 *
	 * @param string $access_token 授权获取的token
	 * @return self
	 */
	public function setOauthAccessToken($access_token);

	/**
	 * 刷新oauth授权获得的access_token，延长过期期限
	 *
	 * @param string $refresh_token 用于刷新用的token，由getOauthAccessToken中返回
	 * @return mixed
	 */
	public function refreshOauthAccessToken($refresh_token);


	/**
	 * 拉取用户信息(需要scope为snsapi_userinfo)
	 *
	 * @param string $openid 用户openid
	 * @return mixed
	 */
	public function getUserInfo($openid);

	/**
	 * 验证授权凭证(access_token)是否正确
	 *
	 * @param string $openid 用户的openid
	 * @return mixed
	 */
	public function authAccessToken($openid);
}