<?php

//如果搜索关键字是分类名或者 tag 名，直接返回该分类或者tag下最新日志
add_filter('weixin_query','wpjam_advanced_weixin_query_catgory_tag', 99);
function wpjam_advanced_weixin_query_catgory_tag($weixin_query_array){
	if(isset($weixin_query_array['s'])){
		global $wpdb;
		$term = $wpdb->get_row("SELECT term_id, taxonomy FROM {$wpdb->prefix}term_taxonomy INNER JOIN {$wpdb->prefix}terms USING ( term_id ) WHERE lower({$wpdb->prefix}terms.name) = '{$weixin_query_array['s']}' OR {$wpdb->prefix}terms.slug = '{$weixin_query_array['s']}' LIMIT 0 , 1");

		if($term){
			$weixin_query_array = wpjam_advanced_weixin_query_new($weixin_query_array);

			if($term->taxonomy == 'category'){
				$weixin_query_array['cat']		= $term->term_id;
			}elseif ($term->taxonomy == 'post_tag') {
				$weixin_query_array['tag_id']	= $term->term_id;
			}
		}
	}
	return $weixin_query_array;
}

function wpjam_basic_filter($original){
	$weixin_robot_basic = weixin_robot_get_basic_option();

	global $wp_current_filter;

	//最后一个才是当前的 filter
	$wpjam_current_filter = $wp_current_filter[count($wp_current_filter)-1];

	if(isset($weixin_robot_basic[$wpjam_current_filter])){
		if($weixin_robot_basic[$wpjam_current_filter ]){
			return $weixin_robot_basic[$wpjam_current_filter];
		}
	}else{
		return $original;
	}
}

// 欢迎回复
add_filter('weixin_custom_keyword','wpjam_welcome_weixin_custom_keyword',10,2);
function wpjam_welcome_weixin_custom_keyword($false,$keyword){
	if($false === false){
		if(in_array( $keyword, array( 'hi', 'h', 'help', '帮助', '您好', '你好', 'subscribe') ) ) {
			global $wechatObj;
			$weixin_welcome = weixin_robot_get_setting('weixin_welcome');
			echo sprintf($wechatObj->get_textTpl(), $weixin_welcome);
			$wechatObj->set_response('welcome');
			return true;
		}elseif($keyword == 'unsubscribe'){
			global $wechatObj;
			$weixin_unsubscribe = "你怎么忍心取消对我的订阅？";
			$wechatObj->set_response('byebye');
			return true;
		}
	}
    return $false;
}

// 关键字太长了
add_filter('weixin_custom_keyword','wpjam_keyword_too_long_weixin_custom_keyword',19,2);
function wpjam_keyword_too_long_weixin_custom_keyword($false,$keyword){
	if($false === false){
		$keyword_length = mb_strwidth(preg_replace('/[\x00-\x7F]/','',$keyword),'utf-8')+str_word_count($keyword)*2;

		$weixin_keyword_allow_length = weixin_robot_get_setting('weixin_keyword_allow_length');
		
		if($keyword_length > $weixin_keyword_allow_length){
			$weixin_keyword_too_long = weixin_robot_get_setting('weixin_keyword_too_long');
			if($weixin_keyword_too_long){
				global $wechatObj;
				echo sprintf($wechatObj->get_textTpl(), $weixin_keyword_too_long);
			}
			$wechatObj->set_response('too-long');

			return true;
		}
	}
    return $false;
}

