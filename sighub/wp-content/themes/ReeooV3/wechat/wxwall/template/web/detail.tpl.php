<?php defined('IN_IA') or exit('Access Denied');?><?php include template('common/header', TEMPLATE_INCLUDEPATH);?>
<script type="text/javascript" src="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/common.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/common.css?t=<?php echo TIMESTAMP;?>" />
<style>
body{background-image:url('<?php if(!empty($wall['background'])){ ?><?php echo $baseurl.$wall['background']?><?php } else { ?><?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/image/5.jpg<?php }?>'); overflow:hidden;  background-size: cover;}
.wxwall-logo {background: url('<?php echo $baseurl.$wall['logo']?>') no-repeat 0 15px;}
.topbox_l {background: url('<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/image/message_box.png') no-repeat 0 15px;}
.userPic{font-size: initial;}
.talkList{margin: 0px auto 3px auto;padding-top: 2px;padding-bottom: 1px;}

</style>
<div id="wallMain">
	<div id="topbox" class="topbox">
		<div class="wxwall-logo"></div>
		<div class="topbox_l">			
			<div class="topic">
				<h1 class="msg_tit">搜索公众号 <strong class="red"><?php echo $wechat_name;?></strong></h1>
				<span class="addCnt"><?php echo $wall['entry_tips'];?></span>
			</div>
		</div>
	</div>
	<div class="msg_list" id="msg_list_wrap">
		<div id="msg_list" style="position:absolute; left:20px;">

		</div>
	</div>
</div>

<div class="side_div">
	<div class="side_item"><a href="javascript:;" id="remaintime" style="color:red; font-weight:600;">0</a></div>
	<div class="side_item"><a href="javascript:;" onclick="wxwall.prevPage()">上一条</a></div>
	<div class="side_item"><a href="javascript:;" onclick="wxwall.nextPage()">下一条</a></div>
	<div class="side_item"><a href="#" id="status">暂停</a></div>
	<?php if(!empty($wall['qrcode'])){?>
	<div class="side_item"><a href="<?php echo $this->createWebUrl('qrcode', array('id' => $wall['id']))?>">二维码</a></div>
	<?php }?>
	<!--<div class="side_item"><a href="<?php echo $this->createWebUrl('lottery', array('id' => $wall['id']))?>">抽奖</a></div>-->
