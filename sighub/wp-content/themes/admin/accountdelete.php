
<?php

require_once './wp-content/themes/ReeooV3/wesite/common/dbaccessor.php';
//加上这个代码，从js传参数过来

$wid=$_GET["id"];
echo $wid;

//如果newsItemId为空，删除的是某个多图文的某条图文
//如果不为空，删除整条多图文
$account_delete=account_delete($wid);
if($account_delete===false){
	echo "删除失败";
}else{
		echo "删除成功";
}		
	
?>