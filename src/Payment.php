<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat;

use Light\Wechat\Interfaces\PaymentInterface;
use Light\Wechat\Helper;
use Light\Wechat\RuntimeException;

class Payment implements PaymentInterface
{

	/**
	 * 公共号身份的唯一标示
	 * @var string
	 */
	protected $app_id;
	/**
	 * 公众号支付请求中用于加密的密钥Key
	 * PaySignKey 对应于支付场景中的appKey值
	 * @var string
	 */
	protected $pay_signkey;

	/**
	 * 公众平台接口API的权限密钥Key
	 * @var string
	 */
	protected $app_secret;

	/**
	 * 财付通商户身份标识
	 * @var string
	 */
	protected $partner_id;

	/**
	 * 财付通商户权限密钥Key
	 * @var string
	 */
	protected $partner_key;

	/**
	 * 调用API接口的token
	 * @var string
	 */
	protected $access_token;

	/**
	 * package生成所需参数
	 * @var array
	 */
	protected $package_params = array();

	/**
	 * 消息参数，用于请求过程返回的状态码和消息
	 */
	public $errcode;
	public $errmsg;

	public function __construct($app_id, $app_secret, $partner_id, $partner_key, $pay_signkey)
	{
		if(empty($app_id) || empty($pay_signkey)
			|| empty($app_secret) || empty($partner_id)
			|| empty($partner_key)) {
			throw new RuntimeException('缺少必要参数');
		}
		$this->app_id = $app_id;
		$this->pay_signkey = $pay_signkey;
		$this->app_secret = $app_secret;
		$this->partner_id = $partner_id;
		$this->partner_key = $partner_key;
	}

	/**
	 * 设置package所需的参数
	 * @param string $key 键值
	 * @param string $value 对应值，必须为字符串
	 * @return null
	 */
	public function setPackageParam($key, $value)
	{
		if(!is_string($value)) {
			$value = (string) $value;
		}
		$this->package_params[$key] = $value;
		return $this;
	}

	/**
	 * 获取现有的package参数值
	 * @param string $key 键值
	 * @return string
	 */
	public function getPackageParam($key)
	{
		return isset($this->package_params[$key]) ? $this->package_params[$key] : null;
	}

	/**
	 * 校验package的参数，保证合法性
	 * @return mixed
	 */
	public function checkPackageParam()
	{
		//检验必填项
		if(empty($this->package_params['bank_type'])
			|| empty($this->package_params['body'])
			|| empty($this->package_params['partner'])
			|| empty($this->package_params['out_trade_no'])
			|| empty($this->package_params['total_fee'])
			|| empty($this->package_params['fee_type'])
			|| empty($this->package_params['notify_url'])
			|| empty($this->package_params['spbill_create_ip'])
			|| empty($this->package_params['input_charset'])) {
			throw new RuntimeException('缺少生成package的必要参数');
		}
		//TODO::检验长度
	}

	/**
	 * 生成订单详情扩展字串
	 *
	 * @return string
	 */
	protected function generatePackageSting($params = array())
	{
		if(!empty($params)) {
			$this->package_params = $params;
		}
		//字典序
		ksort($this->package_params);
		//拼接
		$unSignParamString = Helper::formatQueryParamMap($this->package_params, false);
		//urlencode package string
		$paramString = Helper::formatQueryParamMap($this->package_params, true);
		//获取package sign
		$sign = Helper::md5Sign($this->partner_key, $unSignParamString);
		return $paramString . '&sign=' . $sign;
	}

	/**
	 * 生成支付签名
	 * 参与paySign签名字段：appid, timestamp, noncestr, package, appkey
	 *
	 * @param array $biz_params 参与签名的字段的数组
	 * @param string $sign_method 签名加密方法
	 * @return string 加密后的签名字串
	 */
	public function generatePaySign($biz_params, $sign_method = 'sha1')
	{
		function_exists($sign_method) OR ($sign_method = 'sha1');
		//转化key为小写
		$biz_params = array_change_key_case($biz_params);
		$biz_params['appkey'] = $this->pay_signkey;
		ksort($biz_params);
		$biz_string = Helper::formatBizQueryParamMap($biz_params);
		return $sign_method($biz_string);
	}

