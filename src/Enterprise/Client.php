<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Enterprise;

use Light\Wechat\Interfaces\Enterprise\ClientInterface;
use Light\Wechat\Utils\Helper;
use Light\Wechat\Exceptions\RuntimeException;

class Client implements ClientInterface
{
	/**
	 * 企业应用的id，整型。可在应用的设置页面查看
	 *
	 * @var string
	 */
	public $agent_id;

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

	/**
	 * 支持上传的媒体类型
	 * @var array
	 */
	private $support_media_type = array('image', 'voice', 'video', 'file');

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
	 * @param int $parentid 父亲部门ID，跟部门id为1.默认为1
	 * @param string $name 部门名称。长度限制1~64个字符
	 * @param int $order 在父部门中的次序。从1开始，数字越大排序越靠后
	 * @return mixed boolean|int 成功返回创建的部门id
	 */
	public function createDepartment($name, $parentid = 1, $order = 1)
	{
		$body = array('name' => $name, 'parentid' => $parentid, 'order' => $order);
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
	 * @param int $order 在父部门中的次序。从1开始，数字越大排序越靠后
	 * @return mixed
	 */
	public function updateDepartment($id, $name = '', $order = 1)
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

	//----------标签

	/**
	 * 创建标签
	 * 标签锁默认为未加锁状态
	 *
	 * @param string $tag_name 标签名称。长度为1~64个字符，标签不可与其他同组的标签重名，也不可与全局标签重名
	 * @return mixed boolean|int 成功返回创建的标签ID
	 */
	public function createTag($tag_name)
	{
		$body = array('tagname' => $tag_name);
		$result = Helper::http_post(self::API_URL_PREFIX . self::TAG_CREATE . 'access_token=' . $this->access_token,
									Helper::json_encode($body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0) {
				return false;
			}
			return $result['tagid'];
		}
		return false;
	}

	/**
	 * 更新标签名字
	 * 管理员必须是指定标签的创建者。
	 *
	 * @param int $tag_id 标签ID
	 * @param string $tag_name 标签名称。最长64个字符
	 * @return mixed
	 */
	public function updateTagName($tag_id, $tag_name)
	{
		$body = array('tagid' => $tag_id, 'tagname' => $tag_name);
		$result = Helper::http_post(self::API_URL_PREFIX . self::TAG_UPDATE . 'access_token=' . $this->access_token,
									Helper::json_encode($body));
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
	 * 删除标签
	 * 管理员必须是指定标签的创建者，并且标签的成员列表为空。
	 *
	 * @param int $tag_id 标签ID
	 * @return mixed
	 */
	public function deleteTag($tag_id)
	{
		$result = http_get(self::API_URL_PREFIX . self::TAG_DELETE . 'access_token=' . $this->access_token . '&tagid=' . $tag_id);
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
	}

	/**
	 * 获取标签成员
	 * 管理员须拥有“获取标签成员”的接口权限，标签须对管理员可见；返回列表仅包含管理员管辖范围的成员。
	 *
	 * @param int $tag_id 标签ID
	 * @return mixed boolean|array 成功时返回标签下的成员列表
	 */
	public function getTagUserList($tag_id)
	{
		$result = http_get(self::API_URL_PREFIX . self::TAG_GET . 'access_token=' . $this->access_token . '&tagid=' . $tag_id);
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

	/**
	 * 增加标签成员
	 * 标签对管理员可见且未加锁，成员属于管理员管辖范围。
	 *
	 * @param int $tag_id 标签ID
	 * @param array $user_list 企业员工ID列表
	 * @return mixed
	 */
	public function addTagUser($tag_id, $user_list = array())
	{
		if(!is_array($user_list) || empty($user_list)) {
			return false;
		}
		$body = array('tagid' => $tag_id, 'userlist' => $user_list);
		$result = Helper::http_post(self::API_URL_PREFIX . self::TAG_ADD_USER . 'access_token=' . $this->access_token,
									Helper::json_encode($body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0 || isset($result['invalidlist'])) {
				//TODO::将非法的userid反馈给开发者
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * 删除标签成员
	 * 标签对管理员可见且未加锁，成员属于管理员管辖范围。
	 *
	 * @param int $tag_id 标签ID
	 * @param array $user_list 企业员工ID列表
	 * @return mixed
	 */
	public function deleteTagUser($tag_id, $user_list = array())
	{
		if(!is_array($user_list) || empty($user_list)) {
			return false;
		}
		$body = array('tagid' => $tag_id, 'userlist' => $user_list);
		$result = Helper::http_post(self::API_URL_PREFIX . self::TAG_DEL_USER . 'access_token=' . $this->access_token,
									Helper::json_encode($body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if($result['errcode'] != 0 || isset($result['invalidlist'])) {
				//TODO::将非法的userid反馈给开发者
				return false;
			}
			return true;
		}
		return false;
	}

	//-------多媒体文件

	/**
	 * 上传多媒体文件
	 * 图片（image）: 1MB，支持JPG格式
	 * 语音（voice）：2MB，播放长度不超过60s，支持AMR格式
	 * 视频（video）：10MB，支持MP4格式
	 * 普通文件（file）：10MB
	 * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
	 * 媒体文件在后台保存时间为3天，即3天后media_id失效。
	 * 返回->{"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
	 *
	 * @param string $media form-data中媒体文件标识，有filename、filelength、content-type等信息
	 * @param string $type 媒体文件类型: image, voice, video, file
	 * @return mixed
	 */
	public function uploadMedia($media, $type)
	{
		$body = array('media' => $media, 'type' => $type);
		$result = Helper::http_post(self::API_URL_PREFIX . self::MEDIA_UPLOAD .'access_token=' . $this->access_token, $body, true);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;

			if(isset($result['errcode'])) {
				$this->errcode = $result['errcode'];
				$this->errmsg = $result['errmsg'];
				return false;
			}
			return $result;
		}
		return false;
	}

	/**
	 * 通过media_id获取图片、语音、视频等文件。
	 * 完全公开。所有管理员均可调用，media_id可以共享。
	 *
	 * @param string $media_id 媒体文件id
	 * @return mixed
	 */
	public function getMedia($media_id)
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::MEDIA_GET . 'access_token=' . $this->access_token . '&media_id=' . $media_id);
		//return directly
		return $result;
	}

	/**
	 * 获取上传媒体文件所支持的文件类型
	 *
	 * @return array
	 */
	public function getSupportMediaType()
	{
		return $this->support_media_type;
	}

	//----------设置应用id

	/**
	 * 设置企业应用的ID
	 *
	 * @param string $agent_id
	 * @return self
	 */
	public function setAgentId($agent_id)
	{
		$this->agent_id = $agent_id;
		return $this;
	}

	//----------------发送消息

	/**
	 * 发送消息接口
	 * 需要管理员对应用有使用权限，对收件人touser、toparty、totag有查看权限，否则本次调用失败。
	 * 返回结果，如果存在不合法的touser、toparty、totag则会返回
	 *
	 * @param array $msg_body 发送消息的数据结构
	 * @return mixed
	 */
	public function sendMessage($msg_body)
	{
		$result = Helper::http_post(self::API_URL_PREFIX . self::MSG_SEND . 'access_token=' . $this->access_token,
									Helper::json_encode($msg_body));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;

			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			//TODO::返回不合法数据给开发者
			if($result['errcode'] != 0) {
				return false;
			}
			return true;
		}
		return false;
	}

	//----------------菜单

	/**
	 * 创建菜单
	 * 管理员须拥有应用的管理权限，并且应用必须设置在回调模式。
	 *
	 * @param array $menu 菜单数据结构
	 * @return mixed
	 */
	public function createMenu($menu)
	{
		$result = Helper::http_post(self::API_URL_PREFIX . self::MENU_CREATE . 'access_token=' . $this->access_token . '&agentid=' . $this->agentid,
									Helper::json_encode($menu));
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
	 * 删除菜单
	 *
	 * @return mixed
	 */
	public function deleteMenu()
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::MENU_DELETE . 'access_token='. $this->access_token . '&agentid=' . $this->agent_id);
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
	 * 获取菜单列表
	 *
	 * @return mixed
	 */
	public function getMenuList()
	{
		$result = Helper::http_get(self::API_URL_PREFIX . self::MENU_GET . 'access_token='. $this->access_token . '&agentid=' . $this->agent_id);
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;

			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			if(isset($result['errcode'])) {
				return false;
			}
			return $result;
		}
		return false;
	}
}