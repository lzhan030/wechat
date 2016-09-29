<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  // 注意：所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。 
  // 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
  // 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
  wx.config({
    appId: '<?php echo $signPackage["appId"]; ?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage'
    ]
  });
  wx.ready(function () {
      wx.onMenuShareAppMessage({
        title: "<?php echo $arr['title']; ?>",
        desc: "<?php echo $arr['desc']; ?>",
        link: "<?php echo $arr['link']; ?>",
        imgUrl: "<?php echo $arr['imgUrl']; ?>",
        trigger: function (res) {
        },
        success: function (res) {
        },
        cancel: function (res) {
        },
        fail: function (res) {
          alert(JSON.stringify(res));
        }
      });

    // 2.2 监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
      wx.onMenuShareTimeline({
        title: "<?php echo $arr['title']; ?>",
        link: "<?php echo $arr['link']; ?>",
        imgUrl: "<?php echo $arr['imgUrl']; ?>",
        trigger: function (res) {
        },
        success: function (res) {
        },
        cancel: function (res) {
        },
        fail: function (res) {
          alert(JSON.stringify(res));
        }
      });
  });
</script>