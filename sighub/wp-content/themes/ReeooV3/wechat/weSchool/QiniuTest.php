<?php
require_once("qiniu/rs.php");

$bucket = "wevideo";
$key = "pic.jpg";
$accessKey = 'BnEuL9EBya39evSshr9Z5uUZYdWaElRZlDuC1c7b';
$secretKey = 'kQntsPFbLqaQLDEN_dOBm3c8VUiyrVIylkNBq__b';

Qiniu_SetKeys($accessKey, $secretKey);


$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$putPolicy -> Expires = 3600*24;
$putPolicy -> PersistentOps = 'avthumb/mp4';
$putPolicy -> PersistentNotifyUrl='http://mednoter.com/video-hosting.html';
$upToken = $putPolicy->Token(null);
?>

<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
	<form id="qiniu_upload" action="http://up.qiniu.com" method="post" enctype="multipart/form-data">
		<input type="hidden" value="<?php echo $upToken ?>" class="form-control" name="token" />
		<input id="fileupload" type="file" name="file">
		<input type="submit" value="Submit" />
	</form>
</body>

</html>