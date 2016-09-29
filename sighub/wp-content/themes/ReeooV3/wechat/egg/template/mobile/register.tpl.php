<?php defined('IN_IA') or exit('Access Denied');?><?php  include $this->template('header', TEMPLATE_INCLUDEPATH);?>
<div data-role="page" data-theme="a">
<div data-role="header" data-position="inline">
	<h1><?php  echo $title;?></h1>
</div>
<style>
.fieldcontain label{vertical-align: middle;display: inline-block;width: 18%;}
.fieldcontain input,.fieldcontain div{width:77%;display: inline-block;}
</style>
	<div class="main">
		 <div data-role="collapsible-set">
			<div data-role="collapsible" data-collapsed="<?php  if($member['nickname']) { ?>true<?php  } else { ?>false<?php  } ?>">
				<h3>砸蛋相关说明</h3>
				<p>尊敬的用户您好，欢迎使用<?php  if(empty($_W['account']['name'])) { ?>由Orange团队开发的<?php  } ?>砸蛋抽奖，中奖用户请务必认真填写您的个人信息，以便我们及时准确的将奖品信息发送给您！谢谢！祝您工作顺利！</p>
			</div>
			<?php  if($member['nickname']) { ?>
			<div data-role="collapsible" data-collapsed="false">
				<h3>您已经登记完成</h3>
				<p style="color:red;">您已经登记完成，以后无需登记可直接砸蛋！如果需要修改信息，请再次提交更改。</p>
			</div>
			<?php  } ?>
		</div>
		<form action="<?php  echo $_W['siteroot'];?><?php  echo $this->createMobileUrl('register')?>" method="post" data-ajax="false" onsubmit="return check(this)">
			<input type="hidden" name="id" value="<?php  echo $_GET['id'];?>" />
			<div class="fieldcontain">
				<label for="name">姓名：</label>
				<input type="text" id="" name="realname" value="<?php echo $member['realname'];?>">
			</div>
			<div class="fieldcontain">
				<label for="phone">手机：</label>
				<input type="text" id="" name="mobile" value="<?php echo $member['mobile'];?>">
			</div>
			<div class="fieldcontain">
				<label for="qq">ＱＱ：</label>
				<input type="text" id="" name="qq" value="<?php echo $member['qq'];?>"></div>
				<div style="margin:20px 0;"><input type="submit" name="submit" data-theme="a" data-icon="check" value="提交登记" />
			</div>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</form>
	</div>
	<script type="text/javascript">
		function check(form) {
			if (!form['realname'].value) {
				alert('请输入您的真实姓名！');
				return false;
			}
			if (!form['mobile'].value) {
				alert('请输入您的手机号码！');
				return false;
			}
			if (!/^1[358]{1}[0-9]{9}/.test(form['mobile'].value)) {
				alert('请输入正确的手机号码！');
				return false;
			}
			if (!form['qq'].value) {
				alert('请输入您的QQ号码！');
				return false;
			}
			return true;
		}
	</script>
<?php  include template('footer', TEMPLATE_INCLUDEPATH);?>