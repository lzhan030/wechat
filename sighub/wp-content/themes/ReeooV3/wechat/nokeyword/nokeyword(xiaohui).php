<?php
$tmp_path = explode ( 'ReeooV3', __FILE__ );
$template_path=$tmp_path[0];
require_once $template_path.'ReeooV3/wechat/common/session.php';

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
global  $current_user;
if( !isset($current_user->user_login)|| empty($current_user->user_login)){
		wp_redirect(wp_login_url());
}	
get_header(); 
include '../common/wechat_dbaccessor.php';
 ?>
<?php	
	//20140623 janeen update
	//$mes_list=wechat_mess_kw_get("nokey",$_SESSION['WEID']);
	$mes_list=wechat_mess_kw_get_group("nokey",$_SESSION['GWEID']);
	//end
	foreach($mes_list as $message){
		$mes_type=$message->arply_type;
		if($mes_type=="weChat_text"){
			$mtext=wechat_text_get($message->arplymesg_id);	
		}
	
	}
    if(isset($_POST['clear_news']))
    {
        unset($_POST['clear_news']);
        //20140623 janeen update
        //$b = wechat_clearNews("nokey",$_SESSION['WEID']);
		$b = wechat_clearNews_group("nokey",$_SESSION['GWEID']);
		//end
        echo '<script>';
        if($b)
            echo "alert('删除成功!');";
        else
            echo "alert('没找到要删除的信息，删除失败!');";
        echo "</script>";
    }
	$is_select=$_GET['isselcet'];
	if($is_select==1){
		$newsid = $_GET['newsid'];
		$tab_ul = $_GET['tab'];
	}else{
		//20140623 janeen update
		//$mes=wechat_mess_kw_get("nokey",$_SESSION['WEID']);
		$mes=wechat_mess_kw_get_group("nokey",$_SESSION['GWEID']);
		//end
		foreach($mes as $message){
			$mespe=$message->arply_type;
			if($mespe=="weChat_news"){
				$newsid=$message->arplymesg_id;
				$tab_ul = 1;
			}
		}
	}
