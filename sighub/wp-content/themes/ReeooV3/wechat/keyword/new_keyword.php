<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	

include '../common/wechat_dbaccessor.php';
include 'keyword_permission_check.php';
?>
<?php
	$news_item_id=$_GET["news_item_id"];
	$keyword=$_GET["keyword"];
	if ($news_item_id!=null) {
		$keyword_news_content=wechat_news_get($news_item_id);
		
		//obtain the newsid
		foreach ($keyword_news_content as $content) {
		    $newsid = $content->news_item_id;
		}
		
	}
	if ($_GET['tab'] == null) {
		$tab_ul=0;
	} else {
		$tab_ul=$_GET['tab'];
	}
?>
 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/table1.css" />
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/editor/themes/default/default.css" />
	<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/kindeditor.js"></script>
	<script charset="utf-8" src="<?php bloginfo('template_directory'); ?>/js/editor/lang/zh_CN.js"></script>
	</head>
	<body>
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：关键词应答> <a href="<?php echo constant("CONF_THEME_DIR"); ?>/wechat/keyword/keyword_list.php?beIframe">关键词列表</a>> <font class="fontpurple">新建关键词 </font>
			</div>
		</div>
		<div class="bgimg"></div>
		<div class="submain">
			<div class="key">
				<p>请输入关键词: </p>
					<input id='addedKeyWord' class='form-control' type='text' value='<?php echo $keyword;?>' style='margin-top:20px'/>							
			</div>
			<div class="main_auto keyWordMain" style="padding:0;">	
				<div id="" class="navs">
					<ul class="nav nav-tabs" id="myTab">
						<li><a href="#txtTab" onclick="return false;">文本消息</a></li>
						<li><a href="#imgsTab" onclick="return false;">图文消息</a></li>
					</ul>				
				</div>	
				<div class="tab-content">
					<div class="tab-pane" id="txtTab">				
						<div class="nub-1">
							<table style="width:100%;">
								<tr style="text-align:left;height:40px"><td>关键词自动回复（若保存文本消息，则原有图文消息将会被自动删除）</td></tr>
								<tr>
									<td>
										<textarea id="editor_id" name="sendContent" class="editor" cols="42" rows="11" style="width:95%;hight=215px">
										</textarea>
									</td>
								</tr>
								<tr style="text-align:left;height:40px"><td>注意：文本要小于2048字节（对应682个中文汉字)</td></tr>
								<tr class="btncenter">
									<td>
										<input class="btn btn-primary btmtxtbtn" type="button" value="保存" onclick="saveTxt()"/>
										<input class="btn btn-default btmtxtbtn" type="button" value="取消" onclick="cancel()"/>
									</td>
								</tr>								
							</table>
						</div>
					</div>
					<div class="tab-pane" id="imgsTab">
						<table>
							<form action="" method="post">
							<tr style="height:40px;text-align:left"><td>关键词自动回复（若保存图文消息，则原有文本消息将会被自动删除）</td></tr>
							<tr><td>
								<div class="left" >
								<?php
								if ($news_item_id!=null) {
									$i=0;
									$upload =wp_upload_dir();
									foreach ($keyword_news_content as $content) {
										if((empty($content->news_item_picurl))||(stristr($content->news_item_picurl,"http")!==false)){
											$newsitempic=$content->news_item_picurl;
										}else{
											$newsitempic=$upload['baseurl'].$content->news_item_picurl;
										}
										if ($i==0){
											echo "<div class='pre-title0'>";
											echo "<div class='pre-bg' style='background-image: url(".$newsitempic."); background-size:100% 100%'>" ;
											echo "<p>封面图片</p></div>";
											echo "<span class='title1'>".$content->news_item_title."</span></div>";
											$i++;
										} else {
											echo "<div class='pre-title1 pre-title'><span class='title1' >".$content->news_item_title."</span>";
											echo "<div style='background-image: url(".$newsitempic."); background-size:100% 100%'>";
											echo "<span>缩略图</span></div></div>";
											$i++;
										}
									}
								}else {
									echo "<div class='pre-title0'>";
										echo "<div class='pre-bg'>";
											echo "<p>未添加图文消息</p>";
										echo "</div>";
										echo "<span class='title1'></span> ";
										echo "<input class='newsUrl' type='text' style='display:none' value=''/>";
										echo "<input class='newsId' type='text' style='display:none'  value=''/>";
									echo "</div><!--封面-->";
									echo "<div class='pre-title1 pre-title' >";
										echo "<span class='title1' >未添加图文消息</span>";
										echo "<input class='newsUrl' type='text' style='display:none' value=''/>";
										echo "<input class='newsId' type='text' style='display:none'  value=''/>";
										echo "<div><span>缩略图</span></div>";		
									echo "</div><!--标题-->";
								}
								?>
								</div><!--left-->	
							</td></tr>
							<tr><td>
								<input class=" btn btn-primary btmimgbtn" type="button"  value="保存" style="margin-left:15px" onclick="saveNews(<?php echo $news_item_id;?>)"/>
								<input class="btn btn-default btmimgbtn" type="button" value="取消" onclick="cancel()"/>	
								<input type="button" onClick="selectNews('<?php echo $newsid;?>')" class="btn btn-warning btmimgbtn" name="del" id="buttondel" value="选择多图文素材" style="width:140px"/> 													
							</td></tr>
							</form>
						</table>
					</div>			
				</div>
			</div>
		</div>
	</div>
	</body>
	<script language='javascript'>
		var xmlHttp;
		var a=0;
		function createXMLHttpRequest(){
			if(window.ActiveXObject){
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}else if(window.XMLHttpRequest) {
				xmlHttp = new XMLHttpRequest();
			}else {
				alert("您的浏览器不支持XMLHTTP！");
			}
		}
		
		//Tab selection function
		$(function() {
			// init the pages
			$(".tab-pane").hide(); // hide all contents
			
			// adjust which tab should be shown first
			a= <?php echo $tab_ul;?>;
			if (a==1) {
				$("ul.nav li:last").addClass("active").show();
				$(".tab-pane:last").show();
			} else {
				$("ul.nav li:first").addClass("active").show(); // active the first tab
				$(".tab-pane:first").show(); // show the first tab content
			}

			$('ul.nav li').click(function(){
				$("ul.nav li").removeClass("active"); // remove all active class
				$(this).addClass("active"); // add active class at the selected tab
				$(".tab-pane").hide(); // hide all contents
				
				var activeTab = $(this).find("a").attr("href"); // find the href attribute value to identify the active tab + content
				$(activeTab).fadeIn(); // Fade in the active ID content
			})
		})
	
		$(document).ready(function(e) {
			//放在这个位置，包含autoclick，避免图文切换文本时editor不存在
			KindEditor.ready(function(K) {
				window.editor = K.create('#editor_id', {
				//items:["emoticons","link","unlink"],
				items:["link","unlink"],
				width:'95%',
				height:'215px'}); //配置kindeditor编辑器的工具栏菜单项
			});	
		
		});	
		
		//save new text msg
		function saveTxt() {
		    editor.sync();
			createXMLHttpRequest();
			var keyword=$("#addedKeyWord").val();
            if(editor.text().length<=0 || keyword.length<=0){
                alert("关键字及内容都不能为空！请重新输入");
                return;
            }
			
			var content=document.getElementById('editor_id').value;
			str = content.replace(/<[^>].*?>/g,""); 
			jQuery.post(
		        "<?php echo constant("CONF_THEME_DIR");?>/wechat/keyword/add_keyword_txt.php?keyword="+encodeURIComponent(keyword),
		        {content : document.getElementById('editor_id').value },
		        function(data, textStatus, jqXHR){
					var str = data;
                    alert(str);
                    if(str.indexOf("添加成功")>0){
                        window.location.href="<?php echo constant("CONF_THEME_DIR");?>/wechat/keyword/keyword_list.php?beIframe";
                    }
		        },
		        "text"
		    ).fail(function(){
		    	alert("新关键词保存失败！");
		    });
		}
		
		//cancel button
		function cancel() {
		    location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wechat/keyword/keyword_list.php?beIframe';
		}
		
		//open img selection page
		function selectNews(newsid){
			var keywordId=null;
			var keyword=$("#addedKeyWord").val();
			window.open("keyword_news_list_dlg.php?keywordId="+keywordId+"&keyword="+keyword+"&selectnewsid="+newsid,"_blank","height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
		}

		//save img msg
		function saveNews(news_item_id) {
			window.param=news_item_id;
			createXMLHttpRequest();
			var keyword=$("#addedKeyWord").val();
            if(news_item_id.length<=0 || keyword.length<=0){
                alert("关键字及图文都不能为空！请重新输入");
                return;
            }
			xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR");?>/wechat/keyword/add_keyword_img.php?keyword="+keyword+"&news_item_id="+news_item_id, true);
			xmlHttp.onreadystatechange = function(){
				if (xmlHttp.readyState==4) {
					if (xmlHttp.status!=200) {
						alert("新关键词保存失败！");
					}
                    var str = xmlHttp.responseText;
                    alert(str);
                    if(str.indexOf("添加成功")>0){
                        window.location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wechat/keyword/keyword_list.php?beIframe';
                    }
				}
            }
			xmlHttp.send(null);
		}
	</script>	
</html>