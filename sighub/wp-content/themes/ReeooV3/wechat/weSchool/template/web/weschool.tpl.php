<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php
//20140624 janeen update
//$weid=$_SESSION['WEID'];
$gweid=$_SESSION['GWEID'];
//end
?>
<?php
    if(isset($_GPC['bt']) && !empty($_GPC['bt']))
    {
        $buttonClick = $_GPC['bt'];
        $isIframe = 'true';
        if($buttonClick == 'personManage')
            $src = $this->createWebUrl('personmanage',array('id' => 3));
        elseif($buttonClick == 'wechatVideo')
            $src = $this->createWebUrl('videomanage',array('id' => 3));
        elseif($buttonClick == 'wechatHomework')
            $src = $this->createWebUrl('homeworkmanage',array('id' => 3));
		elseif($buttonClick == 'wechatNotice')
            $src = $this->createWebUrl('noticemanage',array('id' => 3));
        else
            echo 'not finish OR error：404';
        if($isIframe = 'true')
        {
        ?>
        <iframe src="<?php echo $src;?>" id="iframepage" name="iframepage" frameBorder=0 scrolling=no width="92%" onLoad="iFrameHeight()" height="900"></iframe>
        <script type="text/javascript" language="javascript">
            function iFrameHeight() {
                var ifm= document.getElementById("iframepage");
                var subWeb = document.frames ? document.frames["iframepage"].document : ifm.contentDocument;
                if(ifm != null && subWeb != null) {
                    ifm.height = subWeb.body.scrollHeight;
                }
             }
        </script>
		
		
		
    <?php
        }
    }
    else{
    ?>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/button.css" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery1.83.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/bootstrap-tab.js"></script>
    <script>
	   $(function(){
			$('.nav-tabs a:first').tab('show');
			//$('#tabselect li:eq(2) a').tab('show');   //显示第几个tab
			//可以使用下面的进行tab的切换
			$('.nav-tabs a:last').click(function (e) { 

			    //e.preventDefault();//阻止a链接的跳转行为 
                //$("#weschooliframe").attr("src","<?php echo $this->createWebUrl('Noticemanage',array('id' => 3));?>");
			}) 


			
			//点击学生管理页面还在该tab下显示而不是跳转到新的页面
            /* $("#studentmanage").bind("click",function(e){
		
				e=e||window.event
				e.preventDefault();
				$("#personmanage").load($(this).attr("href"));

			});
			
			//点击教师管理页面还在该tab下显示而不是跳转到新的页面
			$("#teachermanage").bind("click",function(e){
		
				e=e||window.event
				e.preventDefault();
				$("#personmanage").load($(this).attr("href"));

			}); */
			
			 $(".personentry").live("click",function(e){
	
		       
				e=e||window.event
				e.preventDefault();
				
				if($(this).attr("href")!=undefined){
					url=$(this).attr("href");
					
				}else{
					url=$(this).attr("title");
					
				}
				//chuanxia
				//console.log($("#personmanage").length);
				alert(url);
				try{
					$("#personmanage").load(url);
				}catch(e){
					console.log(e.message);
				}
				
			
			}); 
			
			
		});
		
		function switab(str)
		{
		    //alert(str);
			if(str == "firstpagemade")
			{
			     $("#weschooliframe").attr("src","<?php echo $this->createWebUrl('createIndex',array('gweid' => $gweid));?>");
			}
			else if(str == "personmanage")
			{
			     $("#weschooliframe").attr("src","<?php echo $this->createWebUrl('personmanage',array('id' => 3));?>");
			}
			else if(str == "picvideo")
			{
			     $("#weschooliframe").attr("src","<?php echo $this->createWebUrl('Videomanage',array('id' => 3));?>");
			}
			else if(str == "homework")
			{
			     $("#weschooliframe").attr("src","<?php echo $this->createWebUrl('Homeworkmanage',array('id' => 3));?>");
			}
			else if(str == "notice")
			{
			    $("#weschooliframe").attr("src","<?php echo $this->createWebUrl('Noticemanage',array('id' => 3));?>");
			}
		}

	</script>

 
	
	<div class="main-titlenew" style="margin-bottom:2%;margin-left:30px;">
		<div class="title-1">当前位置：微学校 
		<!--<font class="fontpurple">作业列表</font>-->
		</div>
	</div>
		
	<article>	
		<article>
			<p><a href="javascript:;" onclick="config()" id="setlink" data-toggle="modal" data-target="#myModal"style="text-decoration:none;"><span class="glyphicon glyphicon-cog"></span>设置</a></p>
		</article>
	</article>
	
	<div style="margin-left:30px;">
		<ul class="nav nav-tabs" id="tabselect">
			<li class="active selected"><a href="#firstpagemade" onclick="switab('firstpagemade')" data-toggle="tab" >微学校首页设置</a></li>
			<li><a href="#personmanage" onclick="switab('personmanage')" data-toggle="tab" >人员管理</a></li>
			<li><a href="#picvideo" onclick="switab('picvideo')" data-toggle="tab" >视频和照片</a></li>
			<li><a href="#homework" onclick="switab('homework')" data-toggle="tab" >作业</a></li>
			<li><a href="#notice" onclick="switab('notice')" data-toggle="tab">公告栏</a></li>
		</ul>
	</div>
	
	<div class="tab-content">
	     <iframe frameborder="0" id="weschooliframe" src="<?php echo $this->createWebUrl('createIndex',array('gweid' => $GWEID));?>" width="100%" height="900" scrolling="no"></iframe>
	</div>
	

<script language='javascript'>
	function config(){
		window.open('<?php echo $this->createWebUrl('weschool_custom_dlg',array());?>','_blank','height=400,width=800,top=150,left=300,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		xmlHttp.onreadystatechange = function(){
			window.location.reload();
		}
	}
	
</script>

<?php   }
    get_footer();
 ?>