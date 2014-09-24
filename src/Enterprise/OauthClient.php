<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Enterprise;

use Light\Wechat\Interfaces\Enterprise\OauthClientInterface;
use Light\Wechat\Exceptions\RuntimeException;
use Light\Wechat\Utils\Helper;

class OautClient extends OauthClientInterface
{
	/**
	 * 企业的CorpID
	 */
	protected $corp_id;

	/**
	 * 跳转链接时所在的企业应用ID
	 */
	public $agent_id;

	/**
	 * 消息参数，用于请求过程返回的状态码和消息
	 */
	public $errcode;
	public $errmsg;

	public function __construct($corp_id = null)
	{
		if(empty($corp_id)) {
			throw new RuntimeException('缺少必要参数');
		}
		$this->corp_id = $corp_id;
	}

	public function setAgentId($agent_id)
	{
		$this->agent_id = $agent_id;
		return $this;
	}

	/**
	 * 获取oauth授权地址
	 *
	 * @param string $redirect_uri 授权回调地址
	 * @param string $state 重定向后带上state参数，取值a-zA-Z0-9
	 * @param string $scope 应用授权作用域，取值：snsapi_base
	 * @return string
	 */
	public function getOauthRedirect($redirect_uri, $state='', $scope='snsapi_base')
	{
		return self::OAUTH_PREFIX . self::OAUTH_AUTHORIZE_URL . 'appid=' . $this->corp_id
						. '&redirect_uri=' . urlencode($redirect_uri)
						. '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
	}

	/**
	 * 获取成员信息
	 *
	 * @param string $code 通过员工授权获取到的code
	 * @return mixed
	 */
	public function getUserInfoByCode($code)
	{
		if(empty($this->corp_id) || empty($this->agent_id)) {
			throw new RuntimeException('缺少corp_id或者agent_id');
		}
		$result = Helper::http_get(self::API_URL_PREFIX . self::USER_GET_INFO . 'access_token=' . $this->access_token
									. '&code=' . $code . '&agentid=' . $this->agent_id);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			if(isset($result['errcode'])) {
				$this->errcode = $result['errcode'];
				$this->errmsg = $result['errmsg'];
				return false;
			}
			return $result['UserId'];
		}
		return false
	}
}