<?php
$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');
if( isset($_POST['alarm_spa']) ){
  $alarmspace = $_POST['alarm_spa'];
  update_option(alarm_space, $alarmspace);
}
$asps=get_option( alarm_space );

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
		<script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
		<title>设置</title>
		<script>  
		<?php if( $_SERVER['REQUEST_METHOD'] == 'POST' ) { ?> 
		        window.opener.location.reload();  				
		        window.opener=null;
				window.open('', '_self', '');
				window.close();  <?php } ?>
		function close2(){
		    window.close();
	    }
		</script>
		<style>
			.panel-info{border-color: #FFF;}
		</style>
	</head>
	<body>
		<div class="dlg-panel panel panel-info">
			<form role="form" action=" " method="post" style="height:250px;"> 
			<table  width="400"  border="0" align="center" style="margin-top:30px">
				<tr>
				<td>空间剩余大小设置为:</td> 
				<td><input type="text" value="<?php echo $asps; ?>" class="form-control" id="alarm_spa" name="alarm_spa"></td>
				<td>M</td> 
				</tr>	
			</table>
				<div style="margin-bottom:10px;margin-top:50px;margin-left:300px;">
					<input type="submit" class="btn btn-sm btn-primary" value="提交" style="width:120px;"/>	
					<input type="cancel" class="btn btn-sm btn-default" value="取消" onclick="close2()" style="width:120px;"/>
				</div>
			</form>
		</div>
	</body>	
</html>