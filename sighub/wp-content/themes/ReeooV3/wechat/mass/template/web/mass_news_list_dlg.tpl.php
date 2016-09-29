<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
	<body>
		<div style="padding:0 30px">
		<form>
			<div class="main-title">
				<div class="title-1"><font class="fontpurple">图文列表： </font>
				</div>
			</div>
			<div class="bgimg"></div>
			<input class="btn btn-primary" type="button" value="创建新图文" onclick="createNews()" ></input>
			<div class="submenu">
				<div class="panel panel-default" style="margin-top:10px;margin-bottom:10px">
					<div class="panel-heading">已建图文列表</div>
					<table class="table table-striped" width="800"  border="1" align="center">
						<tr>
							<td align="center" style="font-weight:bold"></td>
							<td align="center" style="font-weight:bold">编号</td>
							<td align="center" style="font-weight:bold">素材名称</td>
							<td align="center" style="font-weight:bold">操作</td>
						</tr>
						<?php
						if(is_array($news) && !empty($news)){
					  
						foreach($news as $ns){
						    $check= $selectnewsid == $ns['news_item_id'] ? "checked='checked'" : '';
						?>	
							<tr> 							
							<td width=50 style='text-align:center'>
								<input type='radio' <?php echo $check; ?> id='myCheck' name='inputCheckBox' value='<?php echo $ns["news_item_id"]?>' />
							</td>					
							<td><?php echo $ns['news_item_id'];?></td>
							<td><?php echo $ns['news_name'];?></td>
							<td style='text-align:center'> 
								<input type='button' class='btn btn-sm btn-warning' onclick="editMaterial('<?php echo $ns["news_item_id"] ?>')"  value='编辑'> </td>
							</tr>
						<?php }}
						?>
					</table>
				</div>
				<div style="text-align:center;margin-bottom:10px">
					<input class=" btn btn-primary btmtxtbtn" type="button" value="保存" onclick="OK()"/>
					<input class="btn btn-default btmtxtbtn" type="button" value="取消" onclick="Cancle()"/>
				</div>
			</div>
		</form>
			<?php echo $pager;?>
		</div>
	</body>
<script language='javascript'>
	function OK(){
		var m=0;
		var massid='<?php echo $massid;?>';
		var massname="<?php echo $massname;?>";
		var aCheckBox=document.getElementsByName('inputCheckBox');

		for(var i=0; i<aCheckBox.length; i++){
			if(aCheckBox[i].getAttribute('type')=='radio'){
				if(aCheckBox[i].checked==true){
					var nid = aCheckBox[i].value;
					if (massid.length>0) {
						opener.location.href='<?php echo $this->createWebUrl('mass',array());?>'+'&tab=1&news_item_id='+nid+'&massid='+massid+'&fromflag=<?php echo $fromflag;?>';
					} else {
						opener.location.href='<?php echo $this->createWebUrl('mass',array());?>'+'&tab=1&news_item_id='+nid+'&massname='+massname+'&fromflag=<?php echo $fromflag;?>';
						
					}
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
	
	function createNews(){	
		var massid='<?php echo $massid;?>';
		location.href='<?php echo $this->createWebUrl('massmaterial',array());?>'+'&netId=0&massid='+massid+'&fromflag=<?php echo $fromflag;?>';
			
		//var url="<?php echo get_template_directory_uri(); ?>";
		//var newsel="<?php echo $selectnewsid; ?>";
		//window.location.href='<?php echo $this->createWebUrl('selectNews',array());?>'+'massid='+massid+'&selectnewsid='+newsel;
	}
	
	
	function editMaterial(nid){
		var massid='<?php echo $massid;?>';
		location.href='<?php echo $this->createWebUrl('massmaterial',array());?>'+'&netId='+nid+'&massid='+massid+'&fromflag=<?php echo $fromflag;?>';
		
		//window.showModalDialog('<?php echo get_template_directory_uri(); ?>/wechat/material/material_edit.php?beIframe&netId='+nid,'_blank','dialogWidth=880;dialogHeight=520;center=yes;scroll=yes;resizable=no;status=no')			
		//var newsel="<?php echo $selectnewsid; ?>";	
		//window.location.href='<?php echo $this->createWebUrl('selectNews',array());?>'+'massid='+massid+'&selectnewsid='+newsel;
	}
	
	
		
</script>
</html>