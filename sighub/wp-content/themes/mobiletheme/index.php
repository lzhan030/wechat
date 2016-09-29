<?php
session_start();
require_once 'wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';

/**
*@function: get
*/
global $gweid,$wpdb;

$gweid =  $_GET['gweid'];
$gweidtrue =  $_GET['gweid'];
$siteId = $_GET['site'];
$_SESSION['orangeSite']=$siteId;
$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];

/**
*@function:封装gweid
*/
if(!empty($siteId)){
	$site=web_admin_get_site($siteId);
	foreach($site as $siteinfo){
		$userid=$siteinfo->site_user;
		$gweid=$siteinfo->GWEID;
		$gweidtrue=$siteinfo->GWEID;
	}

	//20150417 sara new added
	//根据当前的gweid去查找有没有处在共享虚拟号下，如果是虚拟号下的，需要将gweid换为虚拟号的gweid
	$gweid = virtualgweid_open($gweid);

	$mid =$_SESSION['gmid'][intval($gweid)]['mid'];
	$auth =	$_SESSION['gmid'][intval($gweid)]['auth'];	
}


/**
*@function:判断会员是否审核
*/
$vipauditinfo=web_admin_usechat_info_group($gweid);
foreach($vipauditinfo as $vaudit){
	$vipaudit=$vaudit->wechat_vipaudit;
}

/*获取fromuser*/
$fromuser=$_SESSION['gopenid'][intval($gweid)];
$weid =  $_SESSION['weid'][intval($gweid)];


if(1)
 {		
	if(empty($siteId)){
		$siteId = $_SESSION['orangeSite'];//$siteId  = $_COOKIE['orangeSite'];	
	}
	 $isShowPic = getSiteMeta('mobilethemeIsShowPic', $siteId);
	 $isShowEditor = getSiteMeta('mobilethemeIsShowEditor', $siteId);
	 $isPostPermission = getSiteMeta('mobilethemeIsPostPermission', $siteId);
	 $isShowVipmember = getSiteMeta('mobilethemeIsShowVipmember', $siteId);
	 $isShowVipmember_editor = getSiteMeta('mobilethemeIsShowVipmemberEditor', $siteId);
	 $useContact = getSiteMeta('mobilethemeContact', $siteId);	
	
	
	//判断会员权限
	$result = web_user_display_index_groupnew_wesforsel($gweid);
	foreach($result as $initfunc){
		if($selCheck[$initfunc->func_name] == 0)
			$selCheck[$initfunc->func_name] = $initfunc->status;
	}
	
	//var_dump(expression);
	if((isset($_SERVER['HTTP_USER_AGENT']))&&(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)&&($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')||($isShowVipmember_editor == 'true'))){
		/*认证服务号则通过oauth2.0获取fromuser*/
		require_once 'wp-content/themes/ReeooV3/wesite/common/common_oauth.php';
	}
	
	
	// if(($isShowVipmember == 'true')||($isShowVipmember_editor == 'true')){
	// 	require_once 'wp-content/themes/ReeooV3/wesite/common/common_oauth_test.php';
		
	// }
	
	
	/*获取会员基本信息*/
	/**
	*@function:通过fromuser拿到会员信息
	*/
	$memberinfo=null;
	$memberinfo_wgroup=null;
	if((!empty($fromuser))&&(!empty($weid))&&(!empty($gweid))){
				$memberinfo_wgroup =  web_admin_member_wgroup($weid,$gweid,$fromuser);		
	}			
	if(!empty($memberinfo_wgroup)){
		foreach($memberinfo_wgroup as $minfo_wgroup){
			$mid=$minfo_wgroup->mid;
		}
		$memberinfo =  web_admin_member_mid_group($mid,$gweid);
		foreach($memberinfo as $minfo){
			$isaudit=$minfo->isaudit;
		}
	}else{
		$memberinfo=null;
	}
	/**
	*@function:已经登陆通过mid拿到会员信息
	*/
	if((empty($memberinfo))&&(!empty($mid))){				
		$memberinfo =  web_admin_member_mid_group($mid,$gweid);
		foreach($memberinfo as $minfo){
			$au_password=$minfo->password;
			$isaudit=$minfo->isaudit;
		}
		if($auth!= md5($mid.$au_password."weauth3647668")){//防止密码更新后
			$memberinfo=null;
			unset($_SESSION['gmid'][intval($gweid)]);
		}		
	}
	/*获取会员基本信息END*/
	
	if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&(empty($memberinfo)))){//开启页面会员限制，并且不是会员
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_login.php?gweid={$gweid}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
	}
	if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')&&((!empty($memberinfo))&&($vipaudit=='1')&&(($isaudit=='2')||($isaudit=='0'))))){//开启页面会员限制，没有通过审核
		header('Location: '.get_bloginfo('template_directory')."/../ReeooV3/wesite/common/vip_perdenied.php?gweid={$gweid}&isaudit={$isaudit}&redirect_url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])."#wechat_redirect");
		exit();	
	}
	
	if(!empty($memberinfo)){
		foreach($memberinfo as $member){
			$realname = $member->realname;
			$nickname = $member->nickname;	
		}
	}
 ?>
 
