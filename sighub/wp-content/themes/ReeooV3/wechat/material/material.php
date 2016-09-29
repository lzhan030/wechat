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
 ?>
 
<?php
    include_once '../common/wechat_dbaccessor.php';
    include_once '../../wesite/common/dbaccessor.php';
    include '../../wesite/common/web_constant.php';  
	//判断是否是分组管理员的公众号,分组管理员不需要进行此功能的check
	$groupadmincount = is_superadmin($_SESSION['GWEID']);
	if($groupadmincount == 0) 
       include 'material_permission_check.php';
   //获取所有的site
	$material_list=web_admin_list_material_group($_SESSION['GWEID']);
	
?> 
<script language="javascript">
	self.close();
</script>
 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<meta name="Author" content="SeekEver">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.Jcrop.js" type="text/javascript"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/test.css" type="text/css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	
	<script>
	    var xmlHttp;
		function createXMLHttpRequest(){
			if(window.ActiveXObject)
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			else if(window.XMLHttpRequest)
				xmlHttp = new XMLHttpRequest();
		}
		
		function delMaterial(id){          
			if(confirm("确定删除吗？")){
				createXMLHttpRequest();
				xmlHttp.open("GET","<?php bloginfo('template_directory'); ?>/wechat/material/material_delete.php?beIframe&netId="+id,true);
				xmlHttp.onreadystatechange = function(){
					if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
						alert("删除成功");
						window.location.reload();
					}				
				} 
				xmlHttp.send(null);
			}
	    }
	</script>
	<style>
		.main_auto{padding:0 30px;}
		.title-1{padding-left:0px;}
		.alert{border-radius: 0px;-webkit-box-shadow: 0 0 0 rgba(0,0,0,0.05);box-shadow: 0 0 0 rgba(0,0,0,0.05);}
		.panel{border-radius: 0px;-webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05);box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
	</style>
	</head>
	<body>
	
		<div class="main_auto">
			<div class="main-title">
				<div class="title-1">当前位置：素材管理> <font class="fontpurple">素材内容设置 </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<div id="nav-main" style="margin-bottom:10px">
				<ul class="nav nav-tabs">
					<li class="active selected"><a href="javascript:void(0)" data-toggle="tab">图文消息</a></li>
				</ul>
			</div>
			<input class="btn btn-primary" style="width:120px;margin-top:20px;display:block" value="创建多图文" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/material/material_edit.php?beIframe&netId=0'" type="button"/>
			<div class="panel panel-default" style="margin-top:20px">
			<table class="table table-striped" width="800" border="1" align="center">
			<tbody>
				<tr>
					<td scope="col" width="100" align="center" style="font-weight:bold">编号</td>
					<td scope="col" width="150" align="center" style="font-weight:bold">图文名称</td>
					<td scope="col" width="150" align="center" style="font-weight:bold">操作</td>
				</tr>
				
				<?php 
					$pagesize=7; //设定每一页显示的记录数						
					//-----------------------------------------------------------------------------------------------//
					//分页逻辑处理
					//-----------------------------------------------------------------------------------------------
					$materialsCount = web_admin_count_material_group($_SESSION['GWEID']);
					foreach($materialsCount as $materialsnumber){
						 $countnumber=$materialsnumber->materialCount;
						 
					}
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页
				   
					$offset=$pagesize*($page - 1);

					$rs=web_admin_array_material_group($_SESSION['GWEID'],$offset,$pagesize);//取得—当前页—记录集！
					
					$arraysCount=web_admin_array_material_count_group($_SESSION['GWEID'],$offset,$pagesize);
					foreach($arraysCount as $arraynumber){
						 $count_number=$arraynumber->arrayCount;
					}
				?> 
				
				<?php
		
					foreach ($rs as $material) {
				?>
				<tr>					
					<td align="center"><?php echo $material->news_item_id; ?> </td>
					<td align="center"><?php echo $material->news_name; ?> </td>
					
					<td class="row" align="center"><input name="site_id" type="hidden" id="site_id" value="308" maxlength="100"> <input type="button" class="btn btn-sm btn-warning" onclick="delMaterial(<?php echo $material->news_item_id?>)" name="del" id="buttondel" value="删除"> <input type="button" class="btn btn-sm btn-info" onclick="location.href='<?php bloginfo('template_directory'); ?>/wechat/material/material_edit.php?beIframe&netId=<?php echo $material->news_item_id ?>'" name="upd" id="buttonupd" value="编辑"> </td>					
				</tr>
				<?php
				}
				?>
				
				</tr>
			</tbody>
			</table>
			
        </div>
			<?php
				//============================//
				//  翻页显示 一               
				//============================//
					echo "<p>";  //  align=center
					$first=1;
					$prev=$page-1;   
					$next=$page+1;
					$last=$pages;

				if ($page > 1)
					{
						echo "<a href='?beIframe&page=".$first."'>首页</a>  ";
						echo "<a href='?beIframe&page=".$prev."'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='?beIframe&page=".$next."'>下一页</a>  ";
						echo "<a href='?beIframe&page=".$last."'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

					//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
					echo "</p>";

			?>
		</div>
<?php 
		
		//拿到标志用一个多图文的id
		$nc=wechat_get_news_act_group($_SESSION['GWEID']);
		$i=1;
		$newsay=array();
		foreach($nc as $ns){
			
			$newsay[$i]=$ns->news_item_id;
			$i++;
		}
		$news_count=count($nc);
?>		
		
		
	<script type="text/javascript">
		
		var itemTitle=new Array();
		var picUrl=new Array();
		var itemId=new Array();
		var itemUrl=new Array();
		var newsItemId=new Array();
		var n="<?php echo $news_count ?>";
		for(var i=0;i<n+5;i++){

		 itemTitle[i]=new Array();
		 picUrl[i]=new Array();
		 itemId[i]=new Array();
		 itemUrl[i]=new Array();
		
		}		
		var newsCount=new Array();		
		
	<?php
		
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
		
			var m=<?php echo $news_count ?>;
			for(var j=1;j<m;j++){	
				var $addLeft=$(".left.nub0").clone("deep");			
				$addLeft.removeClass().attr("class","left nub"+j);
				$addLeft.insertAfter(".left.nub"+(j-1));
						
			}
		})
		
		$(document).ready(function(e) {		
			var m=<?php echo $news_count ?>;
			for(var s=1;s<=m;s++){		
				var n=newsCount[s];	
				//显示	
				var a=".nub"+(s-1)+" .pre-title0,.nub"+(s-1)+" .pre-title1";
				var b=".nub"+(s-1)+" .pre-title1";
			
				if(n<1){$(a).css("display","none");}
				if(n<2){$(b).css("display","none");}
				if(n>2){	
					for(var j=2;j<n;j++){	
						var cloneUrl=".nub"+(s-1)+">.pre-title1";
						var addTo=".nub"+(s-1)+">.getData";
						var $add=$(cloneUrl).clone("deep");
						var changeId="pre-title"+j+" pre-title";
				
						$add.attr("class",changeId);
						$(addTo).before($add);
							  
					}
				}				
				//读数据
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
		$(".getData").click(function(){
			var a=$(this).parent().attr("class");
			var nub=a.substr(-1,1)*1+1;
			var nid=newsItemId[nub];
			var title=$("#title").val();
			var itemUrl=$("#itemUrl").val();
			var itemId=$("#itemId").val();
			location.href="<?php bloginfo('template_directory'); ?>/wechat/material/material_edit.php?beIframe&netId="+nid;
			 		  
		})
		  
		$("#nav-main ul li").click(function(e){			
			$("#nav-main ul li.selected").removeClass("selected");
			$(this).addClass("selected");			
		})
			
		$(".pic").hide();
		$("#nav-main ul li:first-child").click(function(){
			$(".main_bd").fadeOut();
			$(".pic").fadeIn();
		})
		$("#nav-main ul li:last-child").click(function(){
			$(".pic").fadeOut();
			$(".main_bd").fadeIn();
			
		})
		</script>
	</body>
</html>