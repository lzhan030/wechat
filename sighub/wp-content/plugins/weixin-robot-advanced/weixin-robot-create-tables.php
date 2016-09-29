<?php

include("../../../wp-config.php");
weixin_robot_custom_replies_crate_table();
weixin_robot_messages_crate_table();

echo '已经手工创建自定义回复和数据库统计所需要的数据表';