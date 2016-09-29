<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>

<script type="text/javascript">

	var idat="<?php echo $indata ?>";
	var rang="<?php echo $range ?>";
	var flag=1;
<?php switch($range){
case 'all':
?>	
	location.href='<?php echo $this->createWebUrl('teachermanage',array('Ipad' => $indata , 'range'=> $range));?>';
	
<?php
break;
case 'tea_name':
?>	
location.href='<?php echo $this->createWebUrl('teachermanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
	//location.href='?admin&page=usermanage&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
case 'tea_gradeclass':
?>	
location.href='<?php echo $this->createWebUrl('teachermanage',array('Ipad' => $indata ,'flag'=> 1, 'range'=> $range));?>';
	//location.href='?admin&page=usermanage&Ipad='+idat+'&flag='+flag+'&range='+rang;
<?php
break;
default:
?>
location.href='<?php echo $this->createWebUrl('teachermanage',array('Ipad' => $indata));?>';
   // location.href='?admin&page=usermanage&Ipad='+idat;
<?php
} 
?>
</script>