<?php
get_header();?>	

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
	<head>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/common.mobile.js.js?v=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/../ReeooV3/we7/script/jquery.form.js"></script>
<script type="text/javascript">
	$(function(){
		isSubmitting = false;
		var actionparm="mobiletheme";
		var ajax_option={			
			url:"<?php bloginfo('template_directory'); ?>/mobile_post.php?action="+actionparm+"&gweid=<?php echo $gweidtrue;?>"+"&siteId=<?php echo $siteId;?>"+"&redirect_url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>#wechat_redirect",
			success: function(data){
				if (data.status == 'insertsuc'){
					location.reload();
				}else if (data.status == 'success'){
					alert(data.message);						
					location.href="<?php bloginfo('template_directory'); ?>"+data.url;
				}else if (data.status == 'error'){
					alert(data.message);
				}else{
					alert("出现错误");
				}
				isSubmitting = false;
			},
		   error: function(data){
				alert("出现错误了");
				isSubmitting = false;
			},
			dataType: 'json'
		}
		$('#commentform').submit(function(){
			if(isSubmitting)
			return false;
			isSubmitting = true;
			$(this).ajaxSubmit(ajax_option);
			
			return false;
		});
		
		
		//上传图片ajaxsubmit
		$("#upload").wrap("<form id='myupload' action='<?php bloginfo('template_directory'); ?>/mobilepost_upload.php?gweid=<?php echo $gweidtrue;?>' method='post' enctype='multipart/form-data'></form>");
		var bar = $('.bar');
		var percent = $('.percent');
		var showimg = $('#showimg');
		var progress = $(".progress");
		var files = $(".files");
		var btn = $(".btnupload span");
		$("#upload").change(function(){
			$("#myupload").ajaxSubmit({
				dataType:  'json',
				beforeSend: function() {
					showimg.empty();
					progress.show();
					var percentVal = '0%';
					bar.width(percentVal);
					percent.html(percentVal);
					btn.html("正在上传...");
				},
				uploadProgress: function(event, position, total, percentComplete) {
					var percentVal = percentComplete + '%';
					bar.width(percentVal);
					percent.html(percentVal);
				},
				success: function(data) {	
					if(data.status == "上传成功" && data.pic != ""){
					    alert(data.status);
						btn.html("上传成功");
						var picurl = '<img src="'+data.pic+'" alt="" />';
						insertAtCursor(document.getElementById('blogContent'), picurl);	
						btn.html("上传图片");
						progress.hide();
					}else{
						alert(data.message);
						btn.html("上传图片");
						progress.hide();
					}					
				},
				error:function(xhr){
					alert("网络异常，请重新上传");
					btn.html("上传图片");
					progress.hide();
				}
			});
			$("#upload").val("");  //fix cancel bug  
			return false;
		});
	});
	
	//将上传的图片插入到textarea中光标所在的位置
	function insertAtCursor(myField, myValue) {
		//IE support
		if (document.selection) {
			myField.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
			sel.select();
		}
		//MOZILLA/NETSCAPE support 
		else if (myField.selectionStart || myField.selectionStart == '0') {
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			var restoreTop = myField.scrollTop;
			myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
			if (restoreTop > 0) {
				myField.scrollTop = restoreTop;
			}
			myField.focus();
			myField.selectionStart = startPos + myValue.length;
			myField.selectionEnd = startPos + myValue.length;
		} else {
			myField.value += myValue;
			myField.focus();
		}
	} 

	//for "一键关注微信号"
	// function WeiXinAddContact(wxid, cb)   
	// { 
	//  if (typeof WeixinJSBridge == 'undefined')  return false;  
	//  WeixinJSBridge.invoke('addContact', { webtype: '1', username: wxid  },  
	//  function(d) {   
	//   // 返回d.err_msg取值，d还有一个属性是err_desc //    add_contact:cancel 用户取消 //  add_contact:fail 关注失败   
	//   // add_contact:ok 关注成功   // add_contact:added 已经关注   
	//   WeixinJSBridge.log(d.err_msg);  cb && cb(d.err_msg); });
	//  }; 
	