	/**
	 * 生成共享收货地址空间所需的签名
	 * 参与字段：appId,url, timestamp,noncestr, accessToken
	 *
	 * @param array  $biz_params 参与签名的参数
	 * @param string $sign_method 签名加密方法
	 * @return string 返回签名后的字符串
	 */
	public function generateAddrSign($biz_params, $sign_method = 'sha1')
	{
		function_exists($sign_method) OR ($sign_method = 'sha1');
		//参数名小写
		$biz_params = array_change_key_case($biz_params);
		ksort($biz_params); //字典序
		//字段名和参数值都采用原始值
		$biz_string = Helper::formatBizQueryParamMap($biz_params);
		return $sign_method($biz_string);
	}

	/**
	 * 生成jsapi支付请求json
	 * "appId" : "wxf8b4f85f3a794e77", //公众号名称，由商户传入
	 * "timeStamp" : "189026618", //时间戳这里随意使用了一个值
	 * "nonceStr" : "adssdasssd13d", //随机串
	 * "package" : "bank_type=WX&body=XXX&fee_type=1&input_charset=GBK&notify_url=http%3a%2f
	 * %2fwww.qq.com&out_trade_no=16642817866003386000&partner=1900000109&spbill_create_i
	 * p=127.0.0.1&total_fee=1&sign=BEEF37AD19575D92E191C1E4B1474CA9",
	 * //扩展字段，由商户传入
	 * "signType" : "SHA1", //微信签名方式:sha1
	 * "paySign" : "7717231c335a05165b1874658306fa431fe9a0de" //微信签名
	 * @return string
	 */
	public function createBizPackage()
	{
		//检查参数的合法性
		$this->checkPackageParam();
	    $native_obj["appId"] = $this->app_id;
	    $native_obj["package"] = $this->generatePackageSting();
	    $native_obj["timeStamp"] = (string)time();
	    $native_obj["nonceStr"] = Helper::getNonceStr();
	    $native_obj["paySign"] = $this->generatePaySign($native_obj);
	    $native_obj["signType"] = self::SIGN_TYPE;

	    return json_encode($native_obj);
	}

	/**
	 * 生成原生支付url
	 * weixin://wxpay/bizpayurl?sign=XXXXX&appid=XXXXXX&productid=XXXXXX&timestamp=XXXXXX&noncestr=XXXXXX
	 * @param string $product_id 产品编码
	 * @return string
	 */
	public function createNativePayUrl($product_id)
	{
	    $native_obj["appid"] = $this->app_id;
	    $native_obj["productid"] = urlencode($product_id);
	    $native_obj["timestamp"] = (string)time();
	    $native_obj["noncestr"] = Helper::getNonceStr();
	    $native_obj["sign"] = $this->generatePaySign($native_obj);
	    $biz_string = Helper::formatBizQueryParamMap($native_obj);
	    return self::NATIVE_PAY_URL_PREFIX . $biz_string;
	}

	/**
	 * 生成原生支付请求xml
	 * <xml>
	 * <AppId><![CDATA[wwwwb4f85f3a797777]]></AppId>
	 * <Package><![CDATA[a=1&url=http%3A%2F%2Fwww.qq.com]]></Package>
	 * <TimeStamp> 1369745073</TimeStamp>
	 * <NonceStr><![CDATA[iuytxA0cH6PyTAVISB28]]></NonceStr>
	 * <RetCode>0</RetCode>
	 * <RetErrMsg><![CDATA[ok]]></ RetErrMsg>
	 * <AppSignature><![CDATA[53cca9d47b883bd4a5c85a9300df3da0cb48565c]]>
	 * </AppSignature>
	 * <SignMethod><![CDATA[sha1]]></ SignMethod >
	 * </xml>
	 * @param int $ret_code 错误码
	 * @param string $ret_errmsg 返回信息
	 * @return string
	 */
	public function createNativePackage($ret_code = 0, $ret_errmsg = 'ok')
	{
		//校验参数合法性
		$this->checkPackageParam();
	    $native_obj["AppId"] = $this->app_id;
	    $native_obj["Package"] = $this->generatePackageSting();
	    $native_obj["TimeStamp"] = (string)time();
	    $native_obj["NonceStr"] = Helper::getNonceStr();
	    $native_obj["RetCode"] = $ret_code;
	    $native_obj["RetErrMsg"] = $ret_errmsg;
	    $native_obj["AppSignature"] = $this->generatePaySign($native_obj);
	    $native_obj["SignMethod"] = self::SIGN_TYPE;
	    return Helper::arrayToXml($native_obj);
	}

