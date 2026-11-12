<?php
/**
 * One-click content import: pages (EN + AR), news posts, media, menus, front page.
 * Safe to run more than once — existing pages are matched by key and updated.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_switch_theme', function () {
	if ( ! get_option( 'ni_imported' ) ) {
		ni_import_content( false );
	}
} );

/**
 * Upload a theme image to the Media Library once; returns its URL.
 */
function ni_import_media( $file ) {
	$media = get_option( 'ni_media', array() );
	if ( ! empty( $media[ $file ] ) && get_post( $media[ $file ] ) ) {
		return wp_get_attachment_url( $media[ $file ] );
	}
	$path = NI_DIR . '/assets/img/' . $file;
	if ( ! file_exists( $path ) ) {
		return ni_asset( 'img/' . $file );
	}
	$upload = wp_upload_bits( 'nikon-' . $file, null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $upload['error'] ) ) {
		return ni_asset( 'img/' . $file );
	}
	$type = wp_check_filetype( $upload['file'] );
	$id   = wp_insert_attachment( array(
		'post_mime_type' => $type['type'],
		'post_title'     => 'Nikon Institute — ' . pathinfo( $file, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
	), $upload['file'] );
	if ( ! is_wp_error( $id ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
		$media[ $file ] = $id;
		update_option( 'ni_media', $media, false );
		return wp_get_attachment_url( $id );
	}
	return ni_asset( 'img/' . $file );
}

/**
 * Create or update a page and return its ID.
 */
function ni_upsert_page( $existing_id, $args, $meta ) {
	$args = array_merge( array( 'post_type' => 'page', 'post_status' => 'publish', 'comment_status' => 'closed' ), $args );
	if ( isset( $args['post_content'] ) ) {
		$args['post_content'] = wp_slash( $args['post_content'] );
	}
	if ( $existing_id && get_post( $existing_id ) ) {
		$args['ID'] = $existing_id;
		$id         = wp_update_post( $args, true );
	} else {
		$id = wp_insert_post( $args, true );
	}
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
	return $id;
}

/**
 * Run the import.
 *
 * @param bool $overwrite Replace the content of pages that already exist.
 * @return array Summary.
 */
function ni_import_content( $overwrite = false ) {
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	kses_remove_filters(); // keep the design's inline styles intact

	$data  = ni_json( 'pages.json' );
	$map   = get_option( 'ni_pages', array( 'en' => array(), 'ar' => array() ) );
	$langs = array( 'en', 'ar' );
	$made  = 0;

	// Home pages.
	$home_en = ni_upsert_page( (int) get_option( 'ni_home_en' ), array( 'post_title' => $data['home_title']['en'], 'post_name' => 'home', 'menu_order' => 0 ), array( '_ni_lang' => 'en', '_ni_key' => 'home' ) );
	$home_ar = ni_upsert_page( (int) get_option( 'ni_home_ar' ), array( 'post_title' => $data['home_title']['ar'], 'post_name' => 'ar', 'menu_order' => 0 ), array( '_ni_lang' => 'ar', '_ni_key' => 'home' ) );
	update_option( 'ni_home_en', $home_en );
	update_option( 'ni_home_ar', $home_ar );
	update_post_meta( $home_en, '_ni_pair', $home_ar );
	update_post_meta( $home_ar, '_ni_pair', $home_en );

	// Pages (parents come before children in pages.json).
	foreach ( $data['pages'] as $p ) {
		foreach ( $langs as $l ) {
			$existing = isset( $map[ $l ][ $p['key'] ] ) ? (int) $map[ $l ][ $p['key'] ] : 0;
			$parent   = $p['parent'] ? (int) $map[ $l ][ $p['parent'] ] : ( 'ar' === $l ? $home_ar : 0 );
			$args     = array(
				'post_title'  => $p['title'][ $l ],
				'post_name'   => $p['slug'],
				'post_parent' => $parent,
				'menu_order'  => $p['order'],
			);
			if ( ! $existing || $overwrite ) {
				$args['post_content'] = $p['content'][ $l ];
				$args['post_excerpt'] = isset( $p['excerpt'][ $l ] ) ? $p['excerpt'][ $l ] : '';
			}
			$id = ni_upsert_page( $existing, $args, array(
				'_ni_lang'  => $l,
				'_ni_key'   => $p['key'],
				'_ni_tab'   => $p['tab'] ? '1' : '0',
				'_ni_h1'    => $p['h1'][ $l ],
				'_ni_crumb' => isset( $p['crumb'] ) ? $p['crumb'][ $l ] : '',
			) );
			if ( $id ) {
				$map[ $l ][ $p['key'] ] = $id;
				if ( ! $existing ) {
					$made++;
				}
			}
		}
	}
	update_option( 'ni_pages', $map );

	// Pairs + resolve {{link:key}} / {{img:file}} placeholders.
	foreach ( $data['pages'] as $p ) {
		$en = $map['en'][ $p['key'] ];
		$ar = $map['ar'][ $p['key'] ];
		update_post_meta( $en, '_ni_pair', $ar );
		update_post_meta( $ar, '_ni_pair', $en );
		foreach ( $langs as $l ) {
			$id      = $map[ $l ][ $p['key'] ];
			$content = get_post_field( 'post_content', $id );
			if ( false === strpos( $content, '{{' ) ) {
				continue;
			}
			$content = preg_replace_callback( '/\{\{link:(\w+)\}\}/', function ( $m ) use ( $l ) {
				return esc_url( ni_url( $m[1], $l ) );
			}, $content );
			$content = preg_replace_callback( '/\{\{img:([\w.]+)\}\}/', function ( $m ) {
				return esc_url( ni_import_media( $m[1] ) );
			}, $content );
			wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $content ) ) );
		}
	}

	// News posts.
	$news_map = get_option( 'ni_news', array() );
	foreach ( ni_json( 'news.json' ) as $i => $n ) {
		$ids = array();
		foreach ( $langs as $l ) {
			$existing = isset( $news_map[ $i ][ $l ] ) ? (int) $news_map[ $i ][ $l ] : 0;
			if ( $existing && get_post( $existing ) && ! $overwrite ) {
				$ids[ $l ] = $existing;
				continue;
			}
			$args = array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => $n['title'][ $l ],
				'post_excerpt' => $n['excerpt'][ $l ],
				'post_content' => $n['excerpt'][ $l ] ? '<!-- wp:paragraph --><p>' . esc_html( $n['excerpt'][ $l ] ) . '</p><!-- /wp:paragraph -->' : '',
				'post_date'    => $n['date'] . ' 09:00:00',
				'post_name'    => ( 'ar' === $l ? 'ar-' : '' ) . sanitize_title( $n['title']['en'] ),
			);
			if ( $existing && get_post( $existing ) ) {
				$args['ID'] = $existing;
				$id         = wp_update_post( $args );
			} else {
				$id = wp_insert_post( $args );
			}
			update_post_meta( $id, '_ni_lang', $l );
			$ids[ $l ] = $id;
		}
		update_post_meta( $ids['en'], '_ni_pair', $ids['ar'] );
		update_post_meta( $ids['ar'], '_ni_pair', $ids['en'] );
		$news_map[ $i ] = $ids;
	}
	update_option( 'ni_news', $news_map );

	// Menus.
	foreach ( $langs as $l ) {
		ni_build_menu( $l, $map, 'en' === $l ? $home_en : $home_ar );
	}

	// Front page + pretty permalinks.
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_en );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules();

	kses_init_filters();
	update_option( 'ni_imported', NI_VERSION );
	return array( 'created' => $made, 'pages' => count( $map['en'] ) + count( $map['ar'] ) );
}

