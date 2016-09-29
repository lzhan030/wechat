<?php 
add_action('weixin_robot','wpjam_stats_weixin_robot',10,2);
function wpjam_stats_weixin_robot($postObj,$response){
	if($response){
		weixin_robot_insert_message($postObj,$response);
	}
}

function weixin_robot_get_messages_table(){
	global $wpdb;
	return apply_filters('weixin_messages_table',$wpdb->prefix.'weixin_messages');
}

register_activation_hook( WEIXIN_ROBOT_PLUGIN_FILE,'weixin_robot_messages_crate_table');
function weixin_robot_messages_crate_table() {	
	global $wpdb;
 
	$weixin_messages_table = weixin_robot_get_messages_table();
	if($wpdb->get_var("show tables like '$weixin_messages_table'") != $weixin_messages_table) {
		$sql = "
		CREATE TABLE IF NOT EXISTS ".$weixin_messages_table." (
			`id` bigint(20) NOT NULL auto_increment,
			`MsgId` bigint(64) NOT NULL,
			`FromUserName` varchar(30) character set utf8 NOT NULL,
			`MsgType` varchar(10) character set utf8 NOT NULL,
			`CreateTime` int(10) NOT NULL,

			`Content` longtext character set utf8 NOT NULL,

			`PicUrl` varchar(255) character set utf8 NOT NULL,

			`Location_X` double(10,6) NOT NULL,
			`Location_Y` double(10,6) NOT NULL,
			`Scale` int(10) NOT NULL,
			`label` varchar(255) character set utf8 NOT NULL,

			`Title` text character set utf8 NOT NULL,
			`Description` longtext character set utf8 NOT NULL,
			`Url` varchar(255) character set utf8 NOT NULL,

			`Event` varchar(255) character set utf8 NOT NULL,
			`EventKey` varchar(255) character set utf8 NOT NULL,

			`Format` varchar(255) character set utf8 NOT NULL,
			`MediaId` text character set utf8 NOT NULL,
			`Recognition` text character set utf8 NOT NULL,
		 
			`Response` varchar(255) character set utf8 NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
 
		dbDelta($sql);
	}
}

function weixin_robot_insert_message($postObj,$Response=''){

	global $wpdb;

	$data = array(
		'MsgType'		=>	$postObj->MsgType,
		'FromUserName'	=>	$postObj->FromUserName,
		'CreateTime'	=>	$postObj->CreateTime,
		'Response'		=>	$Response,
	);

	$msgType = $postObj->MsgType;

	if($msgType == 'text'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Content']	= $postObj->Content;
	}elseif($msgType == 'image'){
		$data['MsgId']		= $postObj->MsgId;
		$data['PicUrl']		= $postObj->PicUrl;
	}elseif($msgType == 'location'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Location_X']	= $postObj->Location_X;
		$data['Location_Y']	= $postObj->Location_Y;
		$data['Scale']		= $postObj->Scale;
		$data['Label']		= $postObj->Label;
	}elseif($msgType == 'link'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Title']		= $postObj->Title;
		$data['Description']= $postObj->Description;
		$data['Url']		= $postObj->Url;
	}elseif($msgType == 'event'){
		$data['Event']		= $postObj->Event;
		$data['EventKey']	= $postObj->EventKey;
	}elseif($msgType == 'voice'){
		$data['MsgId']		= $postObj->MsgId;
		$data['Format']		= $postObj->Format;
		$data['MediaId']	= $postObj->MediaId;
		$data['Recognition']= $postObj->Recognition;
	}

	$weixin_messages_table = weixin_robot_get_messages_table();
	$wpdb->insert($weixin_messages_table,$data); 
	return $wpdb->insert_id;
}

/*function weixin_robot_update_message($id,$Response){
	global $wpdb;
	$data = array('Response'	=>	$Response );
	$weixin_messages_table = weixin_robot_get_messages_table();
	$wpdb->update($weixin_messages_table,$data,array('id'=>$id));
}*/

add_action('admin_head','weixin_robot_stats_admin_head',999);
function weixin_robot_stats_admin_head(){
	global $plugin_page;
	if(in_array($plugin_page, array('weixin-robot-stats', 'weixin-robot-summary'))){
?>
<link rel="stylesheet" href="http://cdn.staticfile.org/morris.js/0.4.2/morris.min.css" />
<script type='text/javascript' src="http://cdn.staticfile.org/raphael/2.1.0/raphael-min.js"></script>
<script type='text/javascript' src="http://cdn.staticfile.org/morris.js/0.4.2/morris.min.js"></script>
<style type="text/css">
input[type="date"]{ background-color: #fff; border-color: #dfdfdf; border-radius: 3px; border-width: 1px; border-style: solid; color: #333; outline: 0; box-sizing: border-box; }
.widefat td { padding:4px 10px; vertical-align: middle;}
</style>
<?php
	}
}

function weixin_robot_stats_get_start_date(){
	$start_date	= (isset($_REQUEST['start_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_REQUEST['start_date']))?$_REQUEST['start_date']:'';
	if(!$start_date) $start_date=gmdate('Y-m-d',current_time('timestamp')-(60*60*24*30));
	return $start_date;
}

function weixin_robot_stats_get_end_date(){
	$end_date 	= (isset($_REQUEST['end_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_REQUEST['end_date']))?$_REQUEST['end_date']:'';
	if(!$end_date) $end_date=gmdate('Y-m-d',current_time('timestamp'));
	return $end_date;
}

function weixin_robot_stats_get_type(){
	return isset($_REQUEST['type'])?$_REQUEST['type']:'';
}

function weixin_robot_stats_header(){
	global $plugin_page;
	$start_date	= weixin_robot_stats_get_start_date();
	$end_date 	= weixin_robot_stats_get_end_date();
	?>
	<div class="tablenav">
    <div class="alignleft actions">
        <form method="get" action="admin.php" target="_self" id="export-filter" style="float:left;">
        	<input type="hidden" name="page" value="<?php echo $plugin_page;?>" />
            日期:
            <input type="date" name="start_date" id="start_date" value="<?php echo esc_attr($start_date);?>" size="11" />
            -
            <input type="date" name="end_date" id="end_date" value="<?php echo esc_attr($end_date);?>" size="11" />
            <?php /*<select name="type">
			<?php foreach ($types as $key => $value) {?>
				<option value="<?php echo $key; ?>"<?php if($type == $key) echo ' selected="selected"';?>><?php echo $value;?></option>
			<?php }?>
			</select>*/?>
            <input type="submit" value="　显示　" class="button-secondary" name="">
        </form>
    </div>
	</div>
    <h3><?php echo $start_date,' - ',$end_date; ?> 汇总数据：</h3>
<?php
}

function weixin_robot_stats_get_types(){
	return array(
		'total'			=>'所有类型',
		'text'			=>'文本消息', 
		'event'			=>'事件消息', 
		'subscribe'		=>'用户订阅', 
		'unsubscribe'	=>'取消订阅', 
		'location'		=>'位置消息', 
		'image'			=>'图片消息', 
		'link'			=>'链接消息', 
		'voice'			=>'语音消息'
	);
}

function weixin_robot_get_response_types(){
	$response_types = array(
		'total'			=> '所有类型',
		'advanced'		=> '高级回复',
		'welcome'		=> '欢迎语',
		'tag'			=> '标签最新日志',
		'cat'			=> '分类最新日志',
		'custom-text'	=> '自定义文本回复',
		'custom-img'	=> '自定义图文回复',
		'query'			=> '搜索查询回复',
		'too-long'		=> '关键字太长',
		'not-found'		=> '没有匹配内容',
		'voice'			=> '语音自动回复',
		'loction'		=> '位置自动回复',
		'image'			=> '图片自动回复'
	);

	return apply_filters('weixin_response_types',$response_types);
}

function weixin_robot_stats_page() {
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div> 
		<h2>消息统计分析</h2>
		<?php
		global $wpdb, $plugin_page;

		$start_date	= weixin_robot_stats_get_start_date();
		$end_date 	= weixin_robot_stats_get_end_date();
		$end_time	= $end_date.' 23:59:59';

		$type = weixin_robot_stats_get_type();
		if(!$type) $type = 'total';

		$types = weixin_robot_stats_get_types();
		
		$weixin_messages_table = weixin_robot_get_messages_table();

		$where = 'CreateTime > '.strtotime($start_date).' AND CreateTime < '.strtotime($end_time);
		$sum = "
		SUM(case when MsgType='text' then 1 else 0 end) as text,
		SUM(case when MsgType='event' AND Event!='subscribe' AND Event!='unsubscribe' then 1 else 0 end) as event, 
		SUM(case when MsgType='event' AND Event='subscribe' then 1 else 0 end) as subscribe, 
		SUM(case when MsgType='event' AND Event='unsubscribe' then 1 else 0 end) as unsubscribe,
		SUM(case when MsgType='location' then 1 else 0 end) as location, 
		SUM(case when MsgType='image' then 1 else 0 end) as image, 
		SUM(case when MsgType='link' then 1 else 0 end) as link, 
		SUM(case when MsgType='voice' then 1 else 0 end) as voice
		";

		weixin_robot_stats_header();

		$sql = "SELECT {$sum} FROM {$weixin_messages_table} WHERE {$where}";

		$count = $wpdb->get_row($sql);

		?>
		<div style="display:table;">

			<div style="display: table-row;">

				<div id="total-chart" style="display: table-cell; width:450px; float:left;"></div>

				<div style="display: table-cell; float:left; width:200px;">
					<table class="widefat" cellspacing="0">
						<thead>
							<tr>
								<th>类型</th>
								<th>数量</th>
							</tr>
						</thead>
						<tbody>
						<?php $data = array();?>	
						<?php foreach ($types as $key=>$value) {?>
							<?php if($key != 'total' && $count->$key){?>
							<?php $data []= '{"label": "'.$value.'", "value": '.$count->$key.' }'; ?>
							<tr>
								<td><?php echo $value; ?></td>
								<td><?php echo $count->$key; ?></td>
							</tr>
							<?php }?>
						<?php } ?>
						<?php $data = "\n".implode(",\n", $data)."\n";?>
						</tbody>
					</table>
				</div>

			</div>

		</div>

		<script type="text/javascript">
			Morris.Donut({
			  element: 'total-chart',
			  data: [<?php echo $data;?>]
			});
		</script>

		<div style="clear:both;"></div>

		<h3>每日详细数据</h3>

		<ul class="subsubsub">
			<?php $current_page_base_url = 'admin.php?page='.$plugin_page.'&start_date='.$start_date.'&end_date='.$end_date; ?>
			<?php foreach ($types as $key=>$value) { ?>
			<li class="<?php echo $key?>"><a href="<?php echo admin_url($current_page_base_url.'&type='.$key)?>" <?php if($type == $key) {?> class="current"<?php } ?>><?php echo $value;?></a> |</li>
			<?php }?>
		</ul>

		<div style="clear:both;"></div>

		<?php

		$sql = "SELECT FROM_UNIXTIME(CreateTime, '%Y-%m-%d') as day, count(id) as total, {$sum} FROM {$weixin_messages_table} WHERE {$where} GROUP BY day ORDER BY day;";

		$counts = $wpdb->get_results($sql);

		$data = array();

		if($type == 'total'){	
			$morris_ykeys = array('total','text','event','subscribe','unsubscribe');

			$morris_labels = array();
			foreach ($morris_ykeys as $morris_ykey) {
				$morris_labels[] = $types[$morris_ykey];
			}

			foreach ($counts as $count) {
				$morris_data = '';
				foreach ($morris_ykeys as $morris_ykey) {
					$morris_data .= ', "'.$morris_ykey.'": '.$count->$morris_ykey;
				}
				$data []= '{"day": "'.$count->day.'"'.$morris_data.' }';
			}

			$morris_ykeys = "'".implode("','", $morris_ykeys)."'";
			$morris_labels = "'".implode("','", $morris_labels)."'";

		}else{
			$morris_ykeys = "'".$type."'";
			$morris_labels = "'".$types[$type]."'";

			foreach ($counts as $count) {
				$data []= '{"day": "'.$count->day.'"'.', "'.$type.'": '.$count->$type.' }';
			}
		}

		$data = "\n".implode(",\n", $data)."\n";

		?>
		
		<div id="daily-chart"></div>

		<script type="text/javascript">
			Morris.Line({
				element: 'daily-chart',
				data: [<?php echo $data;?>],
				xkey: 'day',
				ykeys: [<?php echo $morris_ykeys;?>],
				labels: [<?php echo $morris_labels;?>]
			});
		</script>
		
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th>日期</th>
				<?php foreach ($types as $key=>$value) {?>
				<th><?php echo $value;?></th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
		<?php foreach (array_reverse($counts) as $count) {?>
			<tr>
				<td><?php echo $count->day; ?></td>
				<?php foreach ($types as $key=>$value) {?>
				<td><?php echo $count->$key;?></td>
				<?php }?>
			</tr>
		<?php } ?>
		</tbody>
		</table>
		<?php
}

function weixin_robot_summary_page(){ 
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2>回复统计分析</h2>
		<?php 
		global $wpdb, $plugin_page;

		$start_date	= weixin_robot_stats_get_start_date();
		$end_date 	= weixin_robot_stats_get_end_date();
		$end_time	= $end_date.' 23:59:59';

		$response_types = weixin_robot_get_response_types();
		$response_type = isset($_GET['response_type'])?$_GET['response_type']:'total';

		$weixin_messages_table = weixin_robot_get_messages_table();

		$where = 'CreateTime > '.strtotime($start_date).' AND CreateTime < '.strtotime($end_time);

		weixin_robot_stats_header();

		$sql = "SELECT COUNT( * ) AS count, Response FROM {$weixin_messages_table} WHERE {$where} AND (MsgType ='text' OR MsgType = 'event') AND Event!='subscribe' AND Event!='unsubscribe' GROUP BY Response ORDER BY count DESC";

		$counts = $wpdb->get_results($sql);
		?>
		<div style="display:table;">

			<div style="display: table-row;">

				<div id="total-chart" style="display: table-cell; width:450px; float:left;"></div>

				<div style="display: table-cell; float:left; width:200px;">
					<table class="widefat" cellspacing="0">
						<thead>
							<tr>
								<th>回复类型</th>
								<th>数量</th>
							</tr>
						</thead>
						<tbody>
						<?php $data = array(); $i=0;?>
						<?php foreach ($counts as $count) {?>
							<?php if($count->Response && isset($response_types[$count->Response])){?>
							<?php $data []= '{"label": "'.$response_types[$count->Response].'", "value": '.$count->count.' }'; $i ++; ?>
							<tr>
								<td><?php echo $response_types[$count->Response]; ?></td>
								<td><?php echo $count->count; ?></td>
							</tr>
							<?php }?>
						<?php } ?>
						<?php $data = "\n".implode(",\n", $data)."\n";?>
						</tbody>
					</table>
				</div>

			</div>

		</div>

		<script type="text/javascript">
			Morris.Donut({
			  element: 'total-chart',
			  data: [<?php echo $data;?>]
			});
		</script>

		<div style="clear:both;"></div>

		<h3>详细回复统计分析</h3>

		<ul class="subsubsub">
			<?php $current_page_base_url = 'admin.php?page='.$plugin_page.'&start_date='.$start_date.'&end_date='.$end_date; ?>
			<li class="<?php echo 'total'?>"><a href="<?php echo admin_url($current_page_base_url.'&response_type=total')?>" <?php if($response_type == 'total') {?> class="current"<?php } ?>>全部</a> |</li>
			<?php foreach ($counts as $count) { ?>
			<?php if($count->Response && isset($response_types[$count->Response])){?>
			<li class="<?php echo $count->Response;?>"><a href="<?php echo admin_url($current_page_base_url.'&response_type='.$count->Response)?>" <?php if($response_type == $count->Response) {?> class="current"<?php } ?>><?php echo $response_types[$count->Response];?></a> |</li>
			<?php } ?>
			<?php }?>
		</ul>

		<?php

		if($response_type == 'total'){
			$where .= " AND Response != ''";
		}else{
			$where .= " AND Response = '{$response_type}'";
		}

		$sql = "SELECT COUNT( * ) AS count, Response, MsgType, (case when Content='' then EventKey else Content end) as Content FROM {$weixin_messages_table} WHERE {$where} AND ( MsgType ='text' OR (MsgType = 'event'  AND Event!='subscribe' AND Event!='unsubscribe')) GROUP BY LOWER(Content) ORDER BY count DESC LIMIT 0 , 100";

		$sql = "SELECT COUNT( * ) AS count, Response, MsgType, Content FROM ( SELECT Response, MsgType, LOWER(Content) as Content FROM {$weixin_messages_table} WHERE {$where} AND MsgType ='text' UNION ALL SELECT Response, MsgType,  LOWER(EventKey) as Content FROM {$weixin_messages_table} WHERE {$where} AND MsgType = 'event'  AND Event!='subscribe' AND Event!='unsubscribe') as T1 GROUP BY Content ORDER BY count DESC LIMIT 0 , 100";

		
		$weixin_hot_messages = $wpdb->get_results($sql);
		?>
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th style="width:60px">排名</th>
				<th style="width:80px">数量</th>
				<th>关键词</th>
				<th style="width:150px">回复类型</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		foreach ($weixin_hot_messages as $weixin_message) {
			if(isset($response_types[$weixin_message->Response])){
			$i++;
		?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $weixin_message->count; ?></td>
				<td><?php echo $weixin_message->Content; ?></td>
				<td><?php echo $response_types[$weixin_message->Response]; ?></td>
			</tr>
			<?php } ?>
		<?php } ?>
		</tbody>
		</table>

	<?php
	
}

function weixin_robot_messages_page() {
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2>最新消息</h2>
		<p>下面是你公众号上最新的消息，你可以直接删除（WordPress 本地删除，公众号后台不受影响）！</p>

		<?php
		$response_types = weixin_robot_get_response_types();

		$types = weixin_robot_stats_get_types();
		unset($types['subscribe']);
		unset($types['unsubscribe']);

		$type = weixin_robot_stats_get_type();
		if(!$type){
			$type = 'total';
		}
		?>

		<ul class="subsubsub">
		<?php foreach ($types as $key=>$value) { ?>
			<li class="<?php echo $key;?>"><a href="<?php global $plugin_page; echo admin_url('admin.php?page='.$plugin_page.'&type='.$key)?>" <?php if($type == $key) {?> class="current"<?php } ?>><?php echo $value;?></a> |</li>
		<?php }?>
		</ul>
		<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th style="width:140px">时间</th>
				<th style="width:60px">类型</th>
				<th>内容</th>
				<th style="width:120px">回复类型</th>
				<th style="width:40px">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		global $wpdb;

		$weixin_messages_table = weixin_robot_get_messages_table();

		if(isset($_GET['delete']) && isset($_GET['id']) && $_GET['id']){
			$wpdb->query("DELETE FROM $weixin_messages_table WHERE id = {$_GET['id']}");
		}elseif(isset($_GET['post']) && isset($_GET['id']) && $_GET['id']){

		}

		$current_page = isset($_GET['paged'])?$_GET['paged']:1;
		$start_count	= ($current_page-1)*50;
		$limit = 'LIMIT '.$start_count.',50';

		if($type =='total'){
			$where = '';
		}else{
			$where = "AND MsgType = '{$type}'";					
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$weixin_messages_table} WHERE 1=1 {$where} AND Event!= 'subscribe' AND Event != 'unsubscribe'  ORDER BY CreateTime DESC ".$limit;

		$weixin_messages = $wpdb->get_results($sql);

		foreach ($weixin_messages as $weixin_message) {?>
			<tr>
				<td><?php echo date('Y-m-d H:i:s',$weixin_message->CreateTime+8*60*60); ?></td>
				<td><?php echo $types[$weixin_message->MsgType]; ?></td>
				<td>
				<?php 
				if($weixin_message->MsgType == 'text'){
					echo $weixin_message->Content; 
				}elseif($weixin_message->MsgType == 'image'){
					echo '<a href="'.$weixin_message->PicUrl.'" target="_blank"><img src="'.$weixin_message->PicUrl.'" width="100px;"></a>';
				}elseif($weixin_message->MsgType == 'location'){
					echo '<a href="http://ditu.google.cn/maps?q='.urlencode($weixin_message->label).'&amp;ll='.$weixin_message->Location_X.','.$weixin_message->Location_Y.'&amp;source=embed" target="_blank">'.$weixin_message->label.'</a>';
				}elseif($weixin_message->MsgType == 'event'){
					echo $weixin_message->EventKey; 
				}else{
					echo '该类型的内容无法显示，请直接访问微信公众号后台进行操作！';
				}
				?>
				</td>
				<td><?php if(isset($response_types[$weixin_message->Response])){echo $response_types[$weixin_message->Response]; }?></td>
				<td><?php /*if($weixin_message->MsgType == 'text'){ ?><span><a href="<?php echo admin_url('admin.php?page=weixin-robot-messages&post&id='.$weixin_message->id); ?>">发布日志</a></span> | <?php } */?><span class="delete"><a href="<?php echo admin_url('admin.php?page=weixin-robot-messages&delete&id='.$weixin_message->id); ?>">删除</a></span></td>
			</tr>
		<?php } ?>
		</tbody>
		</table>
		<?php 
			$total_count	= $wpdb->get_var("SELECT FOUND_ROWS();");
			$total_pages	= ceil($total_count/50);
			
			if($current_page > 1 && $current_page < $total_pages){
				$prev_page = $current_page-1;
				$next_page = $current_page+1;
			}elseif($current_page == 1){
				$prev_page = 1;
				$next_page = $current_page+1;
			}elseif($current_page == $total_pages){
				$prev_page = $current_page-1;
				$next_page = $current_page;
			}

			$current_page_base_url = admin_url('admin.php?page=weixin-robot-messages');
		?>
		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo $total_count;?> 个项目</span>
				<span class="pagination-links">
					<a class="first-page <?php if($prev_page==1) echo 'disabled'; ?>" title="前往第一页" href="<?php echo $current_page_base_url;?>">«</a>
					<a class="prev-page <?php if($prev_page==1) echo 'disabled'; ?>" title="前往上一页" href="<?php echo $current_page_base_url;?>&amp;paged=<?php echo $prev_page;?>">‹</a>
					<span class="paging-input">第 <?php echo $current_page;?> 页，共 <span class="total-pages"><?php echo $total_pages; ?></span> 页</span>
					<a class="next-page <?php if($prev_page==$total_pages) echo 'disabled'; ?>" title="前往下一页" href="<?php echo $current_page_base_url;?>&amp;paged=<?php echo $next_page;?>">›</a>
					<a class="last-page <?php if($prev_page==$total_pages) echo 'disabled'; ?>" title="前往最后一页" href="<?php echo $current_page_base_url;?>&amp;paged=<?php echo $total_pages; ?>">»</a>
				</span>
			</div>
			<br class="clear">
		</div>
	</div>
<?php } ?>