</script>	
</head>
<!--测试公众号关注<a data-cke-saved-href="#" href="#" onclick="WeiXinAddContact('gh_d90dfa5a1d79')">点击关注</a>-->
</html>


<div class="entry">
<?php if(($useContact !=null) && (strlen($useContact)>0)) { ?>
	<div class="post">
		<U><a href="tel:<?php echo $useContact ?>">点此预定,联系我们</a></U>
		<br clear="all" />
	</div>
<?php };?>	
    <div id="container">
        <?php query_posts(array( 'post_type' => 'post','post_content_filtered'=> $siteId, 'orderby' => 'date', 'order' => 'DESC', 'showposts' => '999'));?>
        <?php if(have_posts()) : ?>
		<?php 
		//
		global $wpdb, $table_prefix;
		$tableName = $table_prefix.'orangesitemeta';
		$siteId = $_GET['site'];
		$keyName = 'mobilethemeSize';
		$sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
		$sitemeta = $wpdb->get_row($sql);
		$postCount = $sitemeta->site_value;
		$tablecount = $table_prefix.'posts';
		$keypost = 'post';
		$sql = "SELECT count(*) as count_p FROM  $wpdb->posts WHERE `post_content_filtered`='".$siteId."' and `post_type`='".$keypost."'";
		$post = $wpdb->get_row($sql);
		$postC = $post->count_p;
		//$postC = count($post);
		//echo $postC;
		if($postCount <= 0)
			$postCount = 5;	
		$haveView = 0;
		if(isset($_GET['haveview']))
			$haveView = $_GET['haveview'];
		$i=$haveView;
		$count = 0; ?>
		
            <?php while(have_posts()) : the_post(); 
			?>
            <?php if($post->post_content_filtered ==  $siteId){
					$i--;
					if($i>=0)
						continue;
					$count++;
					if($count>$postCount)
							break;
			?>
                <div <?php post_class(); ?> class="post" id="post-<?php the_ID(); ?>">
                    <?php if($isShowPic=='true'){?>					
                    <div class="imgpost"><?php mtheme_thumb_v2(); ?></div>
                    <?php } ?>
                    <div class="title">
                       <h2><a href="<?php the_permalink(); echo '&site='.$siteId.'&gweid='.$gweidtrue; ?>#wechat_redirect" title="<?php the_title(); ?>"><?php the_title(); if( is_sticky() ) echo '&nbsp;&nbsp;<span style="color:red;">置顶</span>'; ?></a></h2>
                    </div>
                    <div class="date"><?php if($isShowPic=='false'){  the_author();?>
                        发布于 <?php the_time('F j, Y');}?> 
                    </div>

                    <br clear="all" />
                </div>

            <?php } endwhile; ?>

            <div class="navigation">
			<?php 
				$url = curPageURL();
				if(stristr($url,"haveview=")){
					$url=substr( $url,0,strripos($url,'&haveview='));
				}
			?>
			<?php if($haveView>0) { 
					if($haveView>=$postC-$postCount){?>
					<div class="goleft"><a href="<?php echo $url.'&haveview='.($haveView-$postCount)?>">上一页</a></div>
					<?php } else{ ?>
					<div class="goleft"><a href="<?php echo $url.'&haveview='.($haveView-$postCount)?>">上一页</a></div>
					<div class="goright"><a href="<?php echo $url.'&haveview='.($haveView+$postCount) ?>">下一页</a></div>
					<?PHP } } ?>
			<?php if($haveView<=0 && $postC-$postCount > 0) {?>
					<div class="goright"><a href="<?php echo $url.'&haveview='.($haveView+$postCount) ?>">下一页</a></div>
				<?php } ?>
                <div class="clear"></div>
			<?php  ?>
            </div>
        <?php else : ?>
            <div class="post" id="post-<?php the_ID(); ?>">
                <h2><?php _e('No posts are added.'); ?></h2>
            </div>
        <?php endif; ?>
    </div>
