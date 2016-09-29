<?php

include './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
$menu_name=$_POST['menu_name'];
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<title></title>
	</head>

<?php
	   $m_name=wechat_add_menu_name($menu_name);
		if($m_name===false){
			echo "添加失败!";
			echo  "<body onload='closeit()'>";
		}else{
			echo "添加成功!";
			echo "<body onload='closeit()'>";
		} 
?>
</body>	
</html>
<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); 		
		setTimeout("self.close()", 3000); 
		opener.location.reload();
	}  
</script>

