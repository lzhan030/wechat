<?php
	$result=array();
	$tmp=array();
		$tmp['id']=0;
		$tmp['campaignId']=0;
		$tmp['name']=null;
	$result[]=$tmp;
		$tmp['id']=1;
		$tmp['campaignId']=0;
		$tmp['name']=null;
	$result[]=$tmp;
	$fresult=array();
	$fresult['code']=$result;
	echo json_encode($result);
	?>
