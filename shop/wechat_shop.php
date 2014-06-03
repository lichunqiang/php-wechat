<?php
/**
 *	微信公众平台微信小店PHP-SDK, 官方API部分
 *  @author  lichunqiang <light-li@hotmail.com>
 *  @link https://github.com/lichunqiang/php-wechat
 *  @version 1.0
 */
class Shop
{
	const API_URL_PREFIX = 'https://api.weixin.qq.com/merchant';
	const PRODUCT_CREATE_URL = '/create?';
	
	const PRODUCT_GET_SUB_CATE ='/category/getsub?';
	
	private $access_token;
	public $errCode = 40001;
	public $errMsg = "no access!";
	
	public function __construct($access_token)
	{
		$this->access_token = $access_token;
	}
	/**
	 * 设置access token
	 * 接口调用的票据
	 * @param String $access_token
	 */
	public function setAccessToken($access_token) {
		$this->access_token = $access_token;
		return $this;
	}
	
	/**
	 * 获取指定分类的子分类
	 * @param Integer $cate_id 大分类ID(根节点分类id为1)
	 * @return boolean||array
	 *   eg: {"errcode":0,"errmsg":"success", "cate_list": [{"id": "12312341", "name": "数码相机"}, {"id": "7324342", "name": "单方相机"}]}
	 */
	public function getSubCategory($cate_id){
		if (!$this->access_token) return false;
		$data = array('cate_id' => $cate_id);
		
		$result = $this->http_post(self::API_URL_PREFIX . self::PRODUCT_GET_SUB_CATE . 'access_token=' . $this->access_token, $data);
		if ($result) {
			$json = json_decode($result, true);
			if (isset($json['errcode']) && $json['errcode'] != 0) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json['cate_list'];
		}
		return false;
	}	
	
	/**
	 * GET 请求
	 * @param string $url
	 */
	private function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 3);
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
	private function http_post($url, $param, $is_upload = FALSE){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 3);
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
}