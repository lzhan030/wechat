<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php 
	$gweid=$_SESSION['GWEID'];
?>
<style type="text/css">
	.btn-sm{font-size:14px; width:120px}
	.btncenter{position: fixed; top: 84px; right: 86px;}
	.btncenter1{position:fixed; top:95px; right:120px;}
</style>
	<body>
	<div class="main_auto">
		<div class="main-title">
			<div class="title-1">当前位置：群发消息> <a href="<?php echo home_url().'/module.php?module=mass&do=masslist&fromflag='.$fromflag; ?>">群发管理</a>> <?php if(!isset($massid)){ ?> <font class="fontpurple">新建群发 </font>   <?php }else{ ?><font class="fontpurple">群发内容设置 </font><?php }?>
			</div>
		</div>
		<div class="submain">
			<?php if(!isset($massid)){ ?>
			<div class="key" style="width:70%">
				<label>请输入名称: </label>
				<input id='addmass' name='addmass' class='form-control' type='text' value='<?php echo $massname;?>' />							
			</div>
			 <?php }else{ ?>
			<div class="key" style="width:70%;margin-bottom:20px">
				<p style="font-weight:bold;margin-bottom: 5px;">群发名称: </p>
				<input id='addmass' class='form-control' type='text' value="<?php echo $mass['mass_name']; ?>" />
			</div>
			<?php } ?>
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
								<tr style="text-align:left;height:40px"><td>群发消息（若保存文本消息，则原有图文消息将会被自动删除）</td></tr>
								<tr>
									<td>
									<?php
									if ($flag==0) {
										echo "<textarea id='editor_id' class='editor' name='sendContent' cols='42' rows='11' style='width:95%;hight=215px'>";
										$textContent = stripslashes($masstext['text_content']);
                                        echo "{$textContent}</textarea>";
									}
									else {
										echo "<textarea id='editor_id' name='sendContent' class='editor' cols='42' rows='11' style='width:95%;hight=215px'></textarea>";
									}
									?>
									</td>
								</tr>
								<tr style="text-align:left;height:40px"><td>注意：文本要小于2048字节（对应682个中文汉字)</td></tr>
								<tr class="btncenter">
									<td>
										<input class="btn btn-success btn-sm btmtxtbtn" type="button" value="保存并群发消息" onclick="saveTxt(<?php echo $massid;?>)"/>
									</td>
								</tr>								
							</table>
						</div>
					</div>
					<div class="tab-pane" id="imgsTab">
						<table>
							<form action="" method="post">
							<tr style="height:40px;text-align:left"><td>群发消息（若保存图文消息，则原有文本消息将会被自动删除）</td></tr>
							<tr>
								<td>
									<div class="left" >
									<?php
									if ($flag==1 || $news_item_id!=null) {
										$i=0;
										$upload =wp_upload_dir();
										foreach ($massnews as $content) {
											if((empty($content['news_item_picurl']))||(stristr($content['news_item_picurl'],"http")!==false)){
												$newsitempic=$content['news_item_picurl'];
											}else{
												$newsitempic=$upload['baseurl'].$content['news_item_picurl'];
											}
											if ($i==0){
												echo "<div class='pre-title0'>";
												echo "<div class='pre-bg' style='background-image: url(".$newsitempic.");	 background-size:100% 100%'>" ;
												echo "<p>封面图片</p></div>";
												echo "<span class='title1'>".$content['news_item_title']."</span></div>";
												$i++;
											} else {
												echo "<div class='pre-title1 pre-title'><span class='title1' >".$content['news_item_title']."</span>";
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
								</td>
								<td style="vertical-align:top">
									<input type="button" onClick="selectNews('<?php echo $massid;?>','<?php echo $massmesg_id;?>')" class="btn btn-warning btmimgbtn" name="del" id="buttondel" value="选择多图文素材" style="width:140px"/> 													
								</td>
							</tr>
							<tr class="btncenter1">
								<td>
									<input style="margin-top:0px;" class=" btn btn-success btn-sm btmimgbtn" type="button"  value="保存并群发消息" onclick="saveNews('<?php echo $massid;?>','<?php echo $news_item_id;?>')"/>
								</td>
							</tr>
							</form>
						</table>
					</div>			
				</div>
			</div>
		</div>
		<!--display the wechat account of this group-->
		<?php if(($fromflag == 1) ){?>
			<?php if(count($getgroupaccounts) !=0 ){?>
			<div style="padding-bottom:60px;width: 95%;margin-top:10px;">
				<div class="bgimg_warning"></div>
				<div>
					<label for="name" style="margin-left:0px; margin-top:1%;">请选择群发公众号: </label>
				</div>
				<div style="margin-top: 1%; margin-left: 20px;" >
					<input type="checkbox" name="allChecked" onclick="check_all(this, 'accountCheck[]')" value="true" style="margin-right:10px;margin-left:10px;" checked="checked">全选/取消全选</input>
					<ul class="applist" style="padding-bottom: 60px;">
					<?php 
						//obtain the groupid
						foreach ($getgroupaccounts as $groupaccount) {				
						?>
							<li>
								<input type="checkbox" name="accountCheck[]" id="groupshare<?php echo $groupaccount['GWEID']?>" value="<?php echo $groupaccount['GWEID']?>" checked="checked" /> 
									<?php echo $groupaccount['wechat_nikename']?>
								</input>
							</li>
						<?php
						}?>
					</ul>
				</div>
			</div>
			<?php }else{ ?>
			<div class="alert alert-warning" style="padding-bottom:60px;width: 95%;margin-top:10px;">
				<div>
					<label for="name" style="margin-left:0px; margin-top:1%;">当前没有支持群发的公众号 </label>
				</div>
			</div>
			<?php }?>
		<?php }?>		
	</div>
	
	</body>
	<script language='javascript'>
		var a=0;
		$(document).ready(function(e) {
			//放在这个位置，包含autoclick，避免图文切换文本时editor不存在
			KindEditor.ready(function(K) {
				window.editor = K.create('#editor_id', {
				//items:["emoticons","link","unlink"],
				items:["link","unlink"],
				width:'95%',
				height:'215px'});
			});	
		
		});	
		//Tab selection function
		$(function() {
			$(".tab-pane").hide(); // hide all contents
			a= <?php echo $tab_ul;?>;
			if (a==1) {
				$("ul.nav li:last").addClass("active").show();
				$(".tab-pane:last").show();
			} else {
				$("ul.nav li:first").addClass("active").show();
				$(".tab-pane:first").show();
			}
            $('ul.nav li').click(function(){
				$("ul.nav li").removeClass("active"); // remove all active class
				$(this).addClass("active");
				$(".tab-pane").hide(); 
				
				var activeTab = $(this).find("a").attr("href"); // find the href attribute value to identify the active tab + content
				$(activeTab).fadeIn(); // Fade in the active ID content
			})

			//判断有多少已经群发成功的公众号,需要在更新页面显示出来
			<?php //if(($fromflag == 1) && isset($gweidarray) && !empty($gweidarray)){
				//for($i=0; $i<count($gweidarray); $i++){
			?>
				//$("#groupshare"+<?php echo $gweidarray[$i]?>).attr("checked","checked"); 
			<?php //} }?>

		})
		
		
		
		isSubmitting=false;
		
		
		//save new text msg
		function saveTxt(massid) {
			editor.sync();
			var massname=$("#addmass").val();
			if(massid==undefined){
				massid='';//表示新建
				if(massname=='' || editor.text().length<=0){
					alert("名称和文本内容均不能为空");
					return;
				}
			}else if(massname==''){
                alert("群发名称不能为空！请重新输入");
				return;
            }else if(editor.text().length<=0){
                alert("内容不能为空！请重新输入");
				return;
            }

            if(isSubmitting)
			return false;
			isSubmitting = true;
			$.ajax({
				url:window.location.href, 
				type: "POST",
				data:{'mass_txt':'istxt','massid':massid,'content':escape($('#editor_id').val()),'massname':massname,'fromflag':<?php if($fromflag == 1){echo "1"; }else{echo "0";}?>},
				success: function(data){
					if (data.status == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						isSubmitting = false;
						mass(data.massid);
						//alert(data.message);
					}
					isSubmitting = false;
				},
				 error: function(data){
					alert("出现错误");
					isSubmitting = false;
				},
				dataType: 'json'
			});			
			
		}
		
		function cancel() {
		    location.href="<?php echo home_url().'/module.php?module=mass&do=masslist&fromflag='.$fromflag; ?>";
		}
		
		//open img selection page
		function selectNews(massid,newsid){
			var massname=$("#addmass").val();
			window.open("<?php echo $this->createWebUrl('selectNews',array());?>"+"&massid="+massid+"&selectnewsid="+newsid+"&massname="+massname+"&fromflag=<?php echo $fromflag;?>","_blank","height=600,width=900,top=80,left=240,toolbar=no,menubar=no,scrollbars=yes,resizable=no,location=no,status=no,titlebar=no");
		}	

		//save img msg
		function saveNews(massid, news_item_id) {
			var massname=$("#addmass").val();
			if(massid.length<=0){
				if(massname=='' || news_item_id.length<=0){
					alert("名称及图文都不能为空！请重新输入");
					return;
				}
			
			}else if(massname==''){
				alert("群发名称不能为空！请重新输入");
				return;
			}else if(news_item_id.length<=0){
               
				<?php if($mass['mass_type'] == "1"){?>
					//原来的newsid
					news_item_id="<?php echo intval($mass['massmesg_id']) ?>";
				<?php }else{ ?>
					alert("图文不能为空！请重新选择");
					return;
				<?php } ?>
            }
					
			if(isSubmitting)
			return false;
			isSubmitting = true;
			$.ajax({
				url:window.location.href, 
				type: "POST",
				data:{'mass_news':'isnews','massid':massid,'news_item_id':news_item_id,'massname':massname,'fromflag':<?php if($fromflag == 1){echo "1"; }else{echo "0";}?>},
				success: function(data){
					if (data.status == 'error'){
						alert(data.message);
					}else if (data.status == 'success'){
						isSubmitting = false;
						mass(data.massid);
						//alert(data.message);
						//location.href='<?php echo $this->createWebUrl('mass',array());?>'+"&massid="+data.massid+"&<?php if($fromflag == 1){echo 'fromflag=1';}else{echo 'fromflag=0';}?>";
					}
					isSubmitting = false;
				},
				 error: function(data){
					alert("出现错误");
					isSubmitting = false;
				},
				dataType: 'json'
			});				
		}		
		
		function mass(massid) {
			if(massid.length<=0){
				alert("出现错误");			
			}else{		
				//获取页面上选中的公众号
	            <?php if($fromflag == 1){?>
		            var checkboxs = document.getElementsByName('accountCheck[]');
					var arr = [];
					var j = 0;
					for(var i=0;i<checkboxs.length;i++)
					{
						if(checkboxs[i].checked){
							arr[j] = checkboxs[i].value;
							j++;
						}
					}
					var accountstring = arr.join(",");
					if(accountstring.length == 0){
						alert("请先选择群发的公众号");
						return false;
					}
				<?php }?>
				
				if(isSubmitting)
				return false;
				isSubmitting = true;
				$.ajax({
					url:window.location.href, 
					type: "POST",
					data:{'mass':'ismass','massid':massid,'selectedaccount':<?php if($fromflag == 1){?>accountstring<?php }else{echo "'nselectedaccount'";}?>},
					success: function(data){
						if (data.status == 'error'){
							alert(data.message+"该消息已保存,您可以编辑重发");
							location.href='<?php echo $this->createWebUrl('masslist',array());?>'+"&<?php if($fromflag == 1){echo 'fromflag=1';}else{echo 'fromflag=0';}?>";
						}else if (data.status == 'success'){
							alert(data.message);
							location.href='<?php echo $this->createWebUrl('masslist',array());?>'+"&<?php if($fromflag == 1){echo 'fromflag=1';}else{echo 'fromflag=0';}?>";
						}
						isSubmitting = false;
					},
					error: function(data){
						alert("出现错误");
						isSubmitting = false;
					},
					dataType: 'json'
				});	
			}
		}	
		function check_all(obj,cName)
		{
			var checkboxs = document.getElementsByName(cName);
			for(var i=0;i<checkboxs.length;i++)
			{
				checkboxs[i].checked = obj.checked;
			}
		}	
	</script>	
</html>