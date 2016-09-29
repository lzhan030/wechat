<?php get_header(); ?> 
             
        <div class="entry"> 
        

                                <div id="container">
                    
                                    <?php if(have_posts()) : ?>
                                    <?php while(have_posts()) : the_post(); ?>
                    
                                        <div class="post" id="post-<?php the_ID(); ?>">


                                            <div class="title">
                                                <h2><a href="<?php the_permalink(); $siteId = $_SESSION['orangeSite'];echo '&site='.$siteId; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                                                <div class="content"><?php the_content(); ?></div>
                                            </div>    
                                            
                                            <div class="date">
                                                发布于 <?php the_time('F j, Y') ?>
                                            </div>     
                                              	
                    <br clear="all" />
                     <?php
                     global $wpdb, $table_prefix;
                     $tableName = $table_prefix.'orangesitemeta';
                     //$siteId = ($_GET['site'] == ''||$_GET['site']==null)? '1': $_GET['site'];
                     $siteId = $_SESSION['orangeSite'];//$siteId  = $_COOKIE['orangeSite'];
                     $keyName = 'mobilethemeIsShowEditor';
                     $sql = "SELECT `site_value` FROM `".$tableName."` WHERE `site_id`='".$siteId."' and `site_key`='".$keyName."'";
                     $sitemeta = $wpdb->get_row($sql);
                     $isShowEditor = $sitemeta->site_value;
                     if(empty($isShowEditor))
                     {
                        // $isShowEditor  = 'true';
                         //$wpdb->query("INSERT INTO `".$tableName."`( `site_id`, `site_key`,`site_value`) VALUE('".$siteId."', '".$keyName."','".$isShowEditor."')");
                     }
                     ?>
                    <?php if($isShowEditor == 'true')comments_template(); ?>
                                        </div>                    
                    
                                    <?php endwhile; ?>
                   
                                    <?php endif; ?>
                                    
                                </div>
         
        
        </div> <!--entry-->
        
  
        <?php //get_sidebar(); ?><!--remove search function-->

        <?php get_footer(); ?>        