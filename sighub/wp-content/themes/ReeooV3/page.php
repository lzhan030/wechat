<?php if(!isset($_GET['beIframe']))get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article id="page">
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>
</article>
<?php endwhile; endif; ?>
<?php if(!isset($_GET['beIframe']))get_footer(); ?>