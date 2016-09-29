<?php get_header(); ?>
<section id="post">
	<?php while (have_posts()) : the_post(); ?>
	<article>
		<header>
			<div class="time">
				<time datetime="<?php the_time('Y-m-d'); ?>" pubdate>
					<div class="day"><?php the_time('d'); ?></div>
					<div class="mon"><?php the_time('Y-m'); ?></div>
				</time>
			</div>
			<h1><?php the_title(); ?></h1>
			<div class="tags"><?php the_tags(); ?></div>
			<div class="addthis_toolbox" addthis:url="<?php the_permalink() ?>" addthis:title="<?php the_title(); ?>">
				<a class="addthis_button_twitter"></a>
				<a class="addthis_button_facebook"></a>
				<a class="addthis_button_sinaweibo"></a>
			</div>
		</header>
		<div id="post_content"><?php the_content(); ?></div>
	</article>
	<?php endwhile; ?>
	<aside id="random">
		<ul>
    		<?php $rand_posts = get_posts('numberposts=6&orderby=rand');  foreach( $rand_posts as $post ) : ?>
    		<li><a href="<?php the_permalink() ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo wp_catch_first_image('m'); ?>&amp;w=180&amp;h=120&amp;zc=1" alt="<?php the_title(); ?>" tile="<?php the_title(); ?>" /></a></li>
    		<?php endforeach; wp_reset_query(); ?>    		
		</ul>
	</aside>
	<?php comments_template(); ?>
</section>
<?php get_footer(); ?>