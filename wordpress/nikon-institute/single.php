<?php
/**
 * Single news post.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$ni_news = ni_url( 'newsItems' );
	?>
	<main id="main">
	<section class="ni-phead">
		<div class="ni-phead__rings"><div></div><div></div></div>
		<div class="ni-phead__in">
			<div class="ni-crumbs">
				<a href="<?php echo esc_url( ni_url( 'home' ) ); ?>"><?php echo esc_html( ni_t( 'home' ) ); ?></a>
				<span class="ni-crumbs__sep">/</span>
				<a class="ni-crumbs__cur" href="<?php echo esc_url( $ni_news ); ?>"><?php echo esc_html( ni_t( 'news' ) ); ?></a>
			</div>
			<h1 class="ni-phead__title ni-phead__title--post"><?php the_title(); ?></h1>
			<div class="ni-post-date"><?php echo esc_html( get_the_date() ); ?></div>
			<div class="ni-tabs ni-tabs--empty"></div>
		</div>
	</section>
	<section class="ni-pbody">
		<div class="ni-pbody__in ni-prose">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="ni-post-thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
			<?php endif; ?>
			<?php the_content(); ?>
		</div>
	</section>
	</main>
	<?php
endwhile;
get_footer();
