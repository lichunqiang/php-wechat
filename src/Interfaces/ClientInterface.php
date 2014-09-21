<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Interfaces;

interface ClientInterface
{
	//常量
	const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
	const FILE_API_URL_PREFIX = 'http://file.api.weixin.qq.com/cgi-bin';
	const QRCODE_IMG_URL_PREFIX ='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';

	const AUTH_URL = '/token?grant_type=client_credential&';

	const MENU_CREATE_URL = '/menu/create?';
	const MENU_GET_URL = '/menu/get?';
	const MENU_DELETE_URL = '/menu/delete?';

	const MEDIA_GET_URL = '/media/get?';
	const UPLOAD_ARTICLE_URL = '/media/uploadnews?';
	const MEDIA_UPLOAD_URL = '/media/upload?';
	const UPLOAD_VIDEO_URL = '/media/uploadvideo?';

	const QRCODE_CREATE_URL = '/qrcode/create?';
	const SHORTURL_CREATE = '/shorturl?';

	const MESSAGE_SENDGROUP_URL = '/message/mass/sendall?';
	const MESSAGE_SENDUSER_URL = '/message/mass/send?';
	const MESSAGE_DELETE_URL = '/message/mass/delete?';
	const CUSTOM_SEND_URL='/message/custom/send?';
	const TEMPLATE_SEND_URL = '/message/template/send?';

	const QR_SCENE = 0;
	const QR_LIMIT_SCENE = 1;

	const USER_GET_URL='/user/get?';
	const USER_INFO_URL='/user/info?';
	const USER_UPDATEREMARK_URL='/user/info/updateremark?';

	const USER_GROUP_URL = '/groups/getid?';
	const GROUP_GET_URL = '/groups/get?';
	const GROUP_CREATE_URL =' /groups/create?';
	const GROUP_UPDATE_URL = '/groups/update?';
	const GROUP_MEMBER_UPDATE_URL = '/groups/members/update?';

	const CUSTOM_SERVICE_GET_RECORD = '/customservice/getrecord?';
	const CUSTOM_SERVICE_GET_KFLIST = '/customservice/getkflist?';
	const CUSTOM_SERVICE_GET_ONLINEKFLIST = '/customservice/getkflist?';

	/**
	 * 使用AppID和AppSecret调用本接口来获取access_token
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

	//--------------------多媒体文件相关

	/**
	 * 上传多媒体文件
	 * 图片（image）: 128K，支持JPG格式
	 * 语音（voice）：256K，播放长度不超过60s，支持AMR\MP3格式
	 * 视频（video）：1MB，支持MP4格式
	 * 缩略图（thumb）：64KB，支持JPG格式
	 * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
	 * 媒体文件在后台保存时间为3天，即3天后media_id失效。
	 * 返回->{"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
	 *
	 * @param string $media form-data中媒体文件标识，有filename、filelength、content-type等信息
	 * @param string $type 媒体文件类型: image, voice, video, thumb
	 * @return mixed
	 */
	public function uploadMedia($media, $type);

	/**
	 * 根据媒体文件ID获取媒体文件
	 * 视频不支持下载，需要http协议
	 *
	 * @param string $media_id 媒体文件ID
	 * @return raw data
	 */
	public function getMedia($media_id);

	/**
	 * 获取上传视频文件的media_id
	 * 高级群发接口中发送视频信息需要的media_id需要进一步处理
	 *
	 * @param string $media_id 通过上传多媒体文件(视频)获取的media_id
	 * @param string $title 视频的标题
	 * @param string $desc 视频的描述
	 * @return mixed
	 */
	public function getUploadedVideoMediaId($media_id, $title, $desc);

	//---------------------发送消息--------

	/**
	 * 发送客服消息
	 * 用户主动发消息给公众账号(发送消息、点击自定义菜单、订阅、扫码、支付成功、维权)
	 * 在(目前)48小时内可以调用此接口无限制发送消息给用户
	 *
	 * @param string $openid 用户的openid
	 * @param string $msg_type 消息类型
	 * @param array $msg_data 消息内容,根据不同的消息类型进行构造
	 * @return mixed
	 */
	public function sendCustomerMsg($openid, $msg_type, $msg_data);

	/**
	 * 上传图文消息素材
	 *
	 * @param array $articles 图文消息数据,最大支持10条
	 * @return mixed
	 */
	public function uploadArticleMaterial($articles = array());

	/**
	 * 根据群组进行群发消息
	 *
	 * @param int $groupid 发送分组的ID
	 * @param string $body 文本消息的content，媒体消息的media_id
	 * @param string $msg_type 消息类型 mpnews|voice|image|mpvideo|text
	 * @return mixed
	 */
	public function sendMsgByGroup($groupid, $body, $msg_type);

	/**
	 * 根据用户的openid群发消息
	 *
	 * @param array $openid_list 消息接收者的openid列表，最大10000个
	 * @param string|array $body 发送消息体，文本消息的content，媒体消息的media_id
	 * @param string $msg_type 消息类型 mpnews|voice|image|mpvideo|text
	 * @return mixed
	 */
	public function sendMsgByOpenid($openid_list = array(), $body, $msg_type);

