微信推送到服务器端事件为Event类型的XML结构,MsgType都为event
-************
* 关注/取消关注事件
```
<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[FromUser]]></FromUserName>
	<CreateTime>123456789</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[subscribe]]></Event>
</xml>
```
> Event事件类型：subscribe(订阅)  unsubscribe(取消订阅)

* 扫描带参数二维码事件

1. 用户未关注时，进行关注后的事件推送
```
<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[FromUser]]></FromUserName>
	<CreateTime>123456789</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[subscribe]]></Event>
	<EventKey><![CDATA[qrscene_123123]]></EventKey>
	<Ticket><![CDATA[TICKET]]></Ticket>
</xml>
```

> Event事件类型, subscribe		
> EventKey, 事件KEY值，qrscene_为前缀，后面为二维码的参数值			
> Ticket, 二维码的ticket，可用来换取二维码图片			

2. 用户已关注时的事件推送
```
<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[FromUser]]></FromUserName>
	<CreateTime>123456789</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[SCAN]]></Event>
	<EventKey><![CDATA[SCENE_VALUE]]></EventKey>
	<Ticket><![CDATA[TICKET]]></Ticket>
</xml>
```

> Event事件类型, SCAN		
> EventKey, 事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id		
> Ticket, 二维码的ticket，可用来换取二维码图片		

* 上报地理位置

```
<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[fromUser]]></FromUserName>
	<CreateTime>123456789</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[LOCATION]]></Event>
	<Latitude>23.137466</Latitude>
	<Longitude>113.352425</Longitude>
	<Precision>119.385040</Precision>
</xml>
````

> Event事件类型, LOCATION		
> Latitude	 地理位置纬度		
> Longitude	 地理位置经度		
> Precision	 地理位置精度		

* 自定义菜单事件

1. 点击菜单拉取消息时的事件推送
```
<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[FromUser]]></FromUserName>
	<CreateTime>123456789</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[CLICK]]></Event>
	<EventKey><![CDATA[EVENTKEY]]></EventKey>
</xml>
```

> Event事件类型, CLICK		
> EventKey 事件KEY值, 与自定义菜单接口中KEY值对应		

2.
```
<xml>
	<ToUserName><![CDATA[toUser]]></ToUserName>
	<FromUserName><![CDATA[FromUser]]></FromUserName>
	<CreateTime>123456789</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[VIEW]]></Event>
	<EventKey><![CDATA[www.qq.com]]></EventKey>
</xml>
```
> Event事件类型, VIEW		
> EventKey 事件KEY值, 设置的跳转URL		

* 高级接口群发结果事件推送(群发任务结束之后)

```
<xml>
	<ToUserName><![CDATA[gh_3e8adccde292]]></ToUserName>
	<FromUserName><![CDATA[oR5Gjjl_eiZoUpGozMo7dbBJ362A]]></FromUserName>
	<CreateTime>1394524295</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[MASSSENDJOBFINISH]]></Event>
	<MsgID>1988</MsgID>
	<Status><![CDATA[sendsuccess]]></Status>
	<TotalCount>100</TotalCount>
	<FilterCount>80</FilterCount>
	<SentCount>75</SentCount>
	<ErrorCount>5</ErrorCount>
</xml>
```

> Event	 事件信息，此处为MASSSENDJOBFINISH		
> MsgID	 群发的消息ID		
> Status	 群发的结构，为“send success”或“send fail”或“err(num)”。但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。err(num)是审核失败的具体原因，可能的情况如下：		
> err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会 err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈 err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他		
> TotalCount	 group_id下粉丝数；或者openid_list中的粉丝数		
> FilterCount	 过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount		
> SentCount	 发送成功的粉丝数		
> ErrorCount	 发送失败的粉丝数		
