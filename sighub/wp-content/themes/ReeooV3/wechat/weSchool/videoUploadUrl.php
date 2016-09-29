<?php
require_once MODULES_DIR.$this -> module['name'].'/upload/OSS/aliyun.php';

use Aliyun\OSS\OSSClient;

$videofilename = substr(basename($_GPC['filename']), strripos( basename($_GPC['filename']),"\\")?(strripos( basename($_GPC['filename']),"\\")+1):0 );

$client = OSSClient::factory(array(
	'AccessKeyId' => 'sbeMJTdyfvlFJ72x',
	'AccessKeySecret' => 'sCfz5NapaGC1U4pj2vGXul5LR0ziLd',
    'Endpoint' => 'http://oss-cn-hangzhou.aliyuncs.com',
));
$url = $client->generatePresignedUrl(array(
	'Bucket' => 'org-video',
	'Key' => $videofilename,
	'Expires' => new \DateTime("+50 minutes"),
));

?>