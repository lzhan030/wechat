<?php 
// 加载 WPJAM 后台选项设置基本函数库
if(!function_exists('wpjam_option_page')){
	include(WEIXIN_ROBOT_PLUGIN_DIR.'/include/wpjam-setting-api.php');
}

//后台菜单
add_action( 'admin_menu', 'weixin_robot_admin_menu' );
function weixin_robot_admin_menu() {
	add_menu_page('微信机器人', '微信机器人',	'manage_options',	'weixin-robot',	'weixin_robot_basic_page',	WEIXIN_ROBOT_PLUGIN_URL.'/weixin-16.ico');

	weixin_robot_add_submenu_page('basic', '设置', 'weixin-robot');

	if(wpjam_net_check_domain()){
		weixin_robot_add_submenu_page('advanced', '高级回复');
		weixin_robot_add_submenu_page('custom-reply', '自定义回复');
	
		$weixin_robot_basic = weixin_robot_get_option('weixin-robot-basic');

		if(($weixin_robot_basic['weixin_app_id'] && $weixin_robot_basic['weixin_app_secret'])||($weixin_robot_basic['yixin_app_id'] && $weixin_robot_basic['yixin_app_secret'])) {
			weixin_robot_add_submenu_page('custom-menu', '自定义菜单');
		}
		if(empty($weixin_robot_basic['weixin_disable_stats'])){
			weixin_robot_add_submenu_page('stats', '微信消息统计分析');
			weixin_robot_add_submenu_page('summary', '微信回复统计分析');
			weixin_robot_add_submenu_page('messages', '微信最新消息');
		}
		do_action('weixin_admin_menu');
	}

	//weixin_robot_add_submenu_page('about', '关于和更新');
}

function weixin_robot_add_submenu_page($key, $title, $slug='', $cap='manage_options'){
	if(!$slug) $slug = 'weixin-robot-'.$key;
	add_submenu_page( 'weixin-robot', $title.' &lsaquo; 微信机器人', $title, $cap, $slug, 'weixin_robot_'.str_replace('-', '_', $key).'_page');
}

add_action('wpjam_net_item_ids','weixin_robot_wpjam_net_item_id');
function weixin_robot_wpjam_net_item_id($item_ids){
	$item_ids['56'] = WEIXIN_ROBOT_PLUGIN_FILE;
	return $item_ids;
}

add_action('admin_head','weixin_robot_admin_head');
function weixin_robot_admin_head(){
	global $plugin_page;
	if(in_array($plugin_page, array('weixin-robot', 'weixin-robot-advanced', 'weixin-robot-custom-reply', 'weixin-robot-custom-menu', 'weixin-robot-stats', 'weixin-robot-summary', 'weixin-robot-messages', 'weixin-robot-about'))){
?>
	<style type="text/css">
	#icon-weixin-robot{background-image: url("<?php echo WEIXIN_ROBOT_PLUGIN_URL; ?>/weixin-32.png");background-repeat: no-repeat;}
	<?php if(in_array($plugin_page, array('weixin-robot-stats', 'weixin-robot-summary'))){?>
	h3{margin:20px 0;font-size: 20px;line-height: 23px;}
	<?php } ?>
	</style>
	<script type="text/javascript">
	jQuery(function(){
		jQuery('span.delete a').click(function(){
			return confirm('确实要删除吗?'); 
		}); 
	});
	</script> 
<?php
	}
}

add_action( 'admin_init', 'weixin_robot_admin_init' );
function weixin_robot_admin_init() {
	wpjam_add_settings(weixin_robot_get_basic_option_labels(),	weixin_robot_get_default_basic_option());
	wpjam_add_settings(weixin_robot_get_advanced_option_labels(),weixin_robot_get_default_advanced_option());
}

function weixin_robot_basic_page() {
	$labels = weixin_robot_get_basic_option_labels();
	wpjam_option_page($labels, $title='设置', $type='tab', $icon='weixin-robot');
}

