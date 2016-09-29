<?php defined('IN_IA') or exit('Access Denied');?><?php  if(is_array($list)) { foreach($list as $item) { ?>
 <?php include $this->template('list_item');?>
<?php  } } ?>