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
include_once '../../wesite/common/dbaccessor.php';
include_once '../common/wechat_dbaccessor.php';

//obtain current newsid from autoreply page
$selectnewsid = $_GET['selectnewsid'];

//get all materials list
//20140623 janeen update start
//$news=material_news_getlist($_SESSION['WEID']);
$news=material_news_getlist_group($_SESSION['GWEID']);
//end
 ?>

<?php	
	
	$mtext=wechat_text_get(15);	
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
	<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">

	</head>
	<body>
	<div style="padding:0 30px">
	    <div class="main-title">
			<div class="title-1"><font class="fontpurple">图文列表： </font>
			</div>
		</div>
		<div class="bgimg"></div>
		<!--<input class="btn btn-primary" type="button" value="创建新文图" onclick="createNews()" ></input>-->
		<div class="submenu">
		<div class="panel panel-default" style="margin-top:30px">
			<div class="panel-heading">已建图文列表</div>
			
			<!--<div class="panel-body">
			<input class="btn btn-primary" type="button" value="创建新图文" onclick="createNews()"></input>
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
						<!--<div class="pre-title1 pre-title" >
							<span class="title1" >标题</span>
							 <input class="newsUrl" type="text" style="display:none" value=""/>
							 <input class="newsId" type="text"style="display:none"  value=""/>
							<div > <span >缩略图</span> </div>						
						</div><!--标题-->
						<!--<input class="getData btn btn-sm " type="button" value="使用" style="margin:5px 3px 5px 10px;"/>-->
						<!--<input class="ediData btn btn-sm btn-warning" type="button" value="编辑"/>
						<input class="selData btn btn-sm btn-info" type="button" value="选择"/>
					</div><!--left-->		
				<!--</div>
			</div>-->	
			
			<table class="table table-striped" width="800"  border="1" align="center">
						<tr>
							<td align="center" style="font-weight:bold"></td>
							<td align="center" style="font-weight:bold">编号</td>
							<td align="center" style="font-weight:bold">素材名称</td>
							<!--<td align="center" style="font-weight:bold">操作</td>-->
						</tr>
						
						<?php 
					$pagesize=7; //设定每一页显示的记录数						
					//-----------------------------------------------------------------------------------------------//
					//分页逻辑处理
					//-----------------------------------------------------------------------------------------------

					//20140623 janeen update start
					//$materialsCount = web_admin_count_material($_SESSION['WEID']);
					$materialsCount = web_admin_count_material_group($_SESSION['GWEID']);
					//end
					//echo $materialsCount;//获取wp_users表的记录总数
					
					foreach($materialsCount as $materialsnumber){
						 $countnumber=$materialsnumber->materialCount;
						 
					}
					//echo $countnumber;
					
					$pages=intval($countnumber/$pagesize); //计算总页数

					if ($countnumber % $pagesize) $pages++;

					//设置缺省页码
					//↓判断“当前页码”是否赋值过
					if (isset($_GET['page'])){ $page=intval($_GET['page']); }else{ $page=1; }//否则，设置为第一页
				   
					//↓计算记录偏移量
						$offset=$pagesize*($page - 1);

					//↓读取指定记录数 
					//20140623 janeen update start					
					//$rs=web_admin_array_material($_SESSION['WEID'],$offset,$pagesize);//取得—当前页—记录集！
					$rs=web_admin_array_material_group($_SESSION['GWEID'],$offset,$pagesize);//取得—当前页—记录集！
					//end
					
					//一个function活的总个数
					//foreach (){$curNem=iii->as bianliang}
					//$curNum = mysql_num_rows($rs); //$curNum - 当前页实际记录数，for循环输出用
					//20140623 janeen update start
					//$arraysCount=web_admin_array_material_count($_SESSION['WEID'],$offset,$pagesize);
					$arraysCount=web_admin_array_material_count_group($_SESSION['GWEID'],$offset,$pagesize);
					//end
					foreach($arraysCount as $arraynumber){
						 $count_number=$arraynumber->arrayCount;
						 //echo $count_number;
					}
				?> 
						
						<?php   
						//foreach($news as $ns){
				        foreach($rs as $ns){
						
						   $check= $selectnewsid == $ns->news_item_id ? "checked='checked'" : '';
							echo "<tr>";								
							echo "<td width=50 style='text-align:center'><input type='radio' {$check} id='myCheck' name='inputCheckBox' value='".$ns->news_item_id."'/></td>";					
							echo "<td style='text-align:center'>$ns->news_item_id</td>";
							echo "<td style='text-align:center'>$ns->news_name</td>";
							//echo "<td style='text-align:center'> <input type='button' class='btn btn-sm btn-warning' onclick=\"editMaterial('$ns->news_item_id')\"  value='编辑'> </td>";
							//echo onclick=\"location.href='".get_bloginfo('template_directory')."/wechat/material/material_edit.php?beIframe&netId={$ns->news_item_id}'\";
							echo "</tr>";
						}
						?>
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
						echo "<a href='?beIframe&page=".$first."&selectnewsid=".$selectnewsid."'>首页</a>  ";
						echo "<a href='?beIframe&page=".$prev."&selectnewsid=".$selectnewsid."'>上一页</a>  ";
					}

				if ($page < $pages)
					{
						echo "<a href='?beIframe&page=".$next."&selectnewsid=".$selectnewsid."'>下一页</a>  ";
						echo "<a href='?beIframe&page=".$last."&selectnewsid=".$selectnewsid."'>尾页</a>  ";
					}

					//============================//
					//  翻页显示 二               
					//============================//
				echo " | 共有 ".$pages." 页(".$page."/".$pages.")";

				for ($i=1;$i< $page;$i++){echo "<a href='?beIframe&page=".$i."&selectnewsid=".$selectnewsid."'>[".$i ."]</a>  ";}  // 1-先输出当前页之前的

				if ($page > 0) echo " [".$page."]";; // 2-再输出当前页

				for ($i=$page+1;$i<=$pages;$i++){echo "<a href='?beIframe&page=".$i."&selectnewsid=".$selectnewsid."'>[".$i ."]</a>  ";}// 3-接着输出当前页之后

					//echo "转到第 <INPUT maxLength=3 size=3 value=".($page+1)." name=gotox> 页 <INPUT hideFocus onclick=\"location.href='?page=gotox.value';\" type=button value=Go name=cmd_goto>"; 
					echo "</p>";

			?>
			
			<div style="text-align:center">
					<input class=" btn btn-primary btmtxtbtn" type="button" value="保存" onclick="OK()"/>
					<input class="btn btn-default btmtxtbtn" type="button" value="取消" onclick="Cancle()"/>
			</div>
		</div>
	</div>
		<script type="text/javascript">
		
		
		function OK(){
			var m=0;
			var aCheckBox=document.getElementsByName('inputCheckBox');

			for(var i=0; i<aCheckBox.length; i++){
				if(aCheckBox[i].getAttribute('type')=='radio'){
					if(aCheckBox[i].checked==true){
						var nid = aCheckBox[i].value;
						opener.location.href='<?php echo get_template_directory_uri(); ?>/wechat/autorep/autoreply.php?beIframe&isselcet=1&tab=1&newsid='+nid;		
						m=m+1;
						window.close();	
					}
				}
			}
			if(m==0){
				alert("请先选择一个素材！");
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
			window.opener=null;
			setTimeout("self.close()",0);
	    }
		
		function editMaterial(nid)
		{
		
		    window.showModalDialog('<?php echo get_template_directory_uri(); ?>/wechat/material/material_edit.php?beIframe&netId='+nid,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')			
						
			var url="<?php echo get_template_directory_uri(); ?>";			
			window.location.href=url+'/wechat/autorep/autoreply_image_select.php?beIframe&artType=post';
			
		}
		
		
		
		var xmlHttp;
		function createXMLHttpRequest(){
		if(window.ActiveXObject)
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		else if(window.XMLHttpRequest)
		xmlHttp = new XMLHttpRequest();
		}
		
		
		function createNews(){	   
			
			//用这种open，关闭后，下一条才href，如果用window.open，没有关闭就直接执行下面的了
			window.showModalDialog('<?php echo get_template_directory_uri(); ?>/wechat/material/material_edit.php?beIframe&netId=0','_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')			
			
			
			var url="<?php echo get_template_directory_uri(); ?>";
			
			window.location.href=url+'/wechat/autorep/autoreply_image_select.php?beIframe&artType=post';
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
	
		
				
	<?php
		
		
		
		//拿到标志用一个多图文的id
		//20140623 janeen add start
		//$nc=wechat_get_news_act($_SESSION['WEID']);
		$nc=wechat_get_news_act_group($_SESSION['GWEID']);
		//end
		
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
				var addTo=".nub"+(s-1)+">.ediData";
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
		  
		//getData
		$(".getData").click(function(){
			var a=$(this).parent().attr("class");
			var nub=a.substr(-1,1)*1+1;
			var nid=newsItemId[nub];
			var title=$("#title").val();
			var itemUrl=$("#itemUrl").val();
			var itemId=$("#itemId").val();
			location.href="<?php bloginfo('template_directory'); ?>/wechat/autorep/autoreply_image_insert.php?beIframe&netId="+nid;
			 		  
		  })
		  
		$(".ediData").click(function(){
			var a=$(this).parent().attr("class");
			var nub=a.substr(-1,1)*1+1;
			var nid=newsItemId[nub];
			var title=$("#title").val();
			var itemUrl=$("#itemUrl").val();
			var itemId=$("#itemId").val();
			
			window.showModalDialog('<?php echo get_template_directory_uri(); ?>/wechat/material/material_edit.php?beIframe&netId='+nid,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')			
			
			
			var url="<?php echo get_template_directory_uri(); ?>";
			
			window.location.href=url+'/wechat/autorep/autoreply_image_select.php?beIframe&artType=post';
			
			 		  
		  })
		  
		  
		$(".selData").click(function(){					
			//opener.location.href='<?php echo get_template_directory_uri(); ?>/wechat/autorep/autoreply.php?beIframe&tab=1';
			var a=$(this).parent().attr("class");
			var nub=a.substr(-1,1)*1+1;
			var nid=newsItemId[nub];
			var title=$("#title").val();
			var itemUrl=$("#itemUrl").val();
			var itemId=$("#itemId").val();
			setTimeout("self.close()", 1000);
			opener.location.href='<?php echo get_template_directory_uri(); ?>/wechat/autorep/autoreply.php?beIframe&isselcet=1&tab=1&newsid='+nid;			
		})
		
		
		  
		$("#nav ul li").click(function(e){
			
			$("#nav ul li.selected").removeClass("selected");
			$(this).addClass("selected");
			
			
			})
			
			$(".pic").hide();
		$("#nav ul li:first-child").click(function(){
			$(".main_bd").fadeOut();
			$(".pic").fadeIn();
		})
		$("#nav ul li:last-child").click(function(){
			$(".pic").fadeOut();
			$(".main_bd").fadeIn();
			
		})
			
	

		</script>
			
			
		
		
		
	</body>
</html>