<div class="sidebar">
	<ul>
         
         <li>
            <!-- <h2><?php //_e('Search'); ?></h2> -->
             <ul>
            <li><form action="<?php bloginfo('url'); ?>" method="GET">
            <input type="text" value="搜索..." name="s" id="ls" class="searchfield" onfocus="if (this.value == '搜索...') {this.value = '';}" onblur="if (this.value == '') {this.value = '搜索...';}" />
			<input type="hidden" name="site" value="<?php echo isset($_GET['site'])?$_GET['site']:'';?>" class="searchbutton" />
            <input type="submit" value="搜索" class="searchbutton" />
            </form></li>
            </ul>
        </li>

        
	</ul>
    </div>