// 高级回复 
add_filter('weixin_custom_keyword','wpjam_advanced_weixin_custom_keyword',10,2);
function wpjam_advanced_weixin_custom_keyword($false,$keyword){
	if($false === false){
		$weixin_robot_advanced = array_flip(weixin_robot_get_advanced_option());
		if(isset($weixin_robot_advanced[$keyword])){

			add_filter('weixin_query','wpjam_advanced_weixin_query_new');

			if($weixin_robot_advanced[$keyword] == 'new') {
				// 上面已经执行了。
			}elseif($weixin_robot_advanced[$keyword] == 'rand') {
				add_filter('weixin_query','wpjam_advanced_weixin_query_rand');
			}elseif($weixin_robot_advanced[$keyword] == 'hot') {
				add_filter('weixin_query','wpjam_advanced_weixin_query_hot');
			}elseif($weixin_robot_advanced[$keyword] == 'comment') {
				add_filter('weixin_query','wpjam_advanced_weixin_query_comment');
			}elseif($weixin_robot_advanced[$keyword] == 'hot-7') {
				add_filter('weixin_query','wpjam_advanced_weixin_query_hot');
				add_filter('posts_where', 'wpjam_advanced_filter_where_7' );
			}elseif($weixin_robot_advanced[$keyword] == 'comment-7') {
				add_filter('weixin_query','wpjam_advanced_weixin_query_comment');
				add_filter('posts_where', 'wpjam_advanced_filter_where_7' );
			}elseif($weixin_robot_advanced[$keyword] == 'hot-30') {
				add_filter('weixin_query','wpjam_advanced_weixin_query_hot');
				add_filter('posts_where', 'wpjam_advanced_filter_where_30' );
			}elseif($weixin_robot_advanced[$keyword] == 'comment-30') {
				add_filter('weixin_query','wpjam_advanced_weixin_query_comment');
				add_filter('posts_where', 'wpjam_advanced_filter_where_30' );
			}
			
			global $wechatObj;
			$wechatObj->set_response('advanced');
			$wechatObj->query();
			return true;
		}
	}
	return $false;
}

function wpjam_advanced_weixin_query_new($weixin_query_array){
	unset($weixin_query_array['s']);
	$weixin_query_array['post_type']	= 'any';
	
	return $weixin_query_array;
}

function wpjam_advanced_weixin_query_rand($weixin_query_array){
	$weixin_query_array['orderby']		= 'rand';
	
	return $weixin_query_array;
}

function wpjam_advanced_weixin_query_hot($weixin_query_array){
	$weixin_query_array['meta_key']		= 'views';
	$weixin_query_array['orderby']		= 'meta_value_num';

	return $weixin_query_array;
}

function wpjam_advanced_weixin_query_comment($weixin_query_array){
	$weixin_query_array['orderby']		= 'comment_count';

	return $weixin_query_array;
}

function wpjam_advanced_filter_where_7( $where = '' ) {
	$where .= " AND post_date > '" . date('Y-m-d', strtotime('-7 days')) . "'";
	return $where;
}

function wpjam_advanced_filter_where_30( $where = '' ) {
	$where .= " AND post_date > '" . date('Y-m-d', strtotime('-60 days')) . "'";
	return $where;
}

// 语音，图像，地理信息默认处理
add_filter('weixin_custom_keyword','wpjam_default_weixin_custom_keyword',11,2);
function wpjam_default_weixin_custom_keyword($false,$keyword){
	if($false === false){
		if(in_array($keyword, array('[voice]','[location]','[image]') ) ){
			$keyword = str_replace(array('[',']'), '', $keyword);

			$weixin_default = weixin_robot_get_setting('weixin_default_'.$keyword);
			global $wechatObj;
			if($weixin_default){
				echo sprintf($wechatObj->get_textTpl(), $weixin_default);
			}
			$wechatObj->set_response($keyword);
			return true;
		}	
	}
	
    return $false;
}

//自定义回复
function wpjam_get_weixin_custom_keywords(){
	global $wpdb;

	$weixin_custom_keywords = get_transient('weixin_custom_keywords');

	if($weixin_custom_keywords === false){
		$weixin_custom_keywords_table = weixin_robot_get_custom_replies_table();
		$weixin_custom_original_keywords = $wpdb->get_results("SELECT keyword,reply,type FROM $weixin_custom_keywords_table WHERE status = 1",OBJECT_K);
		
		$weixin_custom_keywords = array(); 
		if($weixin_custom_original_keywords){
			foreach ($weixin_custom_original_keywords as $key => $value) {
				if(strpos($key,',')){
					foreach (explode(',', $key) as $new_key) {
						$new_key = strtolower(trim($new_key));
						if($new_key){
							$weixin_custom_keywords[$new_key] = $value;
						}
					}
				}else{
					$weixin_custom_keywords[strtolower($key)] = $value;
				}
			}
		}

		set_transient('weixin_custom_keywords',$weixin_custom_keywords,3600);
	}
	return $weixin_custom_keywords;
}