function weixin_robot_advanced_page() {
	$labels = weixin_robot_get_advanced_option_labels();
	wpjam_option_page($labels, $title='高级回复', $type='default', $icon='weixin-robot');
}

/* 基本设置的字段 */

function weixin_robot_get_basic_option_labels(){
	$option_group               =   'weixin-robot-basic-group';
    $option_name = $option_page =   'weixin-robot-basic';
    $field_validate				=	'weixin_robot_basic_validate';

    $basic_section_fileds = array(
		'weixin_token'					=> array('title'=>'微信 Token',		'type'=>'text'),
		'weixin_default'				=> array('title'=>'默认缩略图',		'type'=>'text'),
		'weixin_keyword_allow_length'	=> array('title'=>'搜索关键字最大长度','type'=>'text',		'description'=>'一个汉字算两个字节，一个英文单词算两个字节，空格不算，搜索多个关键字可以用空格分开！'),
		'weixin_count'					=> array('title'=>'返回结果最大条数',	'type'=>'text',		'description'=>'微信接口最多支持返回10个。'), 
		'weixin_disable_stats'			=> array('title'=>'屏蔽统计',			'type'=>'checkbox',	'description'=>'屏蔽统计之后，就无法统计用户发的信息和系统的回复。'), 
    );

    $default_reply_section_fileds = array(
    	'weixin_welcome'				=> array('title'=>'用户关注默认回复',	'type'=>'textarea'),
		'weixin_keyword_too_long'		=> array('title'=>'超过最大长度回复',	'type'=>'textarea',	'description'=>'设置超过最大长度提示语，留空则不回复！'),
		'weixin_not_found'				=> array('title'=>'搜索结果为空回复',	'type'=>'textarea',	'description'=>'可以使用 [keyword] 代替相关的搜索关键字，留空则不回复！'),
    	'weixin_default_voice'			=> array('title'=>'语音默认回复',		'type'=>'textarea',	'description'=>'设置语言的默认回复文本，留空则不回复！'),
    	'weixin_default_location'		=> array('title'=>'位置默认回复',		'type'=>'textarea',	'description'=>'设置位置的默认回复文本，留空则不回复！'),
    	'weixin_default_image'			=> array('title'=>'图片默认回复',		'type'=>'textarea',	'description'=>'设置图片的默认回复文本，留空则不回复！'),
    );

    $app_section_fileds = array(
		'weixin_app_id'					=> array('title'=>'微信AppID',		'type'=>'text',		'description'=>'设置自定义菜单的所需的 AppID，如果没申请，可不填！'),
		'weixin_app_secret'				=> array('title'=>'微信APPSecret',	'type'=>'text',		'description'=>'设置自定义菜单的所需的 APPSecret，如果没申请，可不填！'),
    );

    $sections = array(
    	'basic'			=> array('title'=>'基本设置',			'callback'=>'weixin_robot_basic_section_callback',	'fileds'=>$basic_section_fileds),
    	'default_reply'	=> array('title'=>'默认回复',			'callback'=>'',	'fileds'=>$default_reply_section_fileds),
    	'app'			=> array('title'=>'APP Key',		'callback'=>'',	'fileds'=>$app_section_fileds)
	);

	$sections = apply_filters('weixin_setting',$sections);

	return compact('option_group','option_name','option_page','sections','field_validate');
}

function weixin_robot_get_default_basic_option(){
	$default_options = array(
		'weixin_token'					=> 'weixin',
		'weixin_default'				=> '',
		'weixin_keyword_allow_length'	=> '16',
		'weixin_count'					=> '5',
		'weixin_disable_stats'			=> '0',
		'weixin_welcome'				=> "输入 n 返回最新日志！\n输入 r 返回随机日志！\n输入 t 返回最热日志！\n输入 c 返回最多评论日志！\n输入 t7 返回一周内最热日志！\n输入 c7 返回一周内最多评论日志！\n输入 h 获取帮助信息！",
		'weixin_keyword_too_long'		=> '你输入的关键字太长了，系统没法处理了，请等待公众账号管理员到微信后台回复你吧。',
		'weixin_not_found'				=> '抱歉，没有找到与[keyword]相关的文章，要不你更换一下关键字，可能就有结果了哦 :-)',
		'weixin_default_voice'			=> "系统暂时还不支持语音回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。",
		'weixin_default_location'		=> "系统暂时还不支持位置回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。",
		'weixin_default_image'			=> "系统暂时还不支持图片回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。",
	);
	return apply_filters('weixin_default_option',$default_options,'weixin-robot-basic');
}

