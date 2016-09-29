<div class="sidebar">
	<ul>
	<?php //if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
         
         <li>
            <!-- <h2><?php //_e('Search'); ?></h2> -->
             <ul>
            <li><form action="<?php bloginfo('url');?>#wechat_redirect" method="GET">
            <input type="text" value="搜索..." name="s" id="ls" class="searchfield" onfocus="if (this.value == '搜索...') {this.value = '';}" onblur="if (this.value == '') {this.value = '搜索...';}" />
			<input type="hidden" name="site" value="<?php echo isset($_GET['site'])?$_GET['site']:'';?>" class="searchbutton" />
			<input type="hidden" name="weid" value="<?php echo isset($_GET['weid'])?$_GET['weid']:'';?>"  />
			<input type="hidden" name="gweid" value="<?php echo isset($_GET['gweid'])?$_GET['gweid']:'';?>"  />
			<!--janeen-add-20140429-->
			<!--20140430<input type="hidden" name="mid" value="<?php echo isset($_GET['mid'])?$_GET['mid']:'';?>"  />
			<input type="hidden" name="weid" value="<?php echo isset($_GET['weid'])?$_GET['weid']:'';?>"  />
			<input type="hidden" name="fromuser" value="<?php echo isset($_GET['fromuser'])?$_GET['fromuser']:'';?>"  />
			<input type="hidden" name="auth" value="<?php echo isset($_GET['auth'])?$_GET['auth']:'';?>"  />20140430-->
			<!--janeen-end-20140429-->
            <input type="submit" value="搜索" class="searchbutton" />
            </form></li>
            </ul>
        </li>
        
        <!-- <li>
      		  <h2><?php //_e('Pages'); ?></h2>
              <ul>
              <li><a href="<?php //bloginfo('url'); ?>">Home</a></li>
              <?php //wp_list_pages('depth=1&title_li='); ?>
              </ul>
           	  
        	</li>

        <li>
        <h2><?php //_e('Categories'); ?></h2>
            <ul>
            <?php //wp_list_cats('sort_column=name&hierarchical=0'); ?>
            </ul>
        </li>
      	
        <li>
        <h2><?php //_e('Archives'); ?></h2>
            <ul>
            <?php //wp_get_archives('type=monthly'); ?>
            </ul>
        </li>
        
        <li>
        <h2><?php //_e('Links'); ?></h2>
            <ul>
             <?php //get_links(2, '<li>', '</li>', '', TRUE, 'url', FALSE); ?>
             </ul>
        </li>-->

        
	<?php //endif; ?>
	</ul>
    </div>