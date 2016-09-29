function placeOrder(gweid,openId,title,price){
	jQuery.post("http://2.wpcloudforsina.sinaapp.com/mobile.php?module=wepay&do=placeNewOrder&gweid="+gweid,
						{gweid:gweid,openid:openId,title:title,price:price},
						function(data, textStatus, jqXHR){
							WeixinJSBridge.invoke('getBrandWCPayRequest',
								{
									"appId" : data.appId,
									"timeStamp" : data.timeStamp,
									"nonceStr" : data.nonceStr,
									"package" : data.package,
									"sign" : data.sign
								},
								function(res){
									if(res.err_msg=="get_brand_wcpay_request:ok"){}
								}
							);
						},
						"json"
					); 
}