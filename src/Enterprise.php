<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat;

use Light\Wechat\Interfaces\EnterpriseInterface;
use Light\Wechat\Utils\Helper;
use Light\Wechat\Exceptions\RuntimeException;

class Enterprise implements EnterpriseInterface
{
	/**
	 * 企业号的标识
	 *
	 * @var string
	 */
	public $corp_id;

	/**
	 * 管理组凭证密钥
	 *
	 * @var string
	 */
	public $secret;

	/**
	 * 接口票据
	 *
	 * @var string
	 */
	public $access_token;

	/**
	* 记录微信返回的错误码
	* @var int
	*/
	public $errcode;

	/**
	* 记录微信返回的错误消息
	* @var string
	*/
	public $errmsg;

	public function __construct($corp_id = null, $secret = null)
	{
		if(empty($corp_id) || empty($secret)) {
			throw new RuntimeException('缺少corp_id或者secret');
		}
		$this->corp_id = $corp_id;
		$this->secret = $secret;
	}

	/**
	 * 获取access_token
	 *
	 * @return mixed
	 */
	public function getAccessToken()
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::AUTH_URL . 'corpid=' . $this->corp_id . '&corpsecret' . $this->secret);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			if(isset($result['errcode'])) {
				$this->errcode = $result['errcode'];
				$this->errmsg = $result['errmsg'];
				return false;
			}
			$this->access_token = $result['access_token'];
			return $result;
		}
		return false
	}

	/**
	 * 设置access_token
	 *
	 * @param string $access_token
	 * @return self
	 */
	public function setAccessToken($access_token)
	{
		$this->access_token = $access_token;
		return $this;
	}

	//-----------通讯录管理

	/**
	 * 创建部门
	 * 管理员须拥有“操作通讯录”的接口权限，以及父部门的管理权限。
	 *
	 * @param $parentid 父亲部门ID，跟部门id为1.默认为1
	 * @param $name 部门名称。长度限制1~64个字符
	 * @return mixed boolean|int 成功返回创建的部门id
	 */
	public function createDepartment($name, $parentid = 1)
	{
		$body = array('name' => $name, 'parentid' => $parentid);
		$result = Helper::http_post(self::API_URL_PREFIX . self::DEPARTMENT_CREATE . 'access_token=' . $this->access_token, Helper::json_encode($body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return $result['id'];
		}
		return false;
	}

	/**
	 * 更新部门
	 * 管理员须拥有“操作通讯录”的接口权限，以及该部门的管理权限。
	 *
	 * @param int $id 部门id
	 * @param string $name 更新部门名称。长度限制0~64.修改部门名称时指定该参数
	 * @return mixed
	 */
	public function updateDepartment($id, $name = '')
	{
		$body = array('id' => $id);
		if($name) {
			$body['name'] = $name;
		}
		$result = Helper::http_post(self::API_URL_PREFIX . self::DEPARTMENT_UPDATE . 'access_token=' . $this->access_token, Helper::json_encode($body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * 删除部门
	 * 管理员须拥有“操作通讯录”的接口权限，以及该部门的管理权限。
	 *
	 * @param int $id 部门id
	 * @return mixed
	 */
	public function deleteDepartment($id)
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::DEPARTMENT_DELETE . 'access_token=' . $this->access_token . '&id=' . $id);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * 获取部门列表
	 * 管理员须拥有“操作通讯录”的接口权限，以及该部门的管理权限。
	 * 返回部门属性
	 * 		id			部门id
	 *		name		部门名称
	 *		parentid	父亲部门id。根部门为1
	 *
	 * @return mixed boolean|array 成功时返回部门列表
	 */
	public function getDepartmentList()
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::DEPARTMENT_LIST . 'access_token=' . $this->access_token);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return $result['department'];
		}
		return false;
	}

	/**
	 * 创建成员
	 * 管理员须拥有“操作通讯录”的接口权限，以及指定部门的管理权限。
	 * 其他属性：(非填项)
	 * 		department  成员所属部门id列表。注意，每个部门的直属员工上限为1000个
	 * 		position    职位信息。长度为0~64个字符
	 * 		mobile 		手机号码。企业内必须唯一，mobile/weixinid/email三者不能同时为空
	 * 		gender 		性别。gender=0表示男，=1表示女。默认gender=0
	 * 		tel    		办公电话。长度为0~64个字符
	 * 		email 		邮箱。长度为0~64个字符。企业内必须唯一
	 * 		weixinid	微信号。企业内必须唯一
	 *
	 * @param int $userid 员工UserID。对应管理端的帐号，企业内必须唯一
	 * @param string $name 成员名称。长度为1~64个字符
	 * @param array $attrs 员工的其他属性
	 * @return mixed
	 */
	public function createUser($userid, $name, $attrs = array())
	{
		$body = array('userid' => $userid, 'name' => $name);
		if(!empty($attrs) && is_array($attrs)) {
			$body = array_merge($body, $attrs);
		}
		$result = Helper::http_post(self::API_URL_PREFIX . self::USER_CREATE . 'access_token=' . $this->access_token, Helper::json_encode($body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * 更新成员
	 * 管理员须拥有“操作通讯录”的接口权限，以及指定部门的管理权限。
	 * 其他属性：(非填项)
	 * 		department  成员所属部门id列表。注意，每个部门的直属员工上限为1000个
	 * 		position    职位信息。长度为0~64个字符
	 * 		mobile 		手机号码。企业内必须唯一，mobile/weixinid/email三者不能同时为空
	 * 		gender 		性别。gender=0表示男，=1表示女。默认gender=0
	 * 		tel    		办公电话。长度为0~64个字符
	 * 		email 		邮箱。长度为0~64个字符。企业内必须唯一
	 * 		weixinid	微信号。企业内必须唯一
	 * 		enable 		启用/禁用成员。1表示启用成员，0表示禁用成员
	 *
	 * @param int $userid 员工UserID。对应管理端的帐号，企业内必须唯一
	 * @param array $attrs 需要更新的属性
	 * @return mixed
	 */
	public function updateUser($userid, $attrs = array())
	{
		$body = array('userid' => $userid);
		if(!empty($attrs) && is_array($attrs)) {
			$body = array_merge($body, $attrs);
		}
		$result = Helper::http_post(self::API_URL_PREFIX . self::USER_UPDATE . 'access_token=' . $this->access_token, Helper::json_encode($body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * 删除成员
	 * 管理员须拥有“操作通讯录”的接口权限，以及指定部门、成员的管理权限。
	 *
	 * @param int $userid 员工UserID。对应管理端的帐号
	 * @return mixed
	 */
	public function deleteUser($userid)
	{

		$result = Helper::http_get(self::API_URL_PREFIX . self::USER_DELETE . 'access_token=' . $this->access_token . '&userid=' . $userid);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * 获取成员信息
	 * 管理员须拥有’获取成员’的接口权限，以及成员的查看权限。
	 * 返回信息：
	 * 		userid		员工UserID
	 *		name		成员名称
	 *		department	成员所属部门id列表
	 *		position	职位信息
	 *		mobile		手机号码
	 *		gender		性别。gender=0表示男，=1表示女
	 *		tel			办公电话
	 *		email		邮箱
	 *		weixinid	微信号
	 *		avatar		头像url。注：如果要获取小图将url最后的"/0"改成"/64"即可
	 *		status		关注状态: 1=已关注，2=已冻结，4=未关注
	 *
	 * @param int $userid 员工UserID。对应管理端的帐号
	 * @return mixed
	 */
	public function getUserInfo($userid)
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::USER_GET . 'access_token=' . $this->access_token . '&userid=' . $userid);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return $result;
		}
		return false;
	}

	/**
	 * 获取部门成员
	 * 管理员须拥有’获取部门成员’的接口权限，以及指定部门的查看权限。
	 *
	 * @param int $departmentid 获取的部门id
	 * @param int $fetch_child 1/0：是否递归获取子部门下面的成员
	 * @param int $status 0获取全部员工，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加
	 * @return mixed boolean|array 成功返回成员列表
	 */
	public function getDepartmentUserList($departmentid, $fetch_child = 0, $status = 0)
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::USER_LIST . 'access_token=' . $this->access_token . '&department_id='
									. $departmentid . '&fetch_child=' . $fetch_child . '&status=' . $status);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return $result['userlist'];
		}
		return false;
	}
}