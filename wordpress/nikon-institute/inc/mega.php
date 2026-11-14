<?php
/**
 * Desktop mega menu: for each top-level menu item with children, a panel with
 * the sub-pages (left), the hovered page's summary (middle) and a photo card
 * with a Donate call to action (right). Built from the primary menu, so it
 * stays in sync with Appearance → Menus.
 *
 * @package nikon-institute
 */

/** Short summary of a page: first meaningful paragraph of its content. */
function ni_page_summary( $id, $max = 170 ) {
	$cache = get_post_meta( $id, '_ni_summary', true );
	$stamp = get_post_field( 'post_modified_gmt', $id );
	if ( is_array( $cache ) && ( $cache['t'] ?? '' ) === $stamp ) {
		return $cache['s'];
	}
	$s = '';
	if ( has_excerpt( $id ) ) {
		$s = get_the_excerpt( $id );
	} else {
		$html = (string) get_post_field( 'post_content', $id );
		if ( preg_match_all( '#<p\b[^>]*>(.*?)</p>#is', $html, $m ) ) {
			foreach ( $m[1] as $p ) {
				$t = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $p ) ) );
				if ( mb_strlen( $t ) >= 40 ) {
					$s = $t;
					break;
				}
			}
		}
	}
	if ( mb_strlen( $s ) > $max ) {
		$s   = mb_substr( $s, 0, $max );
		$cut = mb_strrpos( $s, ' ' );
		$s   = rtrim( mb_substr( $s, 0, $cut ? $cut : $max ), " ,.;:—–-" ) . '…';
	}
	update_post_meta( $id, '_ni_summary', array( 't' => $stamp, 's' => $s ) );
	return $s;
}

/** Photo for a menu item's page (its featured image), falling back to its section's. */
function ni_menu_photo( $item, $fallback = '' ) {
	if ( 'post_type' === $item->type ) {
		$u = get_the_post_thumbnail_url( (int) $item->object_id, 'large' );
		if ( $u ) {
			return $u;
		}
	}
	return $fallback;
}

/** Echo the mega panels for a menu location. */
function ni_mega_menu( $location ) {
	$locs = get_nav_menu_locations();
	if ( empty( $locs[ $location ] ) ) {
		return;
	}
	$items = wp_get_nav_menu_items( $locs[ $location ] );
	if ( ! $items ) {
		return;
	}
	$top  = array();
	$kids = array();
	foreach ( $items as $it ) {
		if ( ! (int) $it->menu_item_parent ) {
			$top[] = $it;
		} else {
			$kids[ (int) $it->menu_item_parent ][] = $it;
		}
	}
	$ar     = ni_is_rtl();
	$arrow  = $ar ? '←' : '→';
	$donate = ni_url( 'donatePage' );
	foreach ( $top as $t ) {
		if ( empty( $kids[ $t->ID ] ) ) {
			continue;
		}
		$section_photo = ni_menu_photo( $t );
		$list          = $kids[ $t->ID ];
		?>
		<div class="ni-mega" id="ni-mega-<?php echo (int) $t->ID; ?>" data-for="menu-item-<?php echo (int) $t->ID; ?>" hidden>
			<span class="ni-mega__caret" aria-hidden="true"></span>
			<div class="ni-mega__in">
				<ul class="ni-mega__list" role="list">
					<?php foreach ( $list as $i => $k ) : ?>
						<li><a class="ni-mega__item<?php echo 0 === $i ? ' is-active' : ''; ?>" href="<?php echo esc_url( $k->url ); ?>" data-pane="<?php echo (int) $i; ?>"><?php echo esc_html( $k->title ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<div class="ni-mega__panes">
					<?php foreach ( $list as $i => $k ) : ?>
						<div class="ni-mega__pane<?php echo 0 === $i ? ' is-active' : ''; ?>" data-pane="<?php echo (int) $i; ?>">
							<div class="ni-mega__kicker"><?php echo esc_html( $t->title ); ?></div>
							<h3 class="ni-mega__title"><a href="<?php echo esc_url( $k->url ); ?>"><?php echo esc_html( $k->title ); ?></a></h3>
							<?php $sum = 'post_type' === $k->type ? ni_page_summary( (int) $k->object_id ) : ''; ?>
							<?php if ( $sum ) : ?><p class="ni-mega__text"><?php echo esc_html( $sum ); ?></p><?php endif; ?>
							<div class="ni-mega__links">
								<a class="ni-mega__go" href="<?php echo esc_url( $k->url ); ?>"><?php echo esc_html( ni_l( 'Explore', 'استكشف' ) . ' ' . $k->title ); ?> <span><?php echo esc_html( $arrow ); ?></span></a>
								<a class="ni-mega__ov" href="<?php echo esc_url( $t->url ); ?>"><?php echo esc_html( sprintf( ni_l( '%s overview', 'نظرة عامة: %s' ), $t->title ) ); ?> <span><?php echo esc_html( $arrow ); ?></span></a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="ni-mega__card">
					<?php foreach ( $list as $i => $k ) : ?>
						<?php $ph = ni_menu_photo( $k, $section_photo ); ?>
						<?php if ( $ph ) : ?><img class="ni-mega__photo<?php echo 0 === $i ? ' is-active' : ''; ?>" data-pane="<?php echo (int) $i; ?>" data-src="<?php echo esc_url( $ph ); ?>" alt=""><?php endif; ?>
					<?php endforeach; ?>
					<div class="ni-mega__cta">
						<strong><?php echo esc_html( ni_l( 'Help us drive change', 'ساعدنا في صنع التغيير' ) ); ?></strong>
						<a class="ni-mega__donate" href="<?php echo esc_url( $donate ); ?>"><?php echo esc_html( ni_t( 'donate' ) ); ?> <i class="ph ph-heart"></i></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
