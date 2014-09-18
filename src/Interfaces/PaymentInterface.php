<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Interfaces;

interface PaymentInterface
{
	//常量

	/**
	 * Native支付链接前缀
	 */
	const NATIVE_PAY_URL_PREFIX = 'weixin://wxpay/bizpayurl?';

	/**
	 * 支付相关API接口URL前缀
	 */
	const API_URL_PREFIX = 'https://api.weixin.qq.com/pay';

	/**
	 * 发货API
	 */
	const DELIVERY_URL_SUFFIX = '/delivernotify?';

	/**
	 * 订单查询
	 */
	const ORDERQUERY_URL_SUFFIX = '/orderquery?';

	/**
	 * 标记客户投诉处理状态地址
	 */
	const FEEDBACK_UPDATE_URL = 'https://api.weixin.qq.com/payfeedback/update?';

	/**
	 * 加密方法,目前仅支持SHA1
	 */
	const SIGN_TYPE = 'sha1';

	/**
	 * 设置package所需的参数
	 * @param string $key 键值
	 * @param string $value 对应值，必须为字符串
	 * @return null
	 */
	public function setPackageParam($key, $value);

	/**
	 * 获取现有的package参数值
	 * @param string $key 键值
	 * @return string
	 */
	public function getPackageParam($key);

	/**
	 * 生成支付使用的签名
	 * @param array  $biz_params 业务参数
	 * @return string
	 */
	public function generatePaySign($biz_params, $sign_method = 'sha1');

	/**
	 * 生成收货地址共享空间的签名
	 * @param array  $biz_params 生成签名所需的业务参数
	 * @return string
	 */
	public function generateAddrSign($biz_params, $sign_method = 'sha1');

	/**
	 * 生成jsapi支付请求json
	 * @return string
	 */
	public function createBizPackage();

	/**
	 * 生成原生支付url
	 * @param string $product_id 产品编码
	 * @return string
	 */
	public function createNativePayUrl($product_id);

	/**
	 * 生成原生支付请求xml,用户Native支付微信请求商户服务器获取package
	 * @param int $ret_code 错误码
	 * @param string $ret_errmsg 返回信息
	 * @return string
	 */
	public function createNativePackage($ret_code = 0, $ret_errmsg = 'ok');

	/**
	 * 生成app支付请求json
	 * @param string $trace_id 跟踪号
	 * @return string
	 */
	public function createAppPackage($trace_id = '');

	/**
	 * 生成使用微信共享收货地址空间的package
	 * @param string $url 当前页面地址
	 * @param string $access_token 用户授权凭证
	 * @return string
	 */
	public function createAddrPackage($url, $access_token);

	/**
	 * 校验原生支付回调获取商户支付package签名
	 *
	 * @param array $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkGetNativePayPackageSign($posted_data);

	/**
	 * 校验支付成功异步通知的sign
	 *
	 * @param array $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkPayConfirmSignature($posted_data);

	/**
	 * 校验告警通知signature
	 *
	 * @param array $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkAlarmSignature($posted_data);

	/**
	 * 校验投诉signature
	 *
	 * @param array $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkFeedbackSignature($posted_data);

	//----------发货等API接口--------------

	/**
	 * 发货通知
	 * 第三方在收到最终支付通知后，调用发货通知API告知微信后台订单的发货状态
	 *
	 * @param string $openid 用户的openid
	 * @param string $transid 微信的交易单号
	 * @param string $out_trade_no 商户的订单编号
	 * @param int $deliver_status  发货状态 1-成功 0-失败 默认1
	 * @param string $deliver_msg 发货状态信息
	 * @return mixed
	 */
	public function deliveryNotify($openid, $transid, $out_trade_no, $deliver_status = 1, $deliver_msg = 'ok');

	/**
	 * 订单查询方法,查询订单详细支付状态
	 *
	 * @param string $out_trade_no 商户订单编号
	 * @return mixed
	 */
	public function orderQuery($out_trade_no);

	//--------------------------------------------------------

	/**
	 * 客户投诉处理状态
	 *
	 * @param string $openid 用户openid
	 * @param int $feedbackid 投诉单号
	 * @return mixed
	 */
	public function feedbackUpdate($openid, $feedbackid);

}