	/**
	 * 删除群发消息
	 *
	 * @param int $msg_id 通过群发接口返回的群发消息的ID
	 * @return mixed
	 */
	public function deleteMessage($msg_id);

	/**
	 * 发送模板消息
	 *
	 * @param array $body 模板消息的内容主体
	 * @return mixed
	 */
	public function sendTemplateMsg($body);



	//---------------------用户管理--------

	/**
	 * 获取用户个人信息
	 *
	 * @param string $openid
	 * @param string $lang 指定返回国家地区的语言版本:zh_CN 简体, zh_TW繁体,en英语.默认zh_CN
	 * @return mixed
	 */
	public function getUserInfo($openid, $lang = 'zh_CN');

	/**
	 * 新增自定义分组
	 * POST数据为：{"group": {"name": "test"}}
	 * 正确返回：{"group": {"id": 107, "name":"test"}}
	 *
	 * @param string $name 分组名称
	 *
	 */
	public function createGroup($name);

	/**
	 * 查询所有分组
	 * 正确返回：{"groups": [{"id": 0, "name": "未分组", "count": 231}..]}
	 *
	 * @return Boolean|Array
	 */
	public function getGroup();

	/**
	 * 查询用户所在分组
	 * POST: {"openid":"od8XIjsmk6QdVTETa9jLtGWA6KBc"}
	 * 正确返回: {"groupid": 102}
	 *
	 * @param string $openid 用户的OPENID
	 * @return
	 */
	public function getUserGroupId($openid);

	/**
	 * 修改分组名称
	 * POST: {"group":{"id":108,"name":"test2_modify2"}}
	 * 分组名称30个字符以内
	 *
	 * @param int $groupid 修改的分组ID
	 * @param string $name 修改后的分组名称
	 * @return mixed
	 */
	public function updateGroupName($groupid, $name);

	/**
	 * 移动用户所在的分组
	 * POST:{"openid":"oDF3iYx0ro3_7jD4HFRDfrjdCM58","to_groupid":108}
	 *
	 * @param string $openid 用户的OPEND
	 * @param int 目标分组ID
	 * @return mixed
	 */
	public function moveUserGroup($openid, $to_groupid);

	/**
	 * 设置用户的备注名
	 * POST: {"openid":"oDF3iYx0ro3_7jD4HFRDfrjdCM58","remark":'test'}
	 *
	 * @param string $openid 用户的openid
	 * @param string $remark 修改后的用户备注名
	 * @return mixed
	 */
	public function updateUserRemark($openid, $remark);

	/**
	 * 批量获取关注者列表
	 * 当公众号关注者数量超过10000时，可通过填写next_openid的值，从而多次拉取列表的方式来满足需求
	 * 在调用接口时，将上一次调用得到的返回中的next_openid值，作为下一次调用中的next_openid值
	 *
	 * @param string $next_openid 用户的openid,以此来分批拉取
	 * @return mixed
	 */
	public function getUserList($next_openid = '');


	//---------------------自定义菜单------

	/**
	 * 自定义菜单创建接口
	 * button: 一级菜单数组，个数应为1~3个
	 * sub_button: 二级菜单数组，个数应为1~5个
	 * type: 菜单的响应动作类型，目前有click、view两种类型
	 * name: 菜单标题，不超过16个字节，子菜单不超过40个字节
	 * key: click类型必须,菜单KEY值，用于消息接口推送，不超过128字节
	 * url: view类型必须,网页链接，用户点击菜单可打开链接，不超过256字节
	 * 成功返回：{"errcode":0,"errmsg":"ok"}
	 *
	 * @param string $menu_data 菜单数据结构的json串
	 * @return mixed
	 */
	public function createMenu($menu_data);

	/**
	 * 获取当前菜单结构
	 * {"menu": {"button": [....]}}
	 *
	 * @return mixed
	 */
	public function getMenu();

	/**
	 * 自定义菜单删除
	 * 正确返回：{"errcode":0,"errmsg":"ok"}
	 *
	 * @return mixed
	 */
	public function deleteMenu();

	//---------------------推广支持--------

	/**
	 * 创建二维码ticket
	 *
	 * @param int $scene_id 场景值ID，临时二维码为32位非0整型，永久二维码时最大值为100000(1-100000)
	 * @param int $qrcode_type 0:临时二维码 1.永久二维码 默认为0
	 * @param int $expire 临时二维码有效期，最大不超过1800
	 * @return mixed
	 */
	public function createQRCodeTicket($scene_id, $qrcode_type = 0, $expire = 1800);

	/**
	 * 通过ticket换取二维码
	 *
	 * @param $ticket 用于换取二维码的ticket(需要urlencode)
	 * @return mixed 正确返回二维码图片的raw data
	 */
	public function getQRCode($ticket);

	/**
	 * 长链接转短链接
	 *
	 * @param string $long_url 需要转换的长链接，支持http://、https://、weixin://wxpay 格式的url
	 * @param string $action long2short,代表长链接转短链接
	 * @return mixed
	 */
	public function convertShortUrl($long_url, $action = 'long2short');

	//---------------------多客服----------

	/**
	 * 获取客服聊天记录接口
	 *
	 * @param array $data POST的数据,用于获取聊天记录
	 * @return mixed
	 */
	public function getCustomerMsgRecord($data);

}