<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<title></title>
	</head>
</body>	
</html>

<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); //?????????????§³		
		setTimeout("self.close()", 3000); //????
		
		opener.location.reload();  //???????????
	}
</script>