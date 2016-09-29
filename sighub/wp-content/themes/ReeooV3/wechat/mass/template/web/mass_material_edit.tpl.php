<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js?t=123" type="text/javascript"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/upload.css">
	<style>
		#nav-main{padding-right:66px;margin-bottom:10px;}
	</style>
	<body>
		<div id="maintest" class="main">			
			<div class="main-title">
				<div class="title-1">当前位置：素材管理> <font class="fontpurple">素材编辑 </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<div id="nav-main">
				<ul class="nav nav-pills">
				</ul>				
				<div>
				    <div style="float:right;">
						<input class="newsadd btn btn-primary" type="button"  value="保存"style="width:75px;height:32px;"/>
						<input class="btnlast btn-default" type="button" value="取消" onclick="returnlast();" style="margin-left:5px; width:75px;height:32px;position: relative;overflow: hidden;margin-right: 4px;display:inline-block;*display:inline;padding:4px 10px 4px;font-size:14px;color:gray;line-height:18px;*line-height:20px;text-align:center;vertical-align:middle;cursor:pointer;border:1px solid #cccccc;border-bottom-color:#b3b3b3;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;">
					</div>
					<table width="250" height="50" border="0" cellpadding="10px" style="margin-left:0px; margin-top:8px;" id="table2">			
						<tr>
							<td width="90"><label for="name">图文名称: </label></td>
							<td width="160"><input type="text" value=""class="form-control" id="material_name" name="news_name" /></td>
							<td></td>
						</tr>							
					</table>
			    </div>
			</div>			
			<div class="main_bd" >			
				<div class="left" style=" margin-left: 0%; ">
					<div  id="pre-title0">
						<div  myurl="" class="pre-bg" >
							<p>封面图片(建议尺寸300*200像素)</p>							
						</div>
						 <span class="title1">标题</span> 
						 <input class="newsUrl" type="text" style="display:none" value=""/>
						 <input class="newsDes" type="text" style="display:none" value=""/>
						 <input class="newsId" type="text"style="display:none"  value=""/>
					</div><!--封面-->
					<div class="pre-title" id="pre-titles" >
						<span class="title1" >标题</span>
							<input class="newsUrl" type="text" style="display:none" value=""/>
							<input class="newsDes" type="text" style="display:none" value=""/>
							<input class="newsId" type="text"style="display:none"  value=""/>
						<div > <span >缩略图<br/>(100*100)</span> </div>
						<a href="javascript:void(0)" style="left:20px;">删除</a>
					</div><!--标题-->
					<div id="pre-add"><a href="javascript:void(0)"><span class="glyphicon glyphicon-plus" style="font-size:20px;"></span></a><br><font size="2px">(建议不超过5个图文信息)</font></div>

				</div><!--left-->

				<div class="right" style="background: rgb(250, 250, 250);">
					<form>
						<div class="border"></div>
						<label for="title">标题</label>
						<input id="title"  type="text" name="title"  class="form-control" oninput="textPreview()" onfocus="javascript:if(this.value=='请输入标题'||this.value=='标题')this.value='';"/>
						<label>封面</label>					

						<div class="upload"> 
							<div class="row">
								<div class="col-xs-6 col-md-3">
									<a href="#" class="thumbnail">
										<img id="pic" src="" alt="图片预览" />
									</a>
								</div>
							</div>
							<div class="btn">
								<span>添加附件</span>
								<input id="fileupload" type="file" name="file">
							</div>
							<div class="progress">
								<span class="bar"></span><span class="percent">0%</span >
							</div>
							<div class="files"></div>
							<div id="showimg"></div>							
						</div>											
						<label for="content">正文</label>
						<textarea id="itemDes" name="desc"  class="form-control" style="width:390px;height:100px;margin-bottom:30px" oninput="textPreview()"></textarea>
						
						<!--添加外联-->
						<label for="content">添加链接:</label>
						
						<input type="radio" name="itmesUrl" value="1" onclick="disableOut()">
						<span> 添加外链（请以http://或https://开头）</span>
						<input id="sourl" type="text" class="form-control" name="itemoUrl" value="" onchange="textPreview()" onpropertychange="textPreview()" oninput="textPreview()" style="margin-bottom:5px;"/>						
						<!--添加内联-->
						<input type="radio" name="itmesUrl" value="0" checked="checked" onclick="disableIn()">
						
						<input id="sitebutton" type="button" onClick="selectSite()" class="btn btn-xs btn-primary" id="menu" value="点击选择微官网站点" style="width:180px;margin-bottom:5px;" />
						<input id="siurl" class="form-control" type='text' style="margin-bottom:5px;" name='itemiUrl' value='' onchange="textPreview()" onpropertychange="textPreview()" oninput="textPreview()" onKeyDown="textPreview()" readonly>						
						
						<input id="itemId" type="text" name="itemId" style="display:none" />
						<input id="netId" type="text" name="netId" value="" style="display:none" />
						<br/>
						
					</form>
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
		
		
		var itemId_save=new Array();
		var itemTitle_save=new Array();
		var picUrl_save=new Array();
		var itemUrl_save=new Array();
		var itemDes_save=new Array();
		var newsId_save=new Array();
		var itid=0;	
		var materialname="";
		
		var itemTitle=new Array();
		var picUrl=new Array();
		var itemId=new Array();
		var itemUrl=new Array();
		var itemDes=new Array();
		
		<?php
		
			$i=0;
			$j=0;
			$k=0;
			$v=0;
			$d=0;
			$o=0;
			$netId=$_GET["netId"];
			
			$materials = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wechat_material_news where news_item_id=".intval($netId)." order by news_id");
			
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
				echo "itemTitle[".$i++."]=\"".$material->news_item_title."\";\n";		
				echo "picUrl[".$j++."]=\"".$newsitempicurl."\";\n";
				echo "itemId[".$k++."]=\"".$material->news_id."\";\n";
				echo "itemUrl[".$v++."]=\"".$newsitemurl."\";\n";
				echo "itemDes[".$d++."]=\"".$material->news_item_description."\";\n";
				echo "materialname=\"".$material->news_name."\";\n";
			}
		
			$newsCount = $wpdb->get_var("SELECT COUNT(*)FROM {$wpdb->prefix}wechat_material_news where news_item_id=".intval($netId));
		?>

	$(document).ready(function(e) {
		var i=0;
		var n="<?php echo $newsCount ?>";
		$("#material_name").val(materialname);
				
		//显示
		if(n<2){$("#pre-titles").css("display","none");}
		if(n>=2){
			for(var j=1;j<n;j++){	
				var $add=$("#pre-titles").clone("deep");
			    var changeId="pre-title"+j;
			    $add.attr("id",changeId);
			    $("#pre-add").before($add);			  
			}
			$("#pre-titles").css("display","none");
		}
		
				
		//读数据		
	    for(var k=0;k<n;k++){	
			var changeId="pre-title"+k;
			var bgid="#"+changeId+">div";
			var textid="#"+changeId+" span.title1";
			var newsUrl="#"+changeId+" input.newsUrl";
			var newsDes="#"+changeId+" input.newsDes";
			var newsId="#"+changeId+" input.newsId";
					
		  
			var url="url("+picUrl[k]+")";
			var text=itemTitle[k];
			var news_Url=itemUrl[k];
			var news_Des=itemDes[k];
			var news_Id=itemId[k];
			
			//alert(picUrl[k]);
			if(picUrl[k]!="")
			{ 
			    $(bgid+" span").css("z-index","-1");
			}
			else{
			    $(bgid+" span").css("z-index","1");
			}
			
			$(newsUrl).val(news_Url);
			
			$(newsDes).val(news_Des);
			$(newsId).val(news_Id);	
				
			
			$(bgid).css("background",url);
			$(bgid).css("background-size","100% 100%");
			//显示时，给添加myurl一个url的值，为了给右边对应赋值显示
			$(bgid).attr("myurl",picUrl[k]);
			$(textid).text(text);		  
		 
		}
		
		//滑动右栏
		$(".left>div:not(#pre-add)").click(function(e) {
			var x=e.X;
			var y=e.pageY;
			var netId=<?php echo $netId ?>;
			$("#netId").val(netId);
			//循环显示时左边已经复制，拿到左边的值
			var tt=$(this).children("span.title1").text();
			var nu=$(this).children("input.newsUrl").val();
			var nd=$(this).children("input.newsDes").val();
			var ni=$(this).children("input.newsId").val();  //news_id
			
			
			//这是拿到图片的url
			var pirl=$(this).children("div").attr("myurl");
			
			
			//依次给右边对应赋值显示
			$("#title").val(tt);
			var need='<?php echo $needle."/?site" ?>';
			if(nu.indexOf(need)>=0){				
				$("#siurl").css("visibility","visible")
				$('input[type="radio"][value="0"]').attr("checked","checked");
				document.getElementById("sourl").disabled=true;
				document.getElementById("siurl").disabled=false;
				document.getElementById("sitebutton").disabled=false;
				$("#siurl").val(nu);
				$("#sourl").val("");
			}else{
				$('input[type="radio"][value="1"]').attr("checked","checked");
				document.getElementById("sourl").disabled=false;
				document.getElementById("siurl").disabled=true;
				document.getElementById("sitebutton").disabled=true;
				$("#sourl").val(nu);
				$("#siurl").val("");
				
				
			}
			//$("#itemUrl").val(nu);
			var reg=new RegExp("<br/>","g");
			nd= nd.replace(reg,"\r\n");
			$("#itemDes").val(nd);
			$("#itemId").val(ni);
			//给右边图片显示内容
			if(pirl!=""){
				$("#pic").show(); 
				$("#pic").attr("src",pirl); 
			}
			else
			    $("#pic").hide();

			$(".right .border").animate({top:y});
				
		});
		//滑动右栏
		//如果n=0，没有多图文，从pretitle1开始，如果不等于0，则从多图文个数-1开始
		if(n==0){
			adnew=0;
		}else{
			var adnew=n-1;
		}
		$("#pre-add>a").click(function(e) {
			adnew=adnew+1;
			itid=itid-1;
			$("#title").val("请输入标题");
			//$("#itemUrl").val("");
			$("#siurl").val("");
			$("#sourl").val("");
			//$("#memurl").val("");
			$("#itemDes").val("");
			$("#pic").attr("src","");
			$("#pic").hide();
			var progress = $(".progress");
			progress.hide();
			//$(".files").remove();
			$("#itemId").val(itid);
			var netId=<?php echo $netId ?>;
			$("#netId").val(netId);
			var $add=$("#pre-titles").clone("deep");
			$add.css("display","block");
			$add.css("height", "102px");
			var changeId="pre-title"+adnew;
			$("#pre-add").before($add);
			$add.attr("id",changeId);
			
			$(changeId).attr("id",changeId);
			
			var bgid="#"+changeId+">div";
			var textid="#"+changeId+">span:first-child";
			var newsUrl="#"+changeId+">input.newsUrl";
			var newsDes="#"+changeId+">input.newsDes";
			var newsId="#"+changeId+">input.newsId";
			
			$(bgid).css("background-image","");
			$(bgid+" span").css("z-index","1");
			$(bgid).attr("myurl","");
			
			var text="请输入标题"
			$(textid).text(text)
			$(newsUrl).val("");
			$(newsDes).val("");
			$(newsId).val(itid);
			
			
		  
			i++;			
			var y=e.pageY+30;
				
			$(".right .border").animate({top:y});
			
			//添加新的图文默认选中外链
			var radUrl=document.getElementsByName("itmesUrl");
			for(var i=0;i<radUrl.length;i++){
				if(radUrl[i].value==1){
					 radUrl[i].click();
				}
			}			

		});

		//delete
		$(".pre-title>a").click(function(e){			
			//$(this).parent().trigger("click");
			if(($(this).parent().prevAll().length)!=2){
				$(this).parent().prev().trigger("click");
				
			}else{
				$("#pre-title0").click();
				
			}
			
			var y=e.pageY-140;
			$(".right .border").animate({top:y});
			var event = window.event || arguments.callee.caller.arguments[0];			
			$(event.target.parentNode).remove();			
			var newsId=$("#itemId").val();
			var title=$("#title").val();			
			
		})		
			
	});
			
		//getData		
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
				
		//预览
		$(document).ready(function(e){
			$("#pic").hide();
			
			//实现用户编辑素材的页面上显示的是第一个图文的信息
			$("#pre-title0").click();
		})
	
		function reset_file(){
		//$("#picpath").val("");
			document.getElementById("picpath").value="";
		}
		
		
		function returnlast(){
			var maitemsvnid=<?php echo $netId ?>;
			var mamassid='<?php echo $massid ?>';
			window.location.href='<?php echo $this->createWebUrl('selectNews',array());?>'+'&massid='+mamassid+'&selectnewsid='+maitemsvnid;
		}
		
		function previewImage(file){  	
			var picsrc = document.getElementById('pic');  
			if (file.files && file.files[0]){ //chrome 
					var reader = new FileReader();
						reader.readAsDataURL(file.files[0]);  
						reader.onload = function(ev){
							picsrc.src = ev.target.result;
							$("#pic").show();
							for(var i=0;i<$(".newsId").length;i++){	
								if($("#pre-title"+i+">.newsId").val()==$("#itemId").val()){	
									$("#pre-title"+i+">div").css({"background-image":"url("+ev.target.result+")","background-size":"100% 100%"});
									$("#pre-title"+i+">div span").css("z-index","-1")
									break;
								}
							}	
						}   																		
			}else{
				//IE下，使用滤镜 出现问题
				picsrc.style.maxwidth="50px";
				picsrc.style.maxheight = "12px";
				picsrc.style.overflow="hidden";
				var picUpload = document.getElementById('picpath'); 
				picUpload.select();
				var imgSrc = document.selection.createRange().text;  
				picsrc.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
				picsrc.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\""+imgSrc+"\")";		
			}                    
		}  
		
				
		$(".newsadd").click(function(e){		    
			//添加图文名称
			var news_name = $("#material_name").val();			
			var itemId_temp="";
			var itemTitle_temp="";
			var itemDes_temp="";
			var itemUrl_temp="";
			var picUrl_temp="";
			
			
			var itemsvnid=<?php echo $netId ?>;
			for(var i=0;i<$("div[id^=pre-title]").length;i++){
				if(("#"+$("div[id^=pre-title]")[i].id)!="#pre-titles"){
					var itemsvid=$("#"+$("div[id^=pre-title]")[i].id+">.newsId").val();
					var itemsvtie=$("#"+$("div[id^=pre-title]")[i].id+" span.title1").text();
					var itemsvdes=$("#"+$("div[id^=pre-title]")[i].id+" input.newsDes").val();
					var itemsvurl=$("#"+$("div[id^=pre-title]")[i].id+" input.newsUrl").val();
					var picsurl=$("#"+$("div[id^=pre-title]")[i].id+" div").attr("myurl");
					
					
					itemId_save[i]=itemsvid;
					itemTitle_save[i]=itemsvtie;
					itemDes_save[i]=itemsvdes;
					itemUrl_save[i]=itemsvurl;
					picUrl_save[i]=picsurl;
					
					itemId_temp=itemId_temp+itemsvid+'|';
					itemTitle_temp=itemTitle_temp+itemsvtie+'|';
					itemDes_temp=itemDes_temp+itemsvdes+'|';
					itemUrl_temp=itemUrl_temp+itemsvurl+'|';
					picUrl_temp=picUrl_temp+picsurl+'|';
					
				}
			}	
			if(news_name==""){
				alert("请输入图文名称！");
			}else{
				var massid='<?php echo $massid ?>';
			$.ajax({
					type:'post',
					url:window.location.href, 
					//"<?php echo get_template_directory_uri(); ?>/wechat/material/material_upall.php?beIframe&itemId_save="+itemId_temp+"&itemTitle_save="+itemTitle_temp+"&itemDes_save="+itemDes_temp+"&itemUrl_save="+encodeURIComponent(itemUrl_temp)+"&newsId_save="+itemsvnid+"&material_name="+news_name+"&picUrl_save="+picUrl_temp,
					data:{'massnewsadd':'isadd','beIframe':'','itemId_save':itemId_temp,'itemTitle_save':itemTitle_temp,'itemDes_save':itemDes_temp,'itemUrl_save':itemUrl_temp,'newsId_save':itemsvnid,'material_name':news_name,'picUrl_save':picUrl_temp},
					success: function(data){
						if (data.status == 'success'){
							alert(data.message);
						}
						window.location.href='<?php echo $this->createWebUrl('selectNews',array());?>'+'&massid='+massid+'&selectnewsid='+itemsvnid;
					},
					error: function(data){
							alert("出现错误");
					},
					dataType: 'json'					
			});				
		}})	
		
		function textPreview(){	
			var newTitle=document.getElementById("title");
			var newDes=document.getElementById("itemDes");
			var newckUrl=document.getElementsByName("itmesUrl");
			var newUrl;
			var val;
			for(var i=0;i<newckUrl.length;i++){
				if(newckUrl[i].checked){
					 val=newckUrl[i].value;
				}
			}
			if(val==0){
				newUrl=document.getElementById("siurl");	
			}else if(val==1){
				newUrl=document.getElementById("sourl");
			}
			
			
			var ntext=newTitle.value;
			var nDes=newDes.value;						
			nDes =  nDes.replace(/\n/g,'<br/>');
			var nUrl=newUrl.value;
			for(var i=0;i<$("div[id^=pre-title]").length;i++){
				if($("#"+$("div[id^=pre-title]")[i].id+">.newsId").val()==$("#itemId").val()){	
					$("#"+$("div[id^=pre-title]")[i].id+" span.title1").text(ntext);
					$("#"+$("div[id^=pre-title]")[i].id+" input.newsDes").val(nDes);
					$("#"+$("div[id^=pre-title]")[i].id+" input.newsUrl").val(nUrl);
					break;
				}			
			}
			
		} 
		function disableOut() {
			document.getElementById("sourl").disabled=false;
			document.getElementById("siurl").disabled=true;
			document.getElementById("sitebutton").disabled=true;
		}
		function disableIn() {
			document.getElementById("sourl").disabled=true;
			document.getElementById("siurl").disabled=false;
			document.getElementById("sitebutton").disabled=false;
		}
		function selectSite(){	
			var siteurl = document.getElementById("siurl").value;
			
			var sidsel=siteurl.split("?site=")[1];			
			
			window.open('wp-content/themes/ReeooV3/wechat/common/wesite_list.php?beIframe&sidsel='+sidsel,'_blank','height=520,width=800,top=120,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no')
		
		}
		
	
		$(function () {
			$("#fileupload").wrap("<form id='myupload' method='post' enctype='multipart/form-data'></form>");
			var bar = $('.bar');
			var percent = $('.percent');
			var showimg = $('#showimg');
			var progress = $(".progress");
			var files = $(".files");
			var btn = $(".btn span");
			
			$("#fileupload").change(function(){
			//$("#fileupload").live('change',function(){
				$("#myupload").ajaxSubmit({
					dataType:  'text',
					url:'<?php echo $this->createWebUrl('massmaterialupload',array());?>',
					beforeSend: function() {
						showimg.empty();
						progress.show();
						var percentVal = '0%';
						bar.width(percentVal);
						percent.html(percentVal);
						btn.html("上传中...");
					},
					uploadProgress: function(event, position, total, percentComplete) {
						var percentVal = percentComplete + '%';
						bar.width(percentVal);
						percent.html(percentVal);
					},
					success: function(data) {
					//dataType改成text，再对json字符串解析成js对象，就不直接用json的了
						var obj = jQuery.parseJSON(data);				
						//files.html("<b>"+obj.name+"("+obj.size+"k)</b> <span class='delimg' rel='"+obj.pic+"'>删除</span>");
						var img = obj.pic;
						//showimg.html("<img src='"+img+"'>");
						$("#pic").show(); 
						$("#pic").attr("src",img); 
						btn.html("添加附件");
						
						for(var i=0;i<$("div[id^=pre-title]").length;i++){
							if($("#"+$("div[id^=pre-title]")[i].id+">.newsId").val()==$("#itemId").val()){
								$("#"+$("div[id^=pre-title]")[i].id+" div").attr("myurl",obj.pic);
								$("#"+$("div[id^=pre-title]")[i].id+" div").css({"background-image":"url("+obj.pic+")","background-size":"100% 100%"});
								$("#"+$("div[id^=pre-title]")[i].id+" div span").css("z-index","-1")
								break;
							}
						}					
						$("#fileupload").attr("value","");
					},
					error:function(xhr){
						btn.html("上传失败");
						bar.width('0')
						files.html(xhr.responseText);
						$("#fileupload").attr("value","");
					}
				});return false;
			});
			
			$(".delimg").on('click',function(){
				var pic = $(this).attr("rel");
				$.post("material_upload.php?act=delimg",{imagename:pic},function(msg){
					if(msg==1){
						files.html("删除成功.");
						progress.hide();
					}else{
						alert(msg);
					}
				});
			});
		});
</script>
	</body>
</html>