</div> <!--entry-->
<br/><br/>    
    <?php //get_sidebar(); ?>  <!--去掉搜索框-->	
		 <!--add the function of publish the blog-->
    <?php if($isPostPermission == 'true'){ ?>
		<div id="respond">
            <h3>发表论题</h3>
		    <div style="font-size:10px">			(在本网站的评论或表述的任何意见均属于发布者个人意见，并不代表本网站的意见。请您遵守中华人民共和国相关法律法规，严禁发布扰乱社会秩序、破坏社会稳定以及包含色情、暴力等的言论。本网站有对您的论题进行删除的权利)
		    </div>
            <div class="cancel-comment-reply">
		        <small></small> 
		    </div>
		    <form action="" method="post" id="commentform">
				<?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember == 'true')||(!empty($memberinfo)))){?>
					<p>微信昵称:<?php echo $nickname ;?></p>
					<p style="display: none">			   
						<input class="author" type="text" value="<?php echo $nickname ;?>" onclick="this.value='';" name="blogAuthor" id="blogAuthor" size="22" tabindex="1"/><label for="author"><small><?php //if ($req) echo "(必填)"; ?></small></label>
					</p>
				<?php }else{?>
					<?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')||(!empty($memberinfo)))){?>
					<p>
						会员姓名（只有会员登陆可进行发表）
					</p>
					<?php }else{?>
						<p <?php if(($selCheck['wechatvip']==1)&&(($isShowVipmember_editor == 'true')||(!empty($memberinfo)))){?> style="display: none" <?php }?> >
							<input class="author" type="text" value="输入姓名..." onclick="this.value='';" name="blogAuthor" id="blogAuthor" size="22" tabindex="1"/><label for="author"><small><?php //if ($req) echo "(必填)"; ?></small></label>
						</p>
					<?php }
				} ?>		
				<p>
					<input class="url" type="text" value="输入标题..." onclick="this.value='';" name="blogTitle" id="blogTitle" size="22" tabindex="3"/><label for="email"><small>(必填)</small></label>
				</p>
				<p>
					<textarea name="blogContent" id="blogContent" tabindex="4"></textarea>
				</p>
				<p>
					<input type="hidden" name="siteId" value="<?php echo $siteId ?>"/>
					<!--<input type="file" name="file" id="upload1" tabindex="5" value="上传图片" style="margin-bottom:10px;"/>-->
				    <div class="upload" style="height:32px;"> 
						<div class="btnupload">
							<span>上传图片</span>
							<input id="upload" type="file" name="file">
						</div>
						<div class="progress">
							<span class="bar"></span><span class="percent">0%</span >
						</div>
						<div class="files"></div>
						<div id="showimg"></div>							
					</div>	
					<div style="margin-top:15px;"><input class="submit" name="submit" type="submit" id="submit" tabindex="5" value="提交" /></div>
				</p>
            </form>
        </div>
       <?php }?>
        <?php get_footer();    }
            else
                echo '请先激活必要的插件';
        ?>