add_filter('weixin_custom_keyword','wpjam_weixin_custom_keyword',1,2);
function wpjam_weixin_custom_keyword($false,$keyword){
	if($false === false){
		$weixin_custom_keywords = wpjam_get_weixin_custom_keywords();

		if(isset($weixin_custom_keywords[$keyword]) ) {
			$weixin_custom_reply = $weixin_custom_keywords[$keyword];

			if($weixin_custom_reply->type == 'text'){
				global $wechatObj;
				$wechatObj->set_response('custom-text');
				echo sprintf($wechatObj->get_textTpl(), $weixin_custom_reply->reply);
			}elseif($weixin_custom_reply->type == 'img'){
				add_filter('weixin_query','wpjam_custom_weixin_query_img_repy');
				add_filter('weixin_query','wpjam_advanced_weixin_query_new');
				global $wechatObj;
				$wechatObj->set_response('custom-img');
				$wechatObj->query($keyword);
			}
			return true;
		}
		
	}
	return $false;
	
}

function wpjam_custom_weixin_query_img_repy($weixin_query_array){
	$weixin_custom_keywords = wpjam_get_weixin_custom_keywords();
	$weixin_custom_reply = $weixin_custom_keywords[$weixin_query_array['s']];
	$post_ids = explode(',', $weixin_custom_reply->reply);

	$weixin_query_array['post__in']		= $post_ids;
	$weixin_query_array['orderby']		= 'post__in';

	return $weixin_query_array;
}

add_action("wp_footer","wpjam_weixin_robot_share_footer",99);
function wpjam_weixin_robot_share_footer(){
	if(is_singular() && is_weixin()){
	global $post;
?>
<script type="text/javascript">
function htmlEncode(e) {
    return e.replace(/&/g, "&amp;").replace(/ /g, "&nbsp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br />").replace(/"/g, "&quot;")
}

function htmlDecode(e) {
    return e.replace(/&#39;/g, "'").replace(/<br\s*(\/)?\s*>/g, "\n").replace(/&nbsp;/g, " ").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"').replace(/&amp;/g, "&")
}

var 
	appId	= "",
	img		= "<?php echo get_post_weixin_thumb($post,array(120,120)); ?>",
	link	= "<?php the_permalink($post->ID);?>",
	title	= htmlDecode("<?php echo $post->post_title; ?>"),
	desc	= htmlDecode("<?php echo get_post_excerpt($post); ?>"),
	fakeid	= "";

	desc = desc || link;
(function(){
	var onBridgeReady=function(){
		// 发送给好友; 
		WeixinJSBridge.on('menu:share:appmessage', function(argv){
			WeixinJSBridge.invoke('sendAppMessage',{
				"appid":		appId,
				"img_url":		img,
				"img_width":	"120",
				"img_height":	"120",
				"link":			link,
				"desc":			desc,
				"title":		title
			}, function(res){<?php do_action('weixin_share','SendAppMessage');?>});
		});
		// 分享到朋友圈;
		WeixinJSBridge.on('menu:share:timeline', function(argv){
			<?php //do_action('weixin_share','ShareTimeline');?>
			WeixinJSBridge.invoke('shareTimeline',{
				"img_url":		img,
				"img_width":	"120",
				"img_height": 	"120",
				"link":			link,
				"desc":			desc,
				"title":		title
			}, function(res){<?php do_action('weixin_share','ShareTimeline');?>});
		});
		// 分享到微博;
		WeixinJSBridge.on('menu:share:weibo', function(argv){
			WeixinJSBridge.invoke('shareWeibo',{
				"content":		title+' '+link,
				"url":			link
			}, function(res){<?php do_action('weixin_share','ShareWeibo');?>});
		});
		// 分享到Facebook
		WeixinJSBridge.on('menu:share:facebook', function(argv){
			<?php do_action('weixin_share','ShareFB');?>
			WeixinJSBridge.invoke('shareFB',{
				"img_url":		img,
				"img_width":	"120",
				"img_height":	"120",
				"link":			link,
				"desc":			desc,
				"title":		title
			}, function(res){});
		});
	};
	if(document.addEventListener){
		document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
	}else if(document.attachEvent){
		document.attachEvent('WeixinJSBridgeReady',		onBridgeReady);
		document.attachEvent('onWeixinJSBridgeReady',	onBridgeReady);
	}
})();
</script>
<?php 
	}
}

add_filter('weixin_thumb','wpjam_weixin_thumb_filter',10,3);
function wpjam_weixin_thumb_filter($thumb,$size,$post){
	if(function_exists('wpjam_get_post_thumbnail_src')){
		if(wpjam_has_post_thumbnail()){
			$thumb = wpjam_get_post_thumbnail_src($post, $size);
		}	
	}
	return $thumb;
}
