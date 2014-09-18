
### JSAPI 网页内支付接口

1. 用户在网页内发起支付，通过Javascript调用getBrandWCPayRequest接口发起支付.

2. 用户点击支付完成后，商户的前段会收到Javascript返回值，可直接跳转到支付成功的静态页面。

3. 商户后台接收到来自微信开放平台的支付成功回调通知。

> 低版本的微信客户端无法使用微信支付功能，5.0以上版本才支持。

#### 显示微信安全支付标题

在原始的链接添加上 "showwxpaytitle=1"的尾串。

#### 参数列表

参数 | 名称 | 必填 | 格式 | 说明
-----|------|------|------|--------
appId| 公众号Id | 是 | 字符串类型 |商户注册具有支付权限的公众号成功可获得
timeStamp | 时间戳 | 是 | 字符串类型 | 商户生成
nonceStr | 随机字串 | 是 | 字符串，32个字节以下 | 商户生成的随机字串
package | 订单详情扩展字串 | 是 | 字符串，4096字节以下 | 订单信息组成
signType | 签名方式 | 是 | 字符串，取值"SHA1" | 目前仅支持SHA1
paySign | 签名 | 是 | 字符串 | 接口列表中的参数按指定的方式进行签名

返回结果：

返回值 | 说明
-------|------
err_msg| get_brand_wcpay_request:ok 支付成功
err_msg| get_brand_wcpay_request:cancel 支付过程用户取消
err_msg| get_brand_wcpay_request:fail 支付失败


#### 订单详情扩展字符串定义

package所需字段列表

参数 | 名称 | 必填 | 格式 | 说明
-----|------|------|------|--------
bank_type | 银行通道类型 | 是 | 字符串类型，固定大写"WX" | 固定为"WX"
body | 商品描述 | 是 | 128字节以下 | 商品描述
attach | 附加数据 | 否 | 128字节以下 | 附加数据，原样返回
partner | 商户号 | 是 | 字符串 | 财付通商户号
out_trade_no | 商户订单号 | 是 | 字符串,32字节下 | 32字节内，可包含字母
total_fee | 订单总金额 | 是 | 字符串 | 订单总金额，单位为分
fee_type | 支付币种 | 是 | 字符串 | 取值1 (人民币),暂只支持1
notify_url | 通知URL | 是 | 字符串255字节以下 | 接口微信通知支付结果的URL，需要绝对路径
spbill_create_ip | 订单生成的机器IP | 是 | 字符串15字节以下 | 用户浏览器端IP，格式为IPV4
time_start | 交易起始时间 | 否 | 字符串，14字节以下 | 订单生成时间，格式为yyyyMMddHHmmss
time_expire | 介意结束时间 | 否 | 字符串，14字节以下 | 订单失效时间
transport_fee | 物流费用 | 否 | 字符串 | 物流费用，单位为分
product_fee | 商品费用 | 否 | 字符串 | 商品费用，单位为分.
goods_tag | 商品标记 | 否 | 字符串 | 商品标记，优惠券可能用到
input_charset | 传入参数字符编码| 是 | 字符串 | 取值范围："GBK", "UTF-8", 默认"GBK"

> 请确保 transport_fee + product_fee = total_fee;


### 订单查询返回数据

参数 | 说明
-----|------
ret_code | 是查询结果状态码， 0 表明成功，其他表明错误；
ret_msg | 是查询结果出错信息；
input_charset | 是返回信息中的编码方式；
trade_state | 是订单状态， 0 为成功，其他为失败；
trade_mode | 是交易模式， 1 为即时到帐，其他保留；
partner | 是财付通商户号，即前文的 partnerid ；
bank_type | 是银行类型；
bank_billno | 是银行订单号；
total_fee | 是总金额，单位为分；
fee_type | 是币种， 1 为人民币；
transaction_id | 是财付通订单号；
out_trade_no | 是第三方订单号；
is_split | 表明是否分账， false 为无分账， true 为有分账；
is_refund | 表明是否退款， false 为无退款， ture 为退款；
attach | 是商户数据包，即生成订单 package 时商户填入的 attach ；
time_end | 是支付完成时间；
transport_fee|  是物流费用，单位为分；
product_fee|  是物品费用，单位为分；
discount | 是折扣价格，单位为分；
rmb_total_fee | 是换算成人民币之后的总金额，单位为分，一般看 total_fee 即可。