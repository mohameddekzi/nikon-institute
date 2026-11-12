<?php
/**
 * Inner pages: navy page header (breadcrumb, title, section tabs) + content.
 */
get_header();
while ( have_posts() ) :
	the_post();
	$ni_id     = get_the_ID();
	$ni_parent = wp_get_post_parent_id( $ni_id );
	$ni_home   = (int) get_option( 'ni_home_ar' );
	if ( $ni_parent === $ni_home ) {
		$ni_parent = 0; // top-level Arabic page
	}
	$ni_h1    = get_post_meta( $ni_id, '_ni_h1', true );
	$ni_h1    = $ni_h1 ? $ni_h1 : get_the_title();
	$ni_crumb = $ni_parent ? get_the_title( $ni_parent ) : get_post_meta( $ni_id, '_ni_crumb', true );

	// Section tabs: visible siblings (or children, on a section landing page).
	$ni_tab_parent = $ni_parent ? $ni_parent : 0;
	$ni_tabs       = array();
	if ( $ni_tab_parent ) {
		$ni_tabs = get_pages( array(
			'parent'      => $ni_tab_parent,
			'sort_column' => 'menu_order',
			'meta_key'    => '_ni_tab', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => '1',       // phpcs:ignore WordPress.DB.SlowDBQuery
		) );
	}
	?>
	<main id="main">
	<section class="ni-phead">
		<div class="ni-phead__rings"><div></div><div></div></div>
		<div class="ni-phead__in">
			<div class="ni-crumbs">
				<a href="<?php echo esc_url( ni_url( 'home' ) ); ?>"><?php echo esc_html( ni_t( 'home' ) ); ?></a>
				<?php if ( $ni_crumb ) : ?>
					<span class="ni-crumbs__sep">/</span>
					<?php if ( $ni_parent ) : ?>
						<a class="ni-crumbs__cur" href="<?php echo esc_url( get_permalink( $ni_parent ) ); ?>"><?php echo esc_html( $ni_crumb ); ?></a>
					<?php else : ?>
						<span class="ni-crumbs__cur"><?php echo esc_html( $ni_crumb ); ?></span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<h1 class="ni-phead__title"><?php echo esc_html( $ni_h1 ); ?></h1>
			<?php if ( $ni_tabs ) : ?>
				<div class="ni-tabs" role="tablist">
					<?php foreach ( $ni_tabs as $ni_tab ) : ?>
						<a role="tab" class="ni-tab<?php echo $ni_tab->ID === $ni_id ? ' is-active' : ''; ?>" aria-selected="<?php echo $ni_tab->ID === $ni_id ? 'true' : 'false'; ?>" href="<?php echo esc_url( get_permalink( $ni_tab ) ); ?>"><?php echo esc_html( get_the_title( $ni_tab ) ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="ni-tabs ni-tabs--empty"></div>
			<?php endif; ?>
		</div>
	</section>
	<section class="ni-pbody">
		<div class="ni-pbody__in">
			<?php the_content(); ?>
		</div>
	</section>
	</main>
	<?php
endwhile;
get_footer();
