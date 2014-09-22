<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------

namespace Light\Wechat;

use Light\Wechat\Interfaces\OauthClientInterface;
use Light\Wechat\Exceptions\RuntimeException;
use Light\Wechat\Utils\Helper;

class OauthClient implements OauthClientInterface
{
	/**
	 * 公众号应用唯一标识
	 */
	protected $app_id;
	/**
	 * 公众号接口API的密钥key
	 */
	protected $app_secret;

	/**
	 * 授权获取的token
	 */
	public $access_token;

	/**
	 * 消息参数，用于请求过程返回的状态码和消息
	 */
	public $errcode;
	public $errmsg;

	public function __construct($app_id = null, $app_secret = null)
	{
		if(empty($app_id) || empty($app_secret)) {
			throw new RuntimeException('缺少必要参数');
		}
		$this->app_id = $app_id;
		$this->app_secret = $app_secret;
	}

	/**
	 * 获取oauth授权地址
	 *
	 * @param string $redirect_uri 授权回调地址
	 * @param string $state 重定向后带上state参数，取值a-zA-Z0-9
	 * @param string $scope 应用授权作用域，取值：snsapi_base | snsapi_userinfo
	 */
	public function getOauthRedirect($redirect_uri, $state='', $scope='snsapi_base')
	{
		return self::OAUTH_PREFIX . self::OAUTH_AUTHORIZE_URL . 'appid=' . $this->app_id
						. '&redirect_uri=' . urlencode($redirect_uri)
						. '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
	}

	/**
	 * 获取oauth授权的access_token
	 * 根据重定向返回的code值，请求微信获取oauth的access_token
	 *
	 * @param string 用户同意授权后，重定向带过来的code参数
	 * @return mixed
	 */
	public function getOauthAccessToken($code)
	{
		$result = Helper::http_get(self::OAUTH_TOKEN_PREFIX . self::OAUTH_TOKEN_URL . 'appid=' . $this->app_id
									. '&secret=' . $this->app_secret . '&code=' . $code . '&grant_type=authorization_code');
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			if(isset($result['errocde'])) {
				$this->errcode = $result['errcode'];
				$this->errmsg = $result['errmsg'];
				return false;
			}
		    //{"access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE"}
			$this->access_token = $result['access_token'];
			return $result;
		}
		return false;
	}

	/**
	 * 设置oauth的access_token
	 *
	 * @param string $access_token 授权获取的token
	 * @return self
	 */
	public function setOauthAccessToken($access_token)
	{
		$this->access_token = $access_token;
		return $this;
	}

	/**
	 * 刷新oauth授权获得的access_token，延长过期期限
	 *
	 * @param string $refresh_token 用于刷新用的token，由getOauthAccessToken中返回
	 * @return mixed
	 */
	public function refreshOauthAccessToken($refresh_token)
	{
		$result = Helper::http_get(self::OAUTH_TOKEN_PREFIX . self::OAUTH_REFRESH_URL . 'appid=' . $this->app_id
									. '&grant_type=refresh_token&refresh_token=' . $refresh_token);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			if($result['errcode'] != 0) {
				$this->errcode = $result['errcode'];
				$this->errmsg = $result['errmsg'];
				return false;
			}
			$this->access_token = $result['access_token'];
			return $result;
		}
		return false;
	}


	/**
	 * 拉取用户信息(需要scope为snsapi_userinfo)
	 * {openid,nickname,sex,province,city,country,headimgurl,privilege}
	 *
	 * @param string $openid 用户openid
	 * @return mixed
	 */
	public function getUserInfo($openid)
	{
		if(empty($this->access_token)) {
			throw new RuntimeException('access_token不能为空');
		}
		$result = Helper::http_get(self::OAUTH_SNS_PREFIX . self::OAUTH_USERINFO_URL . 'access_token=' . $this->access_token . '&openid=' . $openid . '&lang=zh_CN');
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			if($result['errcode'] != 0) {
				$this->errcode = $result['errcode'];
				$this->errmsg = $result['errmsg'];
				return false;
			}
			return $result;
		}
		return false;
	}

	/**
	 * 验证授权凭证(access_token)是否正确
	 *
	 * @param string $openid 用户的openid
	 * @return mixed
	 */
	public function authAccessToken($openid)
	{
		if(empty($this->access_token)) {
			throw new RuntimeException('access_token不能为空');
		}
		$result = Helper::http_get(self::OAUTH_SNS_PREFIX . self::OAUTH_AUTH_URL . 'access_token=' . $this->access_token . '&openid=' . $openid);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;

			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			return true;
		}
		return false;
	}
}