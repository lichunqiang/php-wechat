<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Interfaces;

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
	 * @return mixed boolean|int 成功返回创建的部门id
	 */
	public function createDepartment($name, $parentid = 1);

	/**
	 * 更新部门
	 * 管理员须拥有“操作通讯录”的接口权限，以及该部门的管理权限。
	 *
	 * @param int $id 部门id
	 * @param string $name 更新部门名称。长度限制0~64.修改部门名称时指定该参数
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

}