?>
 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery-1.10.2.min.js" type="text/javascript"></script>
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
	<script>
		   // KindEditor.ready(function(K) {
					// window.editor = K.create('#editor_id');
			// });
			KindEditor.ready(function(K) {
				window.editor = K.create('#editor_id', {
				items:["link","unlink"],
				width:'650px',
				height:'215px'}); //配置kindeditor编辑器的工具栏菜单项
				});
	</script>
	</head>
	<body>
		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：无匹配应答> <font class="fontpurple">应答内容设置 </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<div id="nav-main" style="margin-bottom:10px">
                <?php  
                if($tab_ul==NULL) $tab_ul = '0';
                //var_dump($tab_ul);
                ?>
				<ul class="nav nav-tabs">
					<li <?php if($tab_ul==0 || empty($tab_ul)) echo 'class="active selected"';?>><a href="javascript:void(0)" data-toggle="tab">文本</a></li>
					<li <?php if($tab_ul==1 ) echo 'class="active selected"';?>><a href="javascript:void(0)" data-toggle="tab">图文消息</a></li>
				</ul>
			</div>
			<div class="main_bd">
				<div  style="width:60%">
					<form action="" method="post">
						<td>
							<input type="button" onclick="useNews()" class="btn btn-primary" value="保存">
                            <a href="<?php echo get_template_directory_uri(); ?>/wechat/nokeyword/nokeyword.php?beIframe&tab=1"
                                   onclick="">
								<input type="submit" name="clear_news" class="btn btn-default" value="删除">
							</a>
							<input type="button" onClick="selectNews('<?php echo $newsid;?>')" class="btn btn-warning" value="选择多图文素材" style="margin-top:10px;margin-bottom:10px;width:140px"/>
						</td>
						<table class="table table-striped" width="450"  border="1" align="center">
							<tr>
								<!--<th scope="col" width="300"></th>
								<th scope="col" width="300">标题</th>
								<th scope="col" width="150">操作</th>-->
							</tr>
							
							</table>
					</form>
				</div>
				<div class="left nub0" >
					<div  class="pre-title0">
						<div class="pre-bg" >
							<p>封面图片</p>
						</div>
						 <span class="title1">标题</span> 
						 <input class="newsUrl" type="text" style="display:none" value=""/>
						 <input class="newsId" type="text"style="display:none"  value=""/>
					</div><!--封面-->
					<div class="pre-title1 pre-title" >
						<span class="title1" >标题</span>
						 <input class="newsUrl" type="text" style="display:none" value=""/>
						 <input class="newsId" type="text"style="display:none"  value=""/>
						<div > <span >缩略图</span> </div>						
					</div><!--标题-->
					<input class="getData" style="display:none" type="button" value="使用" />
					<input class="ediData" style="display:none" type="button" value="编辑" />
				</div><!--left-->		
			</div>
			
			<div class="pic">				
				<form action="nokeyword_text_insert.php?beIframe" method="post">				 
				<input type="button" onclick="useText()" class="btn btn-primary" value="保存" style="margin-top:10px;margin-bottom:20px"/>
				<input type="submit" name="clear_text" class="btn btn-default" value="删除" style="margin-top:10px;margin-bottom:20px"/>
					<?php 
					if($mtext!=null){
						foreach($mtext as $text){
							$texts=stripslashes($text->text_content);
						} 
						echo "<textarea id='editor_id' name='sendContent' cols='42' rows='11' style='width: 650px;hight=215px' >".$texts."</textarea>";
					}else{
						echo "<textarea id='editor_id' name='sendContent' cols='42' rows='11' style='width: 650px;hight=215px'></textarea>";
					}
					?>					
				</form>				
			</div>
		</div>
		<script type="text/javascript">
		var xmlHttp;
		function createXMLHttpRequest(){
		if(window.ActiveXObject)
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		else if(window.XMLHttpRequest)
		xmlHttp = new XMLHttpRequest();
		}
		
        $(function(e){
            var a = <?php echo $tab_ul;?>;
            if(a==1){$(".main_bd").show();$(".pic").hide();}
            else{$(".main_bd").hide();$(".pic").show();}

        })
			var itemTitle=new Array();
			var picUrl=new Array();
			var itemId=new Array();
			var itemUrl=new Array();
			var newsItemId=new Array();
			for(var i=0;i<10;i++){
				itemTitle[i]=new Array();
				picUrl[i]=new Array();
				itemId[i]=new Array();
				itemUrl[i]=new Array();
			}
			var newsCount=new Array();
	<?php
		//拿到标志用一个多图文的id
		//$nc=wechat_get_news_act($_SESSION['WEID']);
		if($newsid!=null){
			$rey=$newsid;
		
		}else{
			//20140623 janeen update
			//$reply=wechat_get_aplymesgid("nokey",$_SESSION['WEID']);
			$reply=wechat_get_aplymesgid_group("nokey",$_SESSION['GWEID']);
			//end
			foreach($reply as $ry){
				$rey=$ry->arplymesg_id;
			}
		}
		$nc=wechat_news_get($rey) ;
		$i=1;
		$newsay=array();
		foreach($nc as $ns){
			
			$newsay[$i]=$ns->news_item_id;
			$i++;
		}
		//$news_count=count($nc);
		$news_count=1;
		for($s=1;$s<=$news_count;$s++){			
						
					$i=0;
					$j=0;
					$k=0;
					$v=0;
			
			$materials=wechat_news_get($newsay[$s]);		
			foreach($materials as $material){
				
				echo "itemTitle[".$s."][".$i++."]=\"".$material->news_item_title."\";\n";		
				echo "picUrl[".$s."][".$j++."]=\"".$material->news_item_picurl."\";\n";
				echo "itemId[".$s."][".$k++."]=\"".$material->news_id."\";\n";
				echo "itemUrl[".$s."][".$v++."]=\"".$material->news_item_url."\";\n";

			}
		
			$newsc=wechat_get_news_count($newsay[$s]); 
				echo "newsItemId[".$s."]=\"".$newsay[$s]."\";\n";	
				foreach($newsc as $newc){				
					echo "newsCount[".$s."]=\"".$newc->counts."\";\n";
				
				}		
		 }	
		 ?>
		
		$(document).ready(function(e){
		
		var m=<?php echo $news_count ?>;
		for(var j=1;j<m;j++)
		{	var $addLeft=$(".left.nub0").clone("deep");
			
			$addLeft.removeClass().attr("class","left nub"+j);
			$addLeft.insertAfter(".left.nub"+(j-1));
					
		}
		})
		
		$(document).ready(function(e) {
		
		
		var m=<?php echo $news_count ?>;
		for(var s=1;s<=m;s++)
		{
		
			var n=newsCount[s];
	
			//显示	
			var a=".nub"+(s-1)+" .pre-title0,.nub"+(s-1)+" .pre-title1";
			var b=".nub"+(s-1)+" .pre-title1";
			
			if(n<1){$(a).css("display","none");}
			if(n<2){$(b).css("display","none");}
			if(n>2)
			{	
			  for(var j=2;j<n;j++)
			  {	
				var cloneUrl=".nub"+(s-1)+">.pre-title1";
				var addTo=".nub"+(s-1)+">.getData";
				var $add=$(cloneUrl).clone("deep");
				var changeId="pre-title"+j+" pre-title";
				
				$add.attr("class",changeId);
				$(addTo).before($add);
							  
				}
			}
		
		
				
		//读数据
			
		//for(2){
		//var changeid="nub1 pre-title"
	    for(var k=0;k<n;k++)
		{	 			 
			var changeId=".nub"+(s-1)+">.pre-title"+k;
			var bgid=".nub"+(s-1)+">.pre-title"+k+">div";
			var textid=".nub"+(s-1)+">.pre-title"+k+" span.title1";
			var newsUrl=".nub"+(s-1)+">.pre-title"+k+" input.newsUrl";
			var newsId=".nub"+(s-1)+">.pre-title"+k+" input.newsId";		  
		  
			var url="url("+picUrl[s][k]+")";
			var text=itemTitle[s][k];
			var news_Url=itemUrl[s][k];
			var news_Id=itemId[s][k];
							
			$(newsUrl).val(news_Url);
						
			$(newsId).val(news_Id);
				  
			$(bgid).css("background",url);
			$(bgid).css("background-size","100% 100%");
			$(textid).text(text);		  
		 
		}
		
		}
		
		
		$("#pre-add>a").click(function(e) {
			
			var $add=$(".pre-title1").clone("deep");
			var changeId="pre-title"+n;
			$add.attr("class",changeId);
			
			$(changeId).attr("class",changeId);
			
		
			$("#pre-add").before($add);
			
			var bgid="#"+changeId+">div";
			var textid="#"+changeId+" span:first-child";
			var newsUrl="#"+changeId+">input.newsUrl";
			var newsId="#"+changeId+">input.newsId";
			var url=""
			var text=""
			$(bgid).css("backgroud-image",url);
			$(bgid).css("background-size","100% 100%");
			$(textid).text(text)
			$(newsUrl).val("");
			$(newsId).val("");
		  
			i++;
			
			  
			
			var y=e.pageY-300;
			$(".right").animate({top:y});
		});//add

		//delete
		$(".pre-title>a").click(function(e){

			$(event.target.parentNode).remove();
			var y=e.pageY-200;
			
			$(".right").animate({top:y});
			
			})

		});
		  
		//getData
		
		  
		/*$(".ediData").click(function(){
			var a=$(this).parent().attr("class");
			var nub=a.substr(-1,1)*1+1;
			var nid=newsItemId[nub];
			var title=$("#title").val();
			var itemUrl=$("#itemUrl").val();
			var itemId=$("#itemId").val();
			location.href="<?php bloginfo('template_directory'); ?>/wechat/material/material_edit.php?beIframe&netId="+nid;
			 		  
		  })*/
		  
		$("#nav-main ul li").click(function(e){
			
			$("#nav-main ul li.selected").removeClass("selected");
			$(this).addClass("selected");
			
			
			})
			
			$(".main_bd").hide();
		$("#nav-main ul li:first-child").click(function(){
			$(".main_bd").fadeOut();
			$(".pic").fadeIn();
		})
		$("#nav-main ul li:last-child").click(function(){
			$(".pic").fadeOut();
			$(".main_bd").fadeIn();
			
		})
		
		function OK(){
	var aCheckBox=document.getElementsByName('inputCheckBox');

		for(var i=0; i<aCheckBox.length; i++){
			if(aCheckBox[i].getAttribute('type')=='checkbox'){
				if(aCheckBox[i].checked==true){
				//document.write(aCheckBox[i].value);
				//location.href='<?php echo constant("CONF_THEME_DIR"); ?>/wesite/mobilepagev2/menu_insert_dialog.php?posturl='+aCheckBox[i].value;
					//opener.Wmenuurl.style.visibility="visible";
					//opener.Wmenuurl.value=aCheckBox[i].value;
				var nid=aCheckBox[i].value;	
				var title=$("#title").val();
				var itemUrl=$("#itemUrl").val();
				var itemId=$("#itemId").val();
				location.href="<?php bloginfo('template_directory'); ?>/wechat/autorep/autoreply_image_insert.php?beIframe&netId="+nid;
					
				window.close();	
				}
			
		}
}
}
		
	function Cancle(){
	var aCheckBox=document.getElementsByName('inputCheckBox');

		for(var i=0; i<aCheckBox.length; i++)
		{
			if(aCheckBox[i].getAttribute('type')=='checkbox')
			{
				aCheckBox[i].checked=false;
			}
		}
}	

		function selectNews(newsid){	
			//window.open('nokeyword_image_select.php?beIframe&artType=post','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
			
			window.open('nokeyword_image_select.php?beIframe&artType=post&selectnewsid='+newsid,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		}
	
	
		function useNews(){
			
			//这里rey如果是空，页面失效，默认赋值为-1
			<?php 
			global $rey;
			if($rey==null){
				$rey=-1;
			}
			?>
			var newid=<?php  echo $rey ?>;
			
			//location.href="<?php bloginfo('template_directory'); ?>/wechat/autorep/autoreply_image_insert.php?beIframe&netId="+nid;
			if(newid!=-1){
				createXMLHttpRequest();
				xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/nokeyword/nokeyword_image_insert.php?beIframe&netId="+newid,true);
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						alert("保存成功!");
						var url="<?php echo get_template_directory_uri(); ?>";
						location.href=url+'/wechat/nokeyword/nokeyword.php?beIframe&tab=1';
					}
				}
				xmlHttp.send(null);
			}else{
				alert("请先选择一个多图文");
			}
		}
		
		function useText(){
			editor.sync();
			var content=document.getElementById('editor_id').value;
			if(content!=""){
				createXMLHttpRequest();
				xmlHttp.open("GET","<?php echo constant("CONF_THEME_DIR"); ?>/wechat/nokeyword/nokeyword_text_insert.php?beIframe&content="+escape(content),true);
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						alert("保存成功!");
						var url="<?php echo get_template_directory_uri(); ?>";
						location.href=url+'/wechat/nokeyword/nokeyword.php?beIframe&tab=0';
					}
				}
				xmlHttp.send(null);		
			}else{
				alert("请输入文本内容");
			}
		}	
	

		</script>
		
		
	</body>
</html>