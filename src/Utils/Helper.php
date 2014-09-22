<?php
// +----------------------------------------------------------------------
// | Writen By lichunqiang
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Light <light-li@hotmail.com>
// +----------------------------------------------------------------------
namespace Light\Wechat\Utils;

class Helper
{
	/**
	 * 生成随机字串
	 * @param int $len 默认为16最长为32
	 * @return string
	 */
	public static function getNonceStr($len = 16)
	{
		if(!is_numeric($len) || $len > 32) {
			$len = 16;
		}
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $len; $i++)
		{
			$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return $str;
	}

	/**
	 * 微信api不支持中文转义的json结构
	 * @param array $arr
	 */
	static function json_encode($arr) {
		$parts = array ();
		$is_list = false;
		//Find out if the given array is a numerical array
		$keys = array_keys ( $arr );
		$max_length = count ( $arr ) - 1;
		if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
			$is_list = true;
			for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
				if ($i != $keys [$i]) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}
		foreach ( $arr as $key => $value ) {
			if (is_array ( $value )) { //Custom handling for arrays
				if ($is_list)
					$parts [] = self::json_encode ( $value ); /* :RECURSION: */
				else
					$parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
			} else {
				$str = '';
				if (! $is_list)
					$str = '"' . $key . '":';
				//Custom handling for multiple data types
				if (is_numeric ( $value ) && $value<2000000000)
					$str .= $value; //Numbers
				elseif ($value === false)
				$str .= 'false'; //The booleans
				elseif ($value === true)
				$str .= 'true';
				else
					$str .= '"' . addslashes ( $value ) . '"'; //All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)
				$parts [] = $str;
			}
		}
		$json = implode ( ',', $parts );
		if ($is_list)
			return '[' . $json . ']'; //Return numerical JSON
		return '{' . $json . '}'; //Return associative JSON
	}

	/**
	 * GET 请求
	 * @param string $url
	 */
	static function http_get($url){
		$oCurl = \curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @param boolean is upload file action
	 * @return string content
	 */
	static function http_post($url, $param, $is_upload = FALSE){
		$oCurl = \curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		}
		if (is_string($param) || $is_upload) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * 将数组信息转化为xml
	 * @param array  $arr 提交的数组信息
	 * @return string 转化后的xml
	 */
	public static function arrayToXml($arr)
    {
    	if(!is_array($arr)) return '';

        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
        	 if (is_numeric($val)) {
        	 	$xml.="<".$key.">".$val."</".$key.">";
        	 } else {
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        	 }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 将xml转化为数组
     * @param string $xml 输入的xml信息
     * @return array
     */
    public static function xmlToArray($xml)
    {
    	$arr = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    	return json_decode(json_encode($arr), true);
    }

    /**
     * 根据数值生成URL键值对格式的字符串
     * 微信规定urlencode编码需要将空格转化为%20而不是+,所以这里使用rawurlencode
     *
     * @param array  $param 参数
     * @param bool $urlencode 是否对值进行url编码
     * @return string
     */
    public static function formatQueryParamMap($param, $urlencode = false)
    {
    	$buff = array();
    	foreach ($param as $key => $value) {
    		if(null !== $value && 'null' != $value
    			&& '' != $value && 'sign' != $key) {
    			$buff[] = $key . '=' . ($urlencode ? rawurlencode($value) : $value);
    		}
    	}
    	return implode('&', $buff);
    }

    /**
     * 根据数值生成URL键值对格式的字符串(用户支付,带上了sign值)
     * @param array  $param 参数
     * @param bool $urlencode 是否对值进行url编码
     * @return string
     */
    public static function formatBizQueryParamMap($param, $urlencode = false)
    {
    	$buff = array();
    	foreach ($param as $key => $value) {
    		$buff[] = $key . '=' . ($urlencode ? rawurlencode($value) : $value);
    	}
    	return implode('&', $buff);
    }

    /**
     * 生成签名
     * @param string $partner_key 财付通加密字串
     * @param string $body 需要加密的字串
     * @return string
     */
    public static function md5Sign($partner_key, $body)
    {
    	return strtoupper(md5($body . '&key=' . $partner_key));
    }
}
