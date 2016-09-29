<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<?php
echo '<h3>Body Part</h3><BR>';
?>
<a href="<?php echo $this->createWebUrl('index',array('id' => 3))?>"><?php echo $this->createWebUrl('index',array('id' => 3))?></a><BR>
<a href="<?php echo $this->createMobileUrl('index',array('id' => 3))?>"><?php echo $this->createMobileUrl('index',array('id' => 3))?></a><BR>
<?php
print_r($test);