	/**
	 * 生成app支付请求json
     * {
	 * "appid":"wwwwb4f85f3a797777",
	 * "traceid":"crestxu",
	 * "noncestr":"111112222233333",
	 * "package":"bank_type=WX&body=XXX&fee_type=1&input_charset=GBK&notify_url=http%3a%2f%2f
	 * 	www.qq.com&out_trade_no=16642817866003386000&partner=1900000109&spbill_create_ip=127.0.0.1&total_fee=1&sign=BEEF37AD19575D92E191C1E4B1474CA9",
	 * "timestamp":1381405298,
	 * "app_signature":"53cca9d47b883bd4a5c85a9300df3da0cb48565c",
	 * "sign_method":"sha1"
	 * }
	 * @param string $trace_id 跟踪号
	 * @return string
	 */
	public function createAppPackage($trace_id = '')
	{
		//校验参数合法性
		$this->checkPackageParam();
	    $native_obj["appid"] = $this->app_id;
	    $native_obj["package"] = $this->generatePackageSting();
	    $native_obj["timestamp"] = (string)time();
	    $native_obj["traceid"] = $trace_id;
	    $native_obj["noncestr"] = Helper::getNonceStr();
	    $native_obj["app_signature"] = $this->generatePaySign($native_obj);
	    $native_obj["sign_method"] = self::SIGN_TYPE;
	    return json_encode($native_obj);
	}

	/**
	 * 获取使用收货地址共享控件package
	 * @param string $url 当前页面地址,'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
	 * @param string $access_token 用户授权凭证
	 * @return string
	 */
	public function createAddrPackage($url, $access_token)
	{
		//需要参与签名的参数
		$biz_params['url'] = $url;
		$biz_params['accessToken'] = $access_token;
		$biz_params['appId'] = $this->app_id;
		$biz_params['timeStamp'] = (string)time();
		$biz_params['nonceStr'] = Helper::getNonceStr();
		$biz_params['addrSign'] = $this->generateAddrSign($biz_params);
		//不需要参与签名
		$biz_params['signType'] = self::SIGN_TYPE;
		$biz_params['scope'] = 'jsapi_address'; //默认

		//剔除access_token和url
		unset($biz_params['url'], $biz_params['accessToken']);
		return json_encode($biz_params);
	}

	/**
	 * 校验原生支付回调获取商户支付package签名
	 * 参与字段appid,appkey,productid,timestamp,noncestr,openid
	 *
	 * @param string $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkGetNativePayPackageSign($posted_data)
	{
		if(!$posted_data) return false;
		//获取参与签名验证的参数
		if(!isset($posted_data['AppId'])
			|| !isset($posted_data['ProductId'])
			|| !isset($posted_data['TimeStamp'])
			|| !isset($posted_data['NonceStr'])
			|| !isset($posted_data['OpenId'])
			|| !isset($posted_data['IsSubscribe'])
			|| !isset($posted_data['AppSignature'])) {
			return false;
		}
		$biz_params['appid'] = $posted_data['AppId'];
		$biz_params['productid'] = $posted_data['ProductId'];
		$biz_params['timestamp'] = $posted_data['TimeStamp'];
		$biz_params['noncestr'] = $posted_data['NonceStr'];
		$biz_params['openid'] = $posted_data['OpenId'];
		$biz_params['issubscribe'] = $posted_data['IsSubscribe'];
		$sign = $this->generatePaySign($biz_params);

		return $posted_data['AppSignature'] == $sign;
	}

	/**
	 * 校验支付成功异步通知的sign
	 * 参与字段：appid, appkey, timestamp, noncestr, issubscribe, openid
	 *
	 * @param array $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkPayConfirmSignature($posted_data)
	{
		if(!isset($posted_data['AppId'])
			|| !isset($posted_data['TimeStamp'])
			|| !isset($posted_data['NonceStr'])
			|| !isset($posted_data['OpenId'])
			|| !isset($posted_data['IsSubscribe'])
			|| !isset($posted_data['AppSignature'])) {
			return false;
		}
		$biz_params['appid'] = $posted_data['AppId'];
		$biz_params['timestamp'] = $posted_data['TimeStamp'];
		$biz_params['noncestr'] = $posted_data['NonceStr'];
		$biz_params['openid'] = $posted_data['OpenId'];
		$biz_params['issubscribe'] = $posted_data['IsSubscribe'];
		$sign = $this->generatePaySign($biz_params);

		return $posted_data['AppSignature'] == $sign;
	}

	/**
	 * 校验告警通知signature
	 * 签名字段: alarmcontent, appid, appkey, description, errortype, timestamp
	 *
	 * @param array $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkAlarmSignature($posted_data)
	{
		if(!isset($posted_data['AppId'])
			|| !isset($posted_data['TimeStamp'])
			|| !isset($posted_data['ErrorType'])
			|| !isset($posted_data['Description'])
			|| !isset($posted_data['AlarmContent'])
			|| !isset($posted_data['AppSignature'])) {
			return false;
		}
		$biz_params['appid'] = $posted_data['AppId'];
		$biz_params['timestamp'] = $posted_data['TimeStamp'];
		$biz_params['alarmcontent'] = $posted_data['AlarmContent'];
		$biz_params['errortype'] = $posted_data['ErrorType'];
		$biz_params['description'] = $posted_data['Description'];
		$sign = $this->generatePaySign($biz_params);

		return $posted_data['AppSignature'] == $sign;
	}

	/**
	 * 校验投诉signature(包括新增投诉和用户确认处理完毕投诉)
	 * 参与校验参数：appid,appkey,timestamp, openid
	 *
	 * @param array $posted_data 微信服务器POST过来的数据
	 * @return Boolean true代表校验通过 false校验失败
	 */
	public function checkFeedbackSignature($posted_data)
	{
		if(!isset($posted_data['AppId'])
			|| !isset($posted_data['TimeStamp'])
			|| !isset($posted_data['OpenId'])
			|| !isset($posted_data['AppSignature'])) {
			return false;
		}
		$biz_params['appid'] = $posted_data['AppId'];
		$biz_params['timestamp'] = $posted_data['TimeStamp'];
		$biz_params['openid'] = $posted_data['OpenId'];
		$sign = $this->generatePaySign($biz_params);

		return $posted_data['AppSignature'] == $sign;
	}