function weixin_robot_basic_section_callback(){
	echo '<p style="font-weight:bold;">友情提示：查看<a href="http://blog.wpjam.com/m/weixin-robot-advanced-faq/">微信机器人高级版常见问题汇总</a>可以解决你使用当中碰到的绝大多数问题。</p>';
}

function weixin_robot_basic_validate( $weixin_robot_basic ) {
	$current = get_option( 'weixin-robot-basic' );

	if ( !is_numeric( $weixin_robot_basic['weixin_keyword_allow_length'] ) ){
		$weixin_robot_basic['weixin_keyword_allow_length'] = $current['weixin_keyword_allow_length'];
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '搜索关键字最大长度必须为数字。' );
	}
	if ( !is_numeric( $weixin_robot_basic['weixin_count'] ) ){
		$weixin_robot_basic['weixin_count'] = $current['weixin_count'];
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '返回结果最大条数必须为数字。' );
	}elseif($weixin_robot_basic['weixin_count'] > 10){
		$weixin_robot_basic['weixin_count'] = 10;
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '返回结果最大条数不能超过10。' );
	}
	if(empty($weixin_robot_basic['weixin_disable_stats'])){ //checkbox 未选，Post 的时候 $_POST 中是没有的，
		$weixin_robot_basic['weixin_disable_stats'] = 0;
	}

	return $weixin_robot_basic;
}

/* 高级回复的字段 */
function weixin_robot_get_advanced_option_labels(){
	$option_group               =   'weixin-robot-advanced-group';
	$option_name = $option_page =   'weixin-robot-advanced';
	$field_validate				=	'';

    $advanced_section_fileds = array(
		'new'			=>array('title'=>'返回最新日志关键字',			'type'=>'text'),
		'rand'			=>array('title'=>'返回随机日志关键字',			'type'=>'text'),
		'hot'			=>array('title'=>'返回浏览最高日志关键字',		'type'=>'text',	'description'=>'博客必须首先安装 Postview 插件！'),
		'comment'		=>array('title'=>'返回留言最高日志关键字',		'type'=>'text'),
		'hot-7'			=>array('title'=>'返回7天内浏览最高日志关键字',	'type'=>'text',	'description'=>'博客必须首先安装 Postview 插件！'),
		'comment-7'		=>array('title'=>'返回7天内留言最高日志关键字',	'type'=>'text')
	);

	$advanced_section_fileds = apply_filters('weixin_advanced_fileds',$advanced_section_fileds);

	$sections = array( 
    	'advanced'=>array('title'=>'',	'callback'=>'weixin_robot_advanced_section_callback',	'fileds'=>$advanced_section_fileds)
	);

	return compact('option_group','option_name','option_page','sections','field_validate');
}

function weixin_robot_get_default_advanced_option(){
 	$default_options = array(
		'new'		=> 'n',
		'rand'		=> 'r', 
		'hot'		=> 't',
		'comment'	=> 'c',
		'hot-7'		=> 't7',
		'comment-7'	=> 'c7',
		'hot-30'	=> 't30',
		'comment-30'=> 'c30'
	);
	return apply_filters('weixin_default_option',$default_options,'weixin-robot-advanced');
}

function weixin_robot_advanced_section_callback(){
	echo '<p style="color:red; font-weight:bold;">修改下面的关键字，请主要修改下基本设置中欢迎语中对应的关键字。</p>';
}
