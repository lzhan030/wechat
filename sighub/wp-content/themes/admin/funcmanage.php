<?php

if(isset($_GET['del']) && !empty($_GET['del']) ){
	require_once './wp-admin/includes/user.php';
	wp_delete_user( $_GET['del'] );
	}
	
global $wpdb;
if(isset($_GET['id'])&&isset($_GET['status']))
	$wpdb->query("UPDATE ".$wpdb->prefix."wechat_func_info SET status=".$_GET['status']." WHERE ID=".$_GET['id']);
$result = $wpdb->get_results("SELECT * from ".$wpdb->prefix."wechat_func_info");

?>

<div class="main-title">
	<div class="title-1">当前位置：功能列表管理 > <font class="fontpurple">功能列表 </font>
	</div>
</div>
<div class="bgimg"></div>
	
<div class="panel panel-default" style="margin-right:50px; margin-top:30px">
	<div class="panel-heading">功能列表</div>
	<table class="table table-striped" width="800" border="1" align="center">
	<tbody>
		<tr>
			<td scope="col" width="100" align="center" style="font-weight:bold">功能名称</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">状态</td>
			<td scope="col" width="150" align="center" style="font-weight:bold">操作</td>
		</tr>
		
		<?php		
		foreach ($result as $funclist) {
		?>
		<tr>
			<td align="center"><?php echo $funclist->func_name_zh; ?> </td>
			<td align="center">
			    <input type="radio" onclick="location.href='?admin&page=funcmanage&id=<?php echo $funclist->ID; ?>&status=1'" name="<?php echo $funclist->func_name; ?>" value="1" <?php if($funclist->status == 1) echo 'checked="checked"'; ?>/>激活 
				<input type="radio" onclick="location.href='?admin&page=funcmanage&id=<?php echo $funclist->ID; ?>&status=0'" name="<?php echo $funclist->func_name; ?>" value="0" <?php if($funclist->status == 0) echo 'checked="checked"'; ?>/>未激活
			</td>
			<td class="row" align="center"><input name="site_id" type="hidden" id="site_id" value="308" maxlength="100"> <input style="display:none;" type="button" class="btn btn-sm btn-warning" onclick="location.href='?admin&page=funcmanage&del=<?php echo $funclist->ID ?>'" name="del" id="buttondel" value="删除"> <input type="button" class="btn btn-sm btn-info" onclick="location.href='?admin&page=funcedit&id=<?php echo $funclist->ID ?>'" name="upd" id="buttonupd" value="更新"> </td>
		</tr>
		<?php
		}
        ?>
		</tr>
	</tbody>
</table>
</div>
					
					
