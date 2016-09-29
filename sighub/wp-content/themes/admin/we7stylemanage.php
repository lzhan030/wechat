<?php

if(isset($_GET['del']) && !empty($_GET['del']) ){
	require_once './wp-admin/includes/user.php';
	wp_delete_user( $_GET['del'] );
	}
	
global $wpdb;
if(isset($_GET['id'])&&isset($_GET['activate'])){
	if($_GET['type']=="we7style"){
	$wpdb->query("UPDATE ".$wpdb->prefix."site_templates SET activate=".$_GET['activate']." WHERE id=".$_GET['id']);
	}
	else{
	$wpdb->query("UPDATE ".$wpdb->prefix."theme SET activate=".$_GET['activate']." WHERE id=".$_GET['id']);
	}
}
	$we7result = $wpdb->get_results("SELECT * from ".$wpdb->prefix."site_templates");
	$orgresult=$wpdb->get_results("SELECT * from ".$wpdb->prefix."theme");
	?>
<style>
.table{width:85%;margin-top:30px;margin-left:30px;text-align:center;}
.addnew{text-align:right; width: 85%;margin-left:30px;}
</style>
<div class="main-title">
	<div class="title-1">当前位置：模板风格管理 > <font class="fontpurple">模板风格列表 </font>
	</div>
</div>
<div class="addnew">
	<button class="btn btn-primary" onclick="location.href='?admin&page=wesitetemplateadddlg'">添加新微官网模板</button>
</div>
<div>
	<table class="table table-striped"  border="1">
	<tbody>
		<tr>
			<td scope="col" width="150"  style="font-weight:bold">模板风格</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">模板名称</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">激活</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">模板下载</td>
			<td scope="col" width="100" align="center" style="font-weight:bold">操作</td>
		</tr>
		<?php 		
		$upload =wp_upload_dir();
		foreach ($orgresult as $orgstylelist) {
			$id=$orgstylelist->id;
		if($id!=7){
			if(stristr($orgstylelist->picUrl,"http")!==false){
				$orgstylepicurl=$orgstylelist->picUrl;		
			}else{
				$orgstylepicurl=$upload['baseurl'].$orgstylelist->picUrl;
			}
		?>
		<tr>
			<td align="center"><img src="<?php echo home_url()?>/wp-content/themes/ReeooV3/images/mv<?php echo $id ?>.png" width="145px" height="225px" />
			<td align="center"><?php echo $orgstylelist->themename; ?> </td>
			<td align="center">
			    <input type="radio" onclick="location.href='?admin&page=we7stylemanage&id=<?php echo $orgstylelist->id; ?>&activate=1&type=original'" name="<?php echo $orgstylelist->themename; ?>" value="1" <?php if($orgstylelist->activate == 1) echo 'checked="checked"'; ?>/>启用
				<input type="radio" onclick="location.href='?admin&page=we7stylemanage&id=<?php echo $orgstylelist->id; ?>&activate=0&type=original'" name="<?php echo $orgstylelist->themename; ?>" value="0" <?php if($orgstylelist->activate == 0) echo 'checked="checked"'; ?>/>禁用
			</td>
			<td><button class="btn btn-success btn-sm" disabled="disabled">无模板下载</button></td>
			<td><button class="btn btn-success btn-sm" disabled="disabled">系统模板不能编辑</button></td>
		</tr>
		<?php
		}
		}
        ?>
		<?php 		
		foreach ($we7result as $we7stylelist) {
		?>
		<?php if($we7stylelist->removed == 0){?>
		<tr>
			<td align="center"><img src="<?php echo home_url()?>/wp-content/themes/mobilepagewe7/template/<?php echo $we7stylelist->name;?>/preview.jpg" width="145px" height="225px" /> </td>
			<td align="center"><?php echo $we7stylelist->title; ?> </td>
			<td align="center">
			    <input type="radio" onclick="location.href='?admin&page=we7stylemanage&id=<?php echo $we7stylelist->id; ?>&activate=1&type=we7style'" name="<?php echo $we7stylelist->title; ?>" value="1" <?php if($we7stylelist->activate == 1) echo 'checked="checked"'; ?>/>启用
				<input type="radio" onclick="location.href='?admin&page=we7stylemanage&id=<?php echo $we7stylelist->id; ?>&activate=0&type=we7style'" name="<?php echo $we7stylelist->title; ?>" value="0" <?php if($we7stylelist->activate == 0) echo 'checked="checked"'; ?>/>禁用
			</td>
			<td><button class="btn btn-success btn-sm" onclick="location.href='?admin&page=wesite_template_download&id=<?php echo $we7stylelist->id;?>'">点击下载模板</button></td>
			<td>
				<?php if($we7stylelist->id <= 17) {?>
				<button class="btn btn-success btn-sm" disabled="disabled">系统模板不能编辑</button>
				<?php } else {?>
				<button class="btn btn-warning btn-sm" onclick="deletetemplate(<?php echo $we7stylelist->id;?>);">删除</button>
				<button class="btn btn-info btn-sm" onclick="location.href='?admin&page=wesitetemplateupdatedlg&id='+<?php echo $we7stylelist->id;?>">编辑</button>
				<?php } ?>
			</td>
		</tr>
		<?php
		}}
        ?>
	</tbody>
</table>
</div>

<script type="text/javascript">

    var xmlHttp;
	function createXMLHttpRequest(){
		if(window.ActiveXObject)
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		else if(window.XMLHttpRequest)
			xmlHttp = new XMLHttpRequest();
	}

    function deletetemplate(id){
		if(confirm("是否确定删除该模板？")) {
			createXMLHttpRequest();
			xmlHttp.open("GET","?admin&page=wesite_template_delete&header=0&footer=0&id="+id,true);
			xmlHttp.onreadystatechange = function(){
				if(xmlHttp.readyState == 4 && xmlHttp.status == 200){
					alert(xmlHttp.responseText);
				    window.location.reload();
				}
			}
			xmlHttp.send(null);
		}
   }
</script>