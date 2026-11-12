<?php
/**
 * Fallback listing (blog index, archives, search).
 */
get_header();
?>
<main id="main">
<section class="ni-phead">
	<div class="ni-phead__rings"><div></div><div></div></div>
	<div class="ni-phead__in">
		<div class="ni-crumbs"><a href="<?php echo esc_url( ni_url( 'home' ) ); ?>"><?php echo esc_html( ni_t( 'home' ) ); ?></a></div>
		<h1 class="ni-phead__title"><?php echo esc_html( is_search() ? get_search_query() : ( is_archive() ? get_the_archive_title() : ni_t( 'news' ) ) ); ?></h1>
		<div class="ni-tabs ni-tabs--empty"></div>
	</div>
</section>
<section class="ni-pbody">
	<div class="ni-pbody__in">
		<div class="ni-news-list">
			<?php while ( have_posts() ) : the_post(); ?>
				<a class="ni-news-item" href="<?php the_permalink(); ?>"><span class="ni-news-date"><?php echo esc_html( get_the_date() ); ?></span><span class="ni-news-title"><?php the_title(); ?></span><span class="ni-news-arrow"><?php echo ni_is_rtl() ? '←' : '→'; ?></span></a>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	</div>
</section>
</main>
<?php
get_footer();
