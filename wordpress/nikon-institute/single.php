<?php
/**
 * Single article / news post — editorial layout.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$ni_id      = get_the_ID();
	$ni_ar      = ni_is_rtl();
	$ni_author  = get_post_meta( $ni_id, '_ni_author', true );
	$ni_role    = get_post_meta( $ni_id, '_ni_author_role', true );
	$ni_words   = str_word_count( wp_strip_all_tags( get_the_content() ) );
	$ni_minutes = max( 1, (int) round( $ni_words / 220 ) );
	$ni_cats    = get_the_category();
	$ni_cat     = $ni_cats ? $ni_cats[0] : null;
	$ni_thumb   = has_post_thumbnail() ? get_the_post_thumbnail_url( $ni_id, 'large' ) : '';
	$ni_alt     = $ni_thumb ? get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) : '';
	$ni_url     = rawurlencode( get_permalink() );
	$ni_title   = rawurlencode( get_the_title() );
	$ni_initial = '';
	if ( $ni_author ) {
		$ni_parts = preg_split( '/\s+/', trim( preg_replace( '/^(Dr|Prof|Mr|Mrs|Ms|Eng)\.?\s+/i', '', $ni_author ) ) );
		$ni_initial = strtoupper( mb_substr( $ni_parts[0], 0, 1 ) . ( count( $ni_parts ) > 1 ? mb_substr( end( $ni_parts ), 0, 1 ) : '' ) );
	}
	$ni_arrow = $ni_ar ? '←' : '→';
	?>
	<div class="ni-progress" aria-hidden="true"><span></span></div>
	<main id="main" class="ni-post">
	<section class="ni-ahero<?php echo $ni_thumb ? '' : ' ni-ahero--plain'; ?>">
		<div class="ni-ahero__rings"><div></div><div></div></div>
		<div class="ni-ahero__in">
			<div class="ni-ahero__text">
				<div class="ni-crumbs">
					<a href="<?php echo esc_url( ni_url( 'home' ) ); ?>"><?php echo esc_html( ni_t( 'home' ) ); ?></a>
					<span class="ni-crumbs__sep">/</span>
					<?php if ( ni_is_article() ) : ?>
						<a href="<?php echo esc_url( ni_url( 'grp_menuKnow' ) ); ?>"><?php echo esc_html( ni_l( 'Knowledge', 'المعرفة' ) ); ?></a>
						<span class="ni-crumbs__sep">/</span>
						<a class="ni-crumbs__cur" href="<?php echo esc_url( ni_url( 'publications' ) ); ?>"><?php echo esc_html( ni_l( 'Publications', 'المنشورات' ) ); ?></a>
					<?php else : ?>
						<a class="ni-crumbs__cur" href="<?php echo esc_url( ni_url( 'newsItems' ) ); ?>"><?php echo esc_html( ni_l( 'Media Center', 'المركز الإعلامي' ) ); ?></a>
					<?php endif; ?>
				</div>
				<?php if ( $ni_cat && 'uncategorized' !== $ni_cat->slug ) : ?>
					<span class="ni-ahero__cat"><?php echo esc_html( $ni_cat->name ); ?></span>
				<?php endif; ?>
				<h1 class="ni-ahero__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="ni-ahero__stand"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="ni-ahero__meta">
					<?php if ( $ni_author ) : ?>
						<span class="ni-avatar" aria-hidden="true"><?php echo esc_html( $ni_initial ); ?></span>
					<?php else : ?>
						<span class="ni-avatar ni-avatar--logo" aria-hidden="true"><i class="ph ph-newspaper"></i></span>
					<?php endif; ?>
					<div>
						<strong><?php echo esc_html( $ni_author ? $ni_author : 'NIKON Institute' ); ?></strong>
						<span><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( $ni_ar ? sprintf( '%d دقائق للقراءة', $ni_minutes ) : sprintf( '%d min read', $ni_minutes ) ); ?></span>
					</div>
				</div>
			</div>
			<?php if ( $ni_thumb ) : ?>
				<figure class="ni-ahero__img"><img src="<?php echo esc_url( $ni_thumb ); ?>" alt="<?php echo esc_attr( $ni_alt ); ?>"></figure>
			<?php endif; ?>
		</div>
	</section>

	<section class="ni-abody">
		<div class="ni-abody__in">
			<aside class="ni-aside"><div class="ni-aside__in">
				<div class="ni-toc" hidden>
					<div class="ni-aside__label"><?php echo esc_html( ni_l( 'In this article', 'في هذا المقال' ) ); ?></div>
					<ol class="ni-toc__list"></ol>
				</div>
				<div class="ni-share">
					<div class="ni-aside__label"><?php echo esc_html( ni_l( 'Share', 'شارك' ) ); ?></div>
					<div class="ni-share__row">
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $ni_url ); ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="ph ph-facebook-logo"></i></a>
						<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $ni_url ); ?>&amp;text=<?php echo esc_attr( $ni_title ); ?>" target="_blank" rel="noopener" aria-label="X"><i class="ph ph-x-logo"></i></a>
						<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_attr( $ni_url ); ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="ph ph-linkedin-logo"></i></a>
						<a href="https://wa.me/?text=<?php echo esc_attr( $ni_title . '%20' . $ni_url ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="ph ph-whatsapp-logo"></i></a>
						<button type="button" class="ni-copy ni-share__copy" data-copy="<?php echo esc_url( get_permalink() ); ?>" data-done="<?php echo esc_attr( ni_l( 'Link copied', 'تم نسخ الرابط' ) ); ?>" aria-label="<?php echo esc_attr( ni_l( 'Copy link', 'نسخ الرابط' ) ); ?>"><i class="ph ph-link"></i><span><?php echo esc_html( ni_l( 'Copy link', 'نسخ الرابط' ) ); ?></span></button>
					</div>
				</div>
			</div></aside>
			<article class="ni-article ni-prose">
				<?php the_content(); ?>
				<?php if ( $ni_author ) : ?>
					<div class="ni-authorbox">
						<span class="ni-avatar ni-avatar--lg" aria-hidden="true"><?php echo esc_html( $ni_initial ); ?></span>
						<div>
							<div class="ni-aside__label"><?php echo esc_html( ni_l( 'About the author', 'عن الكاتب' ) ); ?></div>
							<strong><?php echo esc_html( $ni_author ); ?></strong>
							<?php if ( $ni_role ) : ?><p><?php echo esc_html( $ni_role ); ?></p><?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</article>
		</div>
	</section>

	<?php
	$ni_more = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'post__not_in'   => array( $ni_id ),
		'meta_key'       => '_ni_lang', // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_value'     => ni_lang(),  // phpcs:ignore WordPress.DB.SlowDBQuery
	) );
	if ( $ni_more->have_posts() ) :
		?>
		<section class="ni-more">
			<div class="ni-more__in">
				<div class="ni-more__head">
					<div><div class="ni-eyebrow"><span></span><?php echo esc_html( ni_l( 'Keep reading', 'تابع القراءة' ) ); ?></div>
					<h2 class="ni-wa-h2"><?php echo esc_html( ni_l( 'More from the Institute', 'المزيد من المعهد' ) ); ?></h2></div>
					<a class="ni-cta__btn" href="<?php echo esc_url( ni_url( ni_is_article() ? 'publications' : 'newsItems' ) ); ?>"><?php echo esc_html( ni_is_article() ? ni_l( 'All publications', 'كل المنشورات' ) : ni_l( 'All news & articles', 'كل الأخبار والمقالات' ) ); ?> <?php echo esc_html( $ni_arrow ); ?></a>
				</div>
				<div class="ni-more__grid">
					<?php
					while ( $ni_more->have_posts() ) :
						$ni_more->the_post();
						$ni_t = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
						?>
						<a class="ni-mcard" href="<?php the_permalink(); ?>">
							<span class="ni-mcard__img"><?php if ( $ni_t ) : ?><img src="<?php echo esc_url( $ni_t ); ?>" alt="" loading="lazy"><?php else : ?><span class="ni-mcard__mark">NIKON</span><?php endif; ?></span>
							<span class="ni-mcard__body"><span class="ni-mcard__date"><?php echo esc_html( get_the_date() ); ?></span><span class="ni-mcard__title"><?php the_title(); ?></span><span class="ni-mcard__more"><?php echo esc_html( ni_l( 'Read article', 'اقرأ المقال' ) ); ?> <?php echo esc_html( $ni_arrow ); ?></span></span>
						</a>
					<?php endwhile; ?>
				</div>
			</div>
		</section>
		<?php
		wp_reset_postdata();
	endif;
	?>
	</main>
	<?php
endwhile;
get_footer();
