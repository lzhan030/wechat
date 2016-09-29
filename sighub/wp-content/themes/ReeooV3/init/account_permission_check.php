<?php 
$result = web_user_display_index_groupnew_wesforsel($_SESSION['GWEID']);
foreach($result as $initfunc){
	if($selCheck[$initfunc->func_name] == 0)
		$selCheck[$initfunc->func_name] = $initfunc->status;
}
if($selCheck['wechatfuncaccountmanage']!=1){?>
	<script>
		location.href="<?php echo get_bloginfo('template_directory')?>/wesite/common/perdenied_forweb.php?gweid=<?php echo $_SESSION['GWEID'];?>#wechat_redirect";
	</script>
<?php	exit();
}
?>