/**
 * (Re)build the primary menu for one language and assign it to its location.
 */
function ni_build_menu( $lang, $map, $home_id ) {
	$data  = ni_json( 'pages.json' );
	$name  = 'en' === $lang ? 'Primary (English)' : 'Primary (Arabic)';
	$menu  = wp_get_nav_menu_object( $name );
	$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( $name );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
		wp_delete_post( $item->ID, true );
	}
	$add = function ( $page_id, $parent = 0, $pos = 0 ) use ( $menu_id ) {
		return wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-object-id' => $page_id,
			'menu-item-object'    => 'page',
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
			'menu-item-position'  => $pos,
		) );
	};
	$pos = 1;
	$add( $home_id, 0, $pos++ );
	foreach ( $data['pages'] as $p ) {
		if ( 0 === strpos( $p['key'], 'grp_' ) && ! empty( $map[ $lang ][ $p['key'] ] ) ) {
			$gid         = (int) $map[ $lang ][ $p['key'] ];
			$parent_item = $add( $gid, 0, $pos++ );
			$kids        = get_pages( array( 'parent' => $gid, 'sort_column' => 'menu_order', 'meta_key' => '_ni_tab', 'meta_value' => '1', 'post_status' => 'publish' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			foreach ( $kids as $kid ) {
				$add( $kid->ID, $parent_item, $pos++ );
			}
		}
	}
	$add( $map[ $lang ]['contact'], 0, $pos++ );

	$locations                       = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ 'primary_' . $lang ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
