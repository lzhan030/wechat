<?php
@session_start();

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

?>

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
	
	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
		<title></title>
	</head>

<?php
	echo getRefreshFlag($refreshOpener);
?>
	<?php
	if($postid==null){
		if($addAttachment == "true") {
			//$insert_postid=web_admin_create_post_and_attachment($post_title, $htmlData,$siteId);
			$insert_postid=web_admin_create_post($post_title, $htmlData,$siteId,$artType);
		}
		else {
			$insert_postid=web_admin_create_post($post_title, $htmlData,$siteId,$artType);
			
		}
		if($insert_postid==false){
			echo "添加失败!";
		}else{

		    if(strpos($insert_postid,"文章添加成功,但是同步失败,请更新该文章重新进行同步")>0)
			{
			    $insert_postid = ereg_replace('[^0-9]','',$insert_postid);  //截取字符串中前面的数字部分，表示文章的id  
			    echo "文章添加成功,但是同步失败,请更新该文章重新进行同步"; 
				//echo $insert_postid;
			}else if(strpos($insert_postid,"文章添加成功,同步也成功")>0){
			    $insert_postid = ereg_replace('[^0-9]','',$insert_postid);  //截取字符串中前面的数字部分，表示文章的id  
			    echo "文章添加成功,同步也成功";
			}else if(strpos($insert_postid,"文章添加成功,同步也成功,但是同步状态更新失败,请更新该文章重新进行同步")>0){
			    $insert_postid = ereg_replace('[^0-9]','',$insert_postid);  //截取字符串中前面的数字部分，表示文章的id  
			    echo "文章添加成功,同步也成功,但是同步状态更新失败,请更新该文章重新进行同步";
			}else if(strpos($insert_postid,"文章添加失败,同步失败,请重新添加并同步")>0){
			    echo "文章添加失败,同步失败,请重新添加并同步";
			}else if(strpos($insert_postid,"添加成功")>0){
			    $insert_postid = ereg_replace('[^0-9]','',$insert_postid);
			    echo "添加成功!";
			}
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
		<script language='javascript'> 
			opener.Wmenuurl.value="<?php echo $pou ?>";
		</script>
		 
	<?php	 
	}else{
		$old_content = web_admin_get_post($postid);
		$old_content = $old_content['post_content'];
		file_unlink_from_xml_update($old_content,$htmlData);
		$pos_update=web_admin_update_post($post_title, $htmlData,$postid);
		if(strpos($pos_update,"文章更新成功,同步也成功")>0)
		{
		    $pos_update = ereg_replace('[^0-9]','',$pos_update);  //截取字符串中前面的数字部分，表示文章的id  
		    echo "文章更新成功,同步也成功";
		}else if(strpos($pos_update,"文章更新成功,但是同步失败,请重新更新进行同步")>0){
		    $pos_update = ereg_replace('[^0-9]','',$pos_update);  //截取字符串中前面的数字部分，表示文章的id  
		    echo "文章更新成功,但是同步失败,请重新更新进行同步"; 
		}else if(strpos($pos_update,"文章更新失败,同步失败,请重新更新和同步")>0){
			    echo "文章更新失败,同步失败,请重新更新和同步";
		}else{
		    $pos_update = ereg_replace('[^0-9]','',$pos_update);  //截取字符串中前面的数字部分，表示文章的id  
		    echo "更新成功!";
		}
		
		
		//return "<body onunload='closeit()'>";
	}
		
	function getRefreshFlag($flag){
		if ($flag == "yes"){
			return "<body onload='closeit()'>";
		}else if($flag == "v2yes"){
			return "<body onload='closeitnore()'>";
		}
		else{
			return "<body>";
		}
	}
?>


</body>	
</html>

<script language='javascript'>
	function closeit() {
		top.resizeTo(300, 200); 		
		setTimeout("self.close()", 3000); 
		//opener.location.href = "<?php echo bloginfo('template_directory')?>/wesite/mobiletheme/post_list.php?beIframe&siteId="+"<?php echo $siteId;?>";
		window.opener.location.reload();
	}
	
	function closeitnore() {
		top.resizeTo(300, 200); 	
		
		setTimeout("self.close()", 3000); 
	}
    
</script>