	//------------------------------------------

	/**
	 * 设置调用API接口所需要的access_token
	 *
	 * @param string $access_token 通过调用API获取的token值
	 * @return self
	 */
	public function setAccessToken($access_token)
	{
		$this->access_token = $access_token;
		return $this;
	}

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
	public function deliveryNotify($openid, $transid, $out_trade_no, $deliver_status = 1, $deliver_msg = 'ok')
	{
		if(!$this->access_token)
			throw new RuntimeException('请先获取access token');
		//业务数据
		$biz_params['appid'] = $this->app_id;
		$biz_params['openid'] =  $openid;
		$biz_params['transid'] = $transid;
		$biz_params['out_trade_no'] = $out_trade_no;
		$biz_params['deliver_timestamp'] = (string)time();
		$biz_params['deliver_status'] = $deliver_status;
		$biz_params['deliver_msg'] = $deliver_msg;
		$biz_params['app_signature'] = $this->generatePaySign($biz_params);
		$biz_params['sign_method'] = self::SIGN_TYPE;

		//发送请求
		$result = Helper::http_post(self::API_URL_PREFIX . self::DELIVERY_URL_SUFFIX . 'access_token=' . $this->access_token,
									Helper::json_encode($biz_params));
		if($result) {
			//{"errcode":0,"errmsg":"ok"}
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;
			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			return true;
		}
		return false;
	}

	/**
	 * 订单查询方法,查询订单详细支付状态
	 *
	 * @param string $out_trade_no 商户订单编号
	 * @return mixed
	 */
	public function orderQuery($out_trade_no)
	{
		if(!$this->access_token)
			throw new RuntimeException('请先获取access token');
		//package数据
		$package_params['out_trade_no'] = $out_trade_no;
		$package_params['partner'] = $this->partner_id;
		//业务数据
		$biz_params['appid'] = $this->app_id;
		$biz_params['package'] = $this->generatePackageSting($package_params);
		$biz_params['timestamp'] = (string)time();
		$biz_params['app_signature'] = $this->generatePaySign($biz_params);
		$biz_params['sign_method'] = self::SIGN_TYPE;

		//发送请求
		$result = Helper::http_post(self::API_URL_PREFIX . self::ORDERQUERY_URL_SUFFIX . 'access_token=' . $this->access_token,
									Helper::json_encode($biz_params));
		if($result) {
			$result = json_decode($result, true);
			if(!$result || empty($result))
				return false;

			$this->errcode = $result['errcode'];
			$this->errmsg = $result['errmsg'];
			return $result['order_info'];
		}
		return false;
	}

	/**
	 * 客户投诉处理状态
	 *
	 * @param string $openid 用户openid
	 * @param int $feedbackid 投诉单号
	 * @return mixed
	 */
	public function feedbackUpdate($openid, $feedbackid)
	{
		if(!$this->access_token)
			throw new RuntimeException('请先获取access token');
		$result = Helper::http_get(FEEDBACK_UPDATE_URL . 'access_token=' . $this->access_token . '&openid=' . $openid . '&feedbackid' . $feedbackid);
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