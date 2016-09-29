<?php
define('WECHAT_PLUGIN_URL', plugins_url('', __FILE__));
include(WECHAT_PATH.'wechat_admin/admin_page_class.php');
if(isset($_GET['page']) && ($_GET['page'] == 'Orange-Wechat-msgPage' || $_GET['page'] == 'Orange-Wechat-settingPage'))
    add_action('admin_head', 'wechat_admin_head');
add_action('admin_menu', 'wechat_menu');


function wechat_admin_head()
{
    $file = plugin_basename(__FILE__);
    $fileurl = plugin_dir_url(__FILE__);
    echo '<link rel="stylesheet" href="'.$fileurl.'wechat_admin/css/base199134.css type="text/css" media="screen" />';
    echo '<script type="text/javascript" src="'.$fileurl.'wechat_admin/js/zh_CN.js"></script>';
    if($_GET['page'] == 'Orange-Wechat-settingPage')
    {

    }
}

function wechat_menu()
{
    add_menu_page('微信公众号', '微信公众号',	'manage_options',	'OrangeWeChat',	'OrangeWeChat_basic_page',	WECHAT_PLUGIN_URL.'/al-16.ico');
    add_submenu_page( 'OrangeWeChat', '高级回复', '高级回复', 'manage_options', 'Orange-Wechat-msgPage', 'Orange_WeChat_MsgPage');
    add_submenu_page( 'OrangeWeChat', '自定义回复', '自定义回复', 'manage_options', 'Orange-Wechat-settingPage', 'Orange_WeChat_SettingPage');
    add_submenu_page( 'OrangeWeChat', '自定义菜单', '自定义菜单', 'manage_options', 'Orange-Wechat-menuPage', 'Orange_WeChat_MenuPage');
    add_submenu_page( 'OrangeWeChat', '统计功能', '统计功能', 'manage_options', 'Orange-Wechat-countPage', 'Orange_WeChat_CountPage');
}
//产生HTML代码，用于后台使用
function OrangeWeChat_basic_page(){
    ECHO "微信公众号页面";
    echo get_option('Orange_WeChat_DebugLog');
}

function Orange_WeChat_MsgPage(){
	if(isset($_POST['submit'])){
		if(wp_verify_nonce($_POST['_wpnonce'],'OrangeWeChat_admin_options-update')){
			//把copyright text存放到了数据库中
			update_option('f_OrangeTitle', stripslashes($_POST['f_Title']));
			update_option('f_OrangeDescription', stripslashes($_POST['f_Description']));
			update_option('f_OrangePicUrl', stripslashes($_POST['f_PicUrl']));
			update_option('f_OrangeUrl', stripslashes($_POST['f_Url']));
			echo '<div class="updated"><p>'._('success!').'</p></div>';
		}
		else{
			echo '<div class="error"><p>'._('fail!').'</p></div>';
		}
	}
	?>
	<div class="wrap">
		<?php screen_icon();?>
		<h2>OrangeWeChat插件配置</h2>
        <div id="plugin-description"  style="margin:10px 0; padding:5px;background-color:#FFFEEB;">
		    <p><?php _e('配置公众账号在微信客户端回复的图文内容')?></p>
        </div>

        <?php
        if(isset($_POST['action'])) {
            unset($_POST['submit']);
            echo '更新';
            $action = $_POST['action'];
            unset($_POST['action']);

            if($action == 'update') {
                $from_id = $_POST['fromId'];
                $title = $_POST['title'];
                $decs = $_POST['decs'];
                $picUrl = $_POST['picUrl'];
                $url = $_POST['url'];
                //过滤
                unset($_POST);
                if($from_id)
                {
                    update_option('OrangeTitle_'.$from_id, $title);
                    update_option('OrangeDescription_'.$from_id, $decs);
                    update_option('OrangePicUrl_'.$from_id, $picUrl);
                    update_option('OrangeUrl_'.$from_id, $url);
                }
                echo '<div id="message" class="updated fade"><p><strong>'.$from_id.'  Settings saved.</strong></p></div>';
            } else if($action == 'delete') {
                ;
            }
        }
        ?>
        <table class="widefat post fixed" cellspacing="0">
            <thead>
            <tr>
                <th class="manage-column" scope="col" width="30">序号</th>
                <th class="manage-column" scope="col" width="80">标题</th>
                <th class="manage-column" scope="col" width="100">描述</th>
                <th class="manage-column" scope="col"width="180">图片链接</th>
                <th class="manage-column" scope="col"width="180">链接</th>
                <th class="manage-column" scope="col"width="80">操作</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th class="manage-column" scope="col">序号</th>
                <th class="manage-column" scope="col">标题</th>
                <th class="manage-column" scope="col">描述</th>
                <th class="manage-column" scope="col">图片链接</th>
                <th class="manage-column" scope="col">链接</th>
                <th class="manage-column" scope="col">操作</th>
            </tr>
            </tfoot>
            <?php update_option('OrangeNewsAccount', 4);  //多图文数量 ?>
            <?php $rowclass=null; for( $from_id = 1;$from_id <= get_option('OrangeNewsAccount');$from_id++){ ?>
                <?php $rowclass = 'alternate' == $rowclass ? '' : 'alternate'; ?>
                <form method="post" action="" class="" id="form<?php echo '_'.$from_id; ?>">
                    <tr valign="top" class="<?php echo $rowclass; ?> author-self status-publish iedit">
                        <th scope="row"><?php _e($from_id) ?></th>
                        <td>
                            <input type="text" name="title" id="title-<?php echo '_'.$from_id; ?>" class="" value="<?php echo get_option('OrangeTitle_'.$from_id); ?>" style="width:8em;" />
                        </td>
                        <td>
                            <input type="text" name="decs" id="decs-<?php echo '_'.$from_id; ?>" class="" value="<?php echo get_option('OrangeDescription_'.$from_id); ?>" style="width:12em;" />
                        </td>
                        <td>
                            <input type="text" name="picUrl" id="picUrl-<?php echo '_'.$from_id; ?>" class="" value="<?php echo get_option('OrangePicUrl_'.$from_id); ?>" style="width:20em;" />
                        </td>
                        <td>
                            <input type="text" name="url" id="url-<?php echo '_'.$from_id; ?>" class="" value="<?php echo get_option('OrangeUrl_'.$from_id); ?>" style="width:20em;" />
                        </td>
                        <td>
                            <input type="hidden" name="fromId" id="fromId<?php echo '_'.$from_id; ?>" value="<?php echo $from_id;?>" />
                            <input type="hidden" name="action" id="action<?php echo '_'.$from_id; ?>" value="update" />

                            <!--<a type="submit" onclick="$('#form<?php echo '_'.$from_id; ?>').submit();" class="button submit">更新</a>
					-->
                            <input type="submit" value="更新"/>
                        </td>
                    </tr>
                </form>
            <?php } ?>
        </table>
    </div>
	<?php
}
function Orange_WeChat_SettingPage(){ECHO "自定义回复页面";
    echo '<iframe id="weixin_if" src="https://mp.weixin.qq.com/" width="900" height="600" border=1></iframe>';
}
function Orange_WeChat_MenuPage(){ECHO "自定义菜单页面";}
function Orange_WeChat_CountPage(){ECHO "统计功能页面";}
?>