</div>
<script type="text/javascript">
var messagehistory = <?php echo json_encode($list)?>;
var wxwall = {
	'options' : {
		'index' : -1,
		'pagesize' : 1,
		'delaytime' : 3000,
		'wrapHeight' : 0,
		'pause' : false
	},
	'temp' : '',
	'status' : {'prev' : false, 'next' : true},
	'timer' : {},
	'timerdown' : {},
	'lastmsgtime' : 0,
	'page' : 1,
	'txwall' : {
		'status' : 1,
		'lastmsgtime' : '<?php echo TIMESTAMP;?>',
		'lastuser' : '',
	},
	'fontsize':{
		'ps1':{
			'w10':60,
			'w50':44,
			'w51':36
		},
		'ps8':{
			'w10':30,
			'w50':28,
			'w51':24
		},


	},
	'pagesize':'<?php echo $wall["pagesize"]; ?>',
	'userPicSize':'',
	'init' : function() {
		var $this = this;
		this.options.wrapHeight = $('#msg_list_wrap').height();
		this.prevPage();
		this.control('start');
		$('#remaintime').html(0);
		
		
	},
	'buildItem' : function(message) {/*构造一条微墙内容*/
		if ($('#msg_list #msg_'+message['id'])[0]){
			return '';
		}
		if (this.lastmsgtime <= 0) {
			this.lastmsgtime = message['createtime'];
		}
		if (message['avatar']) {
			if (message['avatar'].indexOf('http') == -1) {
				message['avatar'] = "<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/avatar/"+message['avatar']+'.png';
			}
		} else {
			message['avatar'] = "<?php echo home_url();?>/wp-content/themes/ReeooV3/wechat/wxwall/template/avatar/avatar_"+Math.floor(Math.random()*11+1)+'.jpg';
		}
		var font_size = this.changeSize(message['content']);
		userPicSize = Math.floor(($(window).height() - 220)/this.pagesize-51);
		var min_height = '';
		if(userPicSize>90){
			userPicSize = 90;
			min_height = 'style="min-height: '+(($(window).height() - 220)/this.pagesize-12)+'px;"';
		}
			
		var userName_size;
		if(this.pagesize<8)
			userName_size = 30-2*this.pagesize;
		else
			userName_size = 14;
		var html = '<div class="talkList" id="msg_'+message['id']+'" style="display:none; height:auto;">' +
					'<div class="userPic" '+min_height+'><img src="'+message['avatar']+'" style="width:'+userPicSize+'px;height:'+userPicSize+'px;"><span class="userName" style="font-size: '+userName_size+'px;"><strong>'+message['nickname']+'</strong></span></div>' +
					'<div class="msgBox"><span class="msgCnt" style="font-size:' + font_size + 'px;">' +
					message['content'] + '</span></div></div>';
		return html;
	},
	'appendItem' : function(message) {/*向后插入一条消息*/
		if (!message) {
			return false;
		}
		$('#msg_list').append(this.buildItem(message));
		$('#msg_list div:last-child').css('display', 'block');
	},
	'beforeItem' : function(message) {/*向前插入一条消息*/
		if (!message) {
			return false;
		}
		if ($('#msg_list div:first').size()) {
			$('#msg_list div:first').before(this.buildItem(message));
		} else {
			$('#msg_list').append(this.buildItem(message));
		}
		var target = $('#msg_list div:first');
		if (!this.options.pause) {
			target.show().css('height', $(this).height()).animate({'duration' : 200, 'specialEasing' : {'width' : target.width()}});
		}
	},
	'prevPage' : function() {/*浏览上一页数据*/
		if (this.options.index >= messagehistory.length) {
			return false;
		}
		this.control('pause');
		if (this.status.prev) {
			this.options.index += 2;
			this.status.prev = false;
		} else {
			this.options.index += 1;
		}
		if ($('#msg_list .talkList').size() < this.options.index + 1) {
			for (i = this.options.index; i < this.options.index + this.options.pagesize; i++) {
				try {
					this.appendItem(messagehistory[i]);
				} catch (e) {
				}
			}
		}
		if (this.options.index >= 2){
			var position = $('#msg_list .talkList').eq(this.options.index).position();
			var top = 0;
			if (position) {
				top = $('#msg_list .talkList').eq(this.options.index).position().top + $('#msg_list .talkList').eq(this.options.index).outerHeight();
				if (this.options.wrapHeight - top > 0) {
					top = 0;
				} else {
					top = this.options.wrapHeight - top;
				}
			}
			if (top != 0) {
				$('#msg_list').css({'position' : 'absolute'}).animate({'top' : top});
			}
		}
	},
	'nextPage' : function() {
		if (this.options.index <= 0) {
			return false;
		}
		this.control('pause');
		this.options.index -= 1;
		if (!this.status.prev) {
			if ($('#msg_list .talkList').eq(this.options.index - 1).outerHeight() < this.options.wrapHeight) {
				this.options.index -= 2;
			} else {
				this.options.index -= 1;
			}
			this.status.prev = true;
			this.status.next = false;
		}
		if (this.options.index < 0) {
			this.options.index = 0;
		}
		if (this.options.index > 0) {
			var position = $('#msg_list .talkList').eq(this.options.index).position();
			var top = 0;
			if (position) {
				top = 0 - $('#msg_list .talkList').eq(this.options.index).position().top;
			}
			if (top != 0) {
				$('#msg_list').css({'position' : 'absolute'}).animate({'top' : top});
			}
		} else if (this.options.index == 0) {
			$('#msg_list').css({'position' : 'absolute'}).animate({'top' : 0});
		}
	},
	'newItem' : function() {
		var $this = this;
		if (this.options.pause) {
			return false;
		}
		if ($('#msg_list .talkList:hidden').size() > 0) {
			try {
				var target = $('#msg_list .talkList:hidden:last');
				if (!this.options.pause && target[0]) {
					target.show({'duration' : 200, 'specialEasing' : {'width' : target.width()}});
				}
			} catch (e) {}
			$this.timer = setTimeout(function(){
				$this.newItem();
			}, $this.options.delaytime);
			$this.countdown($this.options.delaytime);
		} else {
			$.getJSON('<?php echo $this->createWebUrl('incoming', array('id' => $wall['id']))?>', {'lastmsgtime' : $this.lastmsgtime, 'page' : $this.page, 'r' : (new Date()).valueOf()}, function(s){
				if (s && s['message']) {
					$this.page++;
				}
				try {
					$this.beforeItem(s['message']);
				} catch (e) {
				}
				$this.timer = setTimeout(function(){
					$this.newItem();
				}, $this.options.delaytime);
				$this.countdown($this.options.delaytime);
			});
			//获取新消息时，请求第三方墙数据
			if ($this.txwall.status){
				$.getJSON('<?php echo $this->createWebUrl('incomingtxwall', array('name' => 'wxwall', 'do' => 'incomingtxwall', 'id' => $wall['id']))?>', {'lastmsgtime' : $this.txwall.lastmsgtime, 'lastuser' : $this.txwall.lastuser, 'r' : (new Date()).valueOf()}, function(s){
					if (s['message']['status'] == '1') {
						$this.txwall.lastmsgtime = s['message']['lastmsgtime'];
						$this.txwall.lastuser = s['message']['lastuser'];
					}
				});
			}
		}
	},
	'control' : function(operation) {
		var $this = this;
		if (operation == 'pause') {
			this.options.pause = true;
			clearTimeout($this.timer);
			$('#status').html('开始');
			$('#status')[0].onclick = function(){
				$this.control('start');
			}
		} else if(operation == 'start') {
			this.options.pause = false;
			$('#status').html('暂停');
			$('#status')[0].onclick = function(){
				$this.control('pause');
			}
			this.options.index = 0;
			$('#msg_list').css({'position' : 'absolute'}).animate({'top' : 0});
			clearTimeout($this.timer);
			$this.newItem();
		}
	},
	'countdown' : function(time) {
		var $this = this;
		if (time) {
			clearTimeout(this.timerdown);
			$('#remaintime').html(time / 1000);
		} else {
			time = parseInt($('#remaintime').html()) - 1;
			if (time < 0){
				time = 0;
			}
			$('#remaintime').html(time);
		}
		this.timerdown = setTimeout(function(){
			$this.countdown();
		}, 1000);
	},
	'removeHTMLTag' : function(str) {
		str = str.replace(/<\/?[^>]*>/g,'');
		str = str.replace(/[ | ]*\n/g,'\n');
		str = str.replace(/\n[\s| | ]*\r/g,'\n');
		str = str.replace(/&nbsp;/ig,'');
		return str;
	},
	'strlen' : function(str) {
		var n = 0;
		str = this.removeHTMLTag(str);
		for(i=0;i<str.length;i++){
			var leg=str.charCodeAt(i);
			/*if(leg>255){
				n+=2;
			}else {
				n+=1;
			}*/
			n+=1;
		}
		return n;
	},
	'changeSize' : function(a) {
		var $this = this;
		var str_len = parseInt($this.strlen(a));
		/*var font_size = 36;
		for (j=18;j>str_len;j--) {
			font_size += 4;
		}
		return font_size;*/
		var fs;
		console.info(this.pagesize);
		if(this.pagesize<8)
			fs = this.fontsize.ps1;
		else
			fs = this.fontsize.ps8;
		if(str_len<=10)
			return fs.w10;

		if(str_len<=50)
			return fs.w50;

		if(str_len>50)
			return fs.w51;
	}
};
$(function(){
	wxwall.init();

	//公众号切换
	var mTimer;
});
</script>
</body>
</html>
