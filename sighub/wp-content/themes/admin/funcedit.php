<?php
global $wpdb;
$funcid = $_GET['id'];


if( isset($_POST['func_name']) ){

   $func_id = $_GET['id'];
   $func_name = $_POST['func_name'];
   $func_status = $_POST['func_status'];
   $func_des = $_POST['description'];
   
   $wpdb->query( "UPDATE ".$wpdb->prefix."wechat_func_info SET func_name_zh = '".$func_name."' ,status = ".$func_status." , description = '".$func_des."' WHERE ID = ".$func_id." ;");
  
}
//echo $funcid;
$func = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wechat_func_info where ID=%s",$funcid));
?>
<div>
	<form id="funcedit" action="" method="post">
	
	<div class="main-title">
		<div class="title-1">当前位置：功能列表管理 > <font class="fontpurple">功能更新 </font>
		</div>
	</div>
	<div class="bgimg"></div>
	<?php
		if( isset($_POST['func_name']) ){
		?>
		<p style="background-color:#ffffe0;border-color:#e6db55;width: 80px;font-size: 18px;margin-left: 230px;">	提交成功!<br>
		</p>
	<?php
		} ?>
	<?php		
		foreach ($func as $funcdata) {
	?>
	<table width="360" height="230" border="0" cellpadding="20px" style="margin-left: 150px; margin-top:30px;" id="table2">
		<tbody>
			<tr>
				<td><label for="func_name">功能名称: </label></td>
				<td width="200"><input type="text" value="<?php echo $funcdata->func_name_zh; ?>" class="form-control" id="func_name" name="func_name"></td>
				<td></td>
			</tr>
			<tr>
				<td><label for="status">状态: </label></td>
				<td><input type="radio" name="func_status" value="1" <?php if($funcdata->status == 1) echo 'checked="checked"'; ?> />激活 
				    <input type="radio" name="func_status" value="0" <?php if($funcdata->status == 0) echo 'checked="checked"'; ?>/>未激活 
				</td>
			</tr>
			<tr>
				<td><label for="description">描述: </label></td>
				<td><textarea cols="30" rows="3" name="description"><?php echo $funcdata->description; ?> </textarea> </td>
			</tr>
			
		</tbody>
	</table>
	<?php } ?>
	<div style="margin-top:3%; margin-left:280px;">
	    <input type="submit" class="btn btn-primary" value="保存" id="sub3" style="width:70px">
	    <input type="button" onclick="location.href='?admin&page=funcmanage'" class="btn btn-primary" value="取消" id="sub3" style="width:70px; margin-left:20px;">
	</div>
	</form>
</div>
<?php //} ?>