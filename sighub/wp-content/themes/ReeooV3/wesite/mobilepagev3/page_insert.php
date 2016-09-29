<?php
@session_start();

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>

<script type="text/javascript">
<?php

	include '../common/dbaccessor.php';

	//get the siteId and the post type
	$siteId=$_REQUEST["siteId"];
	$artType=$_REQUEST["artType"];
	$refreshOpener=$_REQUEST["refreshOpener"];
	$addAttachment=$_REQUEST["addAttachment"];	
	
	//insert or update
	$postid=$_REQUEST["postid"];
	
	//the post content
	$htmlData = '';
	$post_title=$_POST['post_title'];
	
	
	if (!empty($_POST['content1'])) {
		//if (get_magic_quotes_gpc()) {
			$htmlData = stripslashes($_POST['content1']);
		//} else {
		//������õ���·��������ģ�<img src=\"http://m3.biz.itc.cn/pic/new/n/62/35/Img5873562_n.jpg\" alt=\"\" />
			//$htmlData = $_POST['content1'];
		//}
	}	

	//echo htmlspecialchars($htmlData); 
    //$vo['content'] = stripslashes(htmlspecialchars_decode($_POST['content1']));
	
	
	
	if($postid==null){
		if($addAttachment == "true") {
			$insert_postid=web_admin_create_post_and_attachment($post_title, $htmlData,$siteId);
		}
		else {
			$insert_postid=web_admin_create_post3($post_title, $htmlData,$siteId,$artType);
		}
		$post=web_admin_get_post($insert_postid);
		
		foreach($post as $post_info){							
			$pou=$post_info->guid;
			/*如果没有http，证明为内链，加上home_url显示，插入时，判断有再截取入库*/
			$tmp = stristr($post_info->guid,"http");
			if(($tmp===false)&&(!empty($post_info->guid))){
				$pou=home_url().$post_info->guid;
			}else{				
				$pou=$post_info->guid;
			}
		}?>
		 
		 opener.Wmenuurl.value="<?php echo $pou ?>";
		 
	<?php
	}else{
		$pos_update=web_admin_update_post($post_title, $htmlData,$postid);
		if($pos_update==false){
			echo "更新失败!";
		}else{
			echo "更新成功!";
		}
		
		//return "<body onunload='closeit()'>";
	}
		
	function getRefreshFlag($flag){
		if ($flag == "yes"){
			return "<body onload='closeit()'>";
		}
		else {
			return "<body>";
		}
	}
?>
</script>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<title>�ޱ����ĵ�</title>
	</head>

<?php
	echo getRefreshFlag($refreshOpener);
?>
</body>	
</html>

<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); //������ҳ��ʾ�Ĵ�С		
		setTimeout("self.close()", 3000); //����
		
		//opener.location.reload();  //��ҳ��ˢ����ʾ
	}
    
</script>
