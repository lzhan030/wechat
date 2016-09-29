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

//拿到window.open里传递过来的值	
$menuId=$_GET["menuId"];
$menuType=$_GET["menuType"];
$menuKey=$_GET["menuKey"];
$menuPad=$_GET["menuPad"];

 ?>

 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	</head>
	<body>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">多图文列表</h3>
		</div>
		<div class="panel-body">
			<input class="btn btn-primary" type="button" value="创建新文图" onclick="createNews()"></input>
			
				<div class="main_bd">
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
						
						<input class="ediData btn btn-sm btn-warning" type="button" value="编辑"/>
						<input class="selData btn btn-sm btn-info" type="button" value="选择"/>
					</div><!--left-->
				</div>
			
				<div class="textNews">				
					<div class="nub-1">
						<textarea name='sendContent' cols='42' rows='11' ></textarea>				
						<input class="connect btn btn-sm btn-info" type="button" value="关联" />
					</div>
				</div>
			
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
		
		
		var arplyId=new Array();
		var newsCount=new Array();
		var arplyType=new Array();
		var arplyKwd=new Array();
		var arplyMesId=new Array();
	
		
				
	<?php
		$keyList=wechat_mess_kw_list_group($_SESSION['GWEID']);
		$s=1;
		foreach($keyList as $key){				
				echo "arplyId[".$s."]=\"".$key->arply_id."\";\n";
				echo "arplyType[".$s."]=\"".$key->arply_type."\";\n";
				echo "arplyMesId[".$s."]=\"".$key->arplymesg_id."\";\n";
				echo "arplyKwd[".$s."]=\"".$key->arply_keyword."\";\n";

				$s=$s+1;
		}
				
		//拿到标志用一个多图文的id
		$nc=wechat_get_news_act_group($_SESSION['GWEID']);
		$i=1;
		$newsay=array();
		foreach($nc as $ns){
			
			$newsay[$i]=$ns->news_item_id;
			$i++;
		}
		$news_count=count($nc);
		
		for($s=1;$s<=$news_count;$s++){			
						
					$i=0;
					$j=0;
					$k=0;
					$v=0;
			
			$materials=wechat_news_get($newsay[$s]);		
			$upload =wp_upload_dir();
			
			foreach($materials as $material){
				/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
				$tmp = stristr($material->news_item_url,"http");
				if(($tmp===false)&&(!empty($material->news_item_url))){
					$newsitemurl=home_url().$material->news_item_url;
				}else{				
					$newsitemurl=$material->news_item_url;
				}
				if((empty($material->news_item_picurl))||(stristr($material->news_item_picurl,"http")!==false)){
					$newsitempicurl=$material->news_item_picurl;
				}else{
					$newsitempicurl=$upload['baseurl'].$material->news_item_picurl;
				}
				
				echo "itemTitle[".$s."][".$i++."]=\"".$material->news_item_title."\";\n";		
				echo "picUrl[".$s."][".$j++."]=\"".$newsitempicurl."\";\n";
				echo "itemId[".$s."][".$k++."]=\"".$material->news_id."\";\n";
				echo "itemUrl[".$s."][".$v++."]=\"".$newsitemurl."\";\n";

			}
		
			$newsc=wechat_get_news_count($newsay[$s]); 
				echo "newsItemId[".$s."]=\"".$newsay[$s]."\";\n";	
				foreach($newsc as $newc){				
					echo "newsCount[".$s."]=\"".$newc->counts."\";\n";
				
				}		
		 }	
		 ?>
		
		$(document).ready(function(e){
			
			//拿到多图文总的条数
			var m=<?php echo $news_count ?>;
			
			for(var j=1;j<m;j++)
			{	var $addLeft=$(".left.nub0").clone("deep");//
				
				$addLeft.removeClass().attr("class","left nub"+j);//
				$addLeft.insertAfter(".left.nub"+(j-1));//在left.nubi后面加上addLeft
						
			}
		})
		
		$(document).ready(function(e) {		
			//显示keyword
			var m=<?php echo $news_count ?>;
			for(var s=1;s<=m;s++){			
				var n=newsCount[s];
		
				//显示	
				var a=".nub"+(s-1)+" .pre-title0,.nub"+(s-1)+" .pre-title1";
				var b=".nub"+(s-1)+" .pre-title1";
				
				if(n<1){$(a).css("display","none");}
				if(n<2){$(b).css("display","none");}
				if(n>2)	{	
				  for(var j=2;j<n;j++){
					
					var cloneUrl=".nub"+(s-1)+">.pre-title1";
					var addTo=".nub"+(s-1)+">.ediData";
					var $add=$(cloneUrl).clone("deep");
					var changeId="pre-title"+j+" pre-title";
					
					$add.attr("class",changeId);
					$(addTo).before($add);
								  
					}
				}
		
				for(var k=0;k<n;k++){	 			 
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
		  
			
		$(".key>ul>li").dblclick(function(){
		
			if($(".key>ul>li>input").html()==""){alert("上一条还没修改呢");}
			else{
			$(this).append("<input type='text'/><button onClick='save()'>保存</button>");
			$(this).find("input").val($(this).find("a").text());
			
			}
		})
	
		
		 
		$(".selData").click(function(){					
			var a=$(this).parent().attr("class");
			var nub=a.substr(-1,1)*1+1;
			var nid=newsItemId[nub];
			var title=$("#title").val();
			var itemUrl=$("#itemUrl").val();
			var itemId=$("#itemId").val();
			
			var menuId=<?php echo $menuId ?>;
			var menuType='<?php echo $menuType ?>';
			var menuKey='<?php echo $menuKey ?>';
			var menuPad=<?php echo $menuPad ?>;
			var id=menuId+'|'+menuType+'|'+menuKey+'|'+menuPad;	
			setTimeout("self.close()", 1000);
			opener.location.href='<?php echo get_template_directory_uri(); ?>/wechat/menu/menu.php?beIframe&menusecid='+menuId+"&newid="+nid;	
		})


		
	//删除关联
		$(".unconnect").click(function(e){
			if($(this).parent().attr("class").match("keyNews")){
				var a=$(this).parent().attr("class");
				var nub=parseInt(a.substr(8))+1;
			
				$("."+nub).attr("class","").addClass("a selected");
				$(this).parent().removeClass("keyNews");
			}else{alert("请选中和本新闻关联的关键词")}
		})
	//删除关联
	//样式 数据关联
	
	//切换图文/文本
			$(".textNews").hide();
		$("#nav>ul:nth-child(1) li:first-child").click(function(){
			$(".main_bd").fadeOut();
			$(".textNews").fadeIn();
		})
		$("#nav>ul:nth-child(1) li:last-child").click(function(){
			$(".textNews").fadeOut();
			$(".main_bd").fadeIn();
			
		})
		$("#nav>ul:nth-child(1) li").click(function(e){
			
			$("#nav ul li.selected").removeClass("selected");
			$(this).addClass("selected");			
			
		})	
	//切换图文/文本

</script>
	
	</body>
</html>