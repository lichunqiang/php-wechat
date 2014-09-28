<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Interfaces\Enterprise;

interface EnterpriseInterface
{
	const API_URL_PREFIX = 'https://qyapi.weixin.qq.com/cgi-bin';

	//token
	const AUTH_URL = '/gettoken?';

	//部门管理
	const DEPARTMENT_CREATE = '/create?';
	const DEPARTMENT_UPDATE = '/update?';
	const DEPARTMENT_LIST = '/list?';
	const DEPARTMENT_DELETE = '/delete?';

	//成员管理
	const USER_CREATE = '/user/create?';
	const USER_UPDATE = '/user/update?';
	const USER_DELETE = '/user/delete?';
	const USER_GET = '/user/get?';
	const USER_LIST = '/user/simplelist?';

	//标签
	const TAG_CREATE = '/tag/create?';
	const TAG_UPDATE = 'tag/update?';
	const TAG_DELETE = '/tag/delete?';
	const TAG_GET = '/tag/get?';
	const TAG_ADD_USER = '/tag/addtagusers?';
	const TAG_DEL_USER = '/tag/deltagusers?';

	//媒体文件
	const MEDIA_UPLOAD = '/media/upload?';
	const MEDIA_GET = '/media/get?';

	//发送消息
	const MSG_SEND = '/message/send?';

	//菜单
	const MENU_CREATE = '/menu/create?';
	const MENU_DELETE = '/menu/delete?';
	const MENU_GET = '/menu/get?';

	/**
	 * 获取access_token
	 *
	 * @return mixed
	 */
	public function getAccessToken();

	/**
	 * 设置access_token
	 *
	 * @param string $access_token
	 * @return self
	 */
	public function setAccessToken($access_token);

	//-----------通讯录管理

	/**
	 * 创建部门
	 * 管理员须拥有“操作通讯录”的接口权限，以及父部门的管理权限。
	 *
	 * @param $parentid 父亲部门ID，跟部门id为1.默认为1
	 * @param $name 部门名称。长度限制1~64个字符
	 * @param int $order 在父部门中的次序。从1开始，数字越大排序越靠后
	 * @return mixed boolean|int 成功返回创建的部门id
	 */
	public function createDepartment($name, $parentid = 1, $order = 1);

	/**
	 * 更新部门
	 * 管理员须拥有“操作通讯录”的接口权限，以及该部门的管理权限。
	 *
	 * @param int $id 部门id
	 * @param string $name 更新部门名称。长度限制0~64.修改部门名称时指定该参数
	 * @param int $order 在父部门中的次序。从1开始，数字越大排序越靠后
	 * @return mixed
	 */
	public function updateDepartment($id, $name = '');

	/**
	 * 删除部门
	 * 管理员须拥有“操作通讯录”的接口权限，以及该部门的管理权限。
	 *
	 * @param int $id 部门id
	 * @return mixed
	 */
	public function deleteDepartment($id);

	/**
	 * 获取部门列表
	 * 管理员须拥有“操作通讯录”的接口权限，以及该部门的管理权限。
	 *
	 * @return mixed
	 */
	public function getDepartmentList();

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
	public function createUser($userid, $name, $attrs = array());

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
	public function updateUser($userid, $attrs = array());

	/**
	 * 删除成员
	 * 管理员须拥有“操作通讯录”的接口权限，以及指定部门、成员的管理权限。
	 *
	 * @param int $userid 员工UserID。对应管理端的帐号
	 * @return mixed
	 */
	public function deleteUser($userid);

	/**
	 * 获取成员信息
	 * 管理员须拥有’获取成员’的接口权限，以及成员的查看权限。
	 *
	 * @param int $userid 员工UserID。对应管理端的帐号
	 * @return mixed
	 */
	public function getUserInfo($userid);

	/**
	 * 获取部门成员
	 * 管理员须拥有’获取部门成员’的接口权限，以及指定部门的查看权限。
	 *
	 * @param int $departmentid 获取的部门id
	 * @param int $fetch_child 1/0：是否递归获取子部门下面的成员
	 * @param int $status 0获取全部员工，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加
	 * @return mixed
	 */
	public function getDepartmentUserList($departmentid, $fetch_child = 0, $status = 0);

	//----------标签

	/**
	 * 创建标签
	 * 标签锁默认为未加锁状态
	 *
	 * @param string $tag_name 标签名称。长度为1~64个字符，标签不可与其他同组的标签重名，也不可与全局标签重名
	 * @return mixed
	 */
	public function createTag($tag_name);

	/**
	 * 更新标签名字
	 * 管理员必须是指定标签的创建者。
	 *
	 * @param int $tag_id 标签ID
	 * @param string $tag_name 标签名称。最长64个字符
	 * @return mixed
	 */
	public function updateTagName($tag_id, $tag_name);

	/**
	 * 删除标签
	 * 管理员必须是指定标签的创建者，并且标签的成员列表为空。
	 *
	 * @param int $tag_id 标签ID
	 * @return mixed
	 */
	public function deleteTag($tag_id);

	/**
	 * 获取标签成员
	 * 管理员须拥有“获取标签成员”的接口权限，标签须对管理员可见；返回列表仅包含管理员管辖范围的成员。
	 *
	 * @param int $tag_id 标签ID
	 * @return mixed
	 */
	public function getTagUserList($tag_id);

	/**
	 * 增加标签成员
	 * 标签对管理员可见且未加锁，成员属于管理员管辖范围。
	 *
	 * @param int $tag_id 标签ID
	 * @param array $user_list 企业员工ID列表
	 * @return mixed
	 */
	public function addTagUser($tag_id, $user_list = array());

	/**
	 * 删除标签成员
	 * 标签对管理员可见且未加锁，成员属于管理员管辖范围。
	 *
	 * @param int $tag_id 标签ID
	 * @param array $user_list 企业员工ID列表
	 * @return mixed
	 */
	public function deleteTagUser($tag_id, $user_list = array());

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
	public function uploadMedia($media, $type);

	/**
	 * 通过media_id获取图片、语音、视频等文件。
	 *
	 * @param string $media_id 媒体文件id
	 * @return mixed
	 */
	public function getMedia($media_id);

	/**
	 * 发送消息接口
	 * 需要管理员对应用有使用权限，对收件人touser、toparty、totag有查看权限，否则本次调用失败。
	 * 返回结果，如果存在不合法的touser、toparty、totag则会返回
	 *
	 * @param array $msg_body 发送消息的数据结构
	 * @return mixed
	 */
	public function sendMessage($msg_body);

	//----------------菜单

	/**
	 * 创建菜单
	 * 管理员须拥有应用的管理权限，并且应用必须设置在回调模式。
	 *
	 * @param array $menu 菜单数据结构
	 * @return mixed
	 */
	public function createMenu($menu);

	/**
	 * 删除菜单
	 *
	 * @return mixed
	 */
	public function deleteMenu();

	/**
	 * 获取菜单列表
	 *
	 * @return mixed
	 */
	public function getMenuList();
}