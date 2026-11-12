<?php
/**
 * Nikon Institute theme — bilingual (English / Arabic) institutional site.
 *
 * Language model: English pages live at the site root, Arabic pages live under
 * the /ar/ page. Every page stores its language (_ni_lang), a stable key (_ni_key)
 * and the ID of its translation (_ni_pair).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NI_VERSION', '1.0.0' );
define( 'NI_DIR', get_template_directory() );
define( 'NI_URI', get_template_directory_uri() );

require_once NI_DIR . '/inc/importer.php';
require_once NI_DIR . '/inc/forms.php';
require_once NI_DIR . '/inc/admin.php';

/* -------------------------------------------------------------------------
 * Setup
 * ---------------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	register_nav_menus( array(
		'primary_en' => 'Primary menu (English)',
		'primary_ar' => 'Primary menu (Arabic)',
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'ni-site', NI_URI . '/assets/css/site.css', array(), NI_VERSION );
	wp_enqueue_style( 'ni-theme', NI_URI . '/assets/css/theme.css', array( 'ni-site' ), NI_VERSION );
	wp_enqueue_script( 'ni-site', NI_URI . '/assets/js/site.js', array(), NI_VERSION, true );
} );

/* -------------------------------------------------------------------------
 * Helpers
 * ---------------------------------------------------------------------- */
function ni_asset( $path ) {
	return NI_URI . '/assets/' . ltrim( $path, '/' );
}

function ni_json( $file ) {
	static $cache = array();
	if ( ! isset( $cache[ $file ] ) ) {
		$cache[ $file ] = json_decode( file_get_contents( NI_DIR . '/data/' . $file ), true );
	}
	return $cache[ $file ];
}

/** Current language: 'en' or 'ar'. */
function ni_lang( $post_id = null ) {
	static $current = null;
	if ( null === $post_id && null !== $current ) {
		return $current;
	}
	$lang = 'en';
	$id   = $post_id ? $post_id : ( is_singular() ? get_queried_object_id() : 0 );
	if ( $id ) {
		$meta = get_post_meta( $id, '_ni_lang', true );
		if ( $meta ) {
			$lang = $meta;
		} else {
			$home_ar = (int) get_option( 'ni_home_ar' );
			if ( $home_ar && ( $id === $home_ar || in_array( $home_ar, get_post_ancestors( $id ), true ) ) ) {
				$lang = 'ar';
			}
		}
	} elseif ( isset( $_GET['lang'] ) && 'ar' === $_GET['lang'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		$lang = 'ar';
	}
	if ( null === $post_id ) {
		$current = $lang;
	}
	return $lang;
}

function ni_is_rtl() {
	return 'ar' === ni_lang();
}

/** Translated UI string. */
function ni_t( $key ) {
	$s = ni_json( 'strings.json' );
	$l = ni_lang();
	return isset( $s[ $l ][ $key ] ) ? $s[ $l ][ $key ] : ( isset( $s['en'][ $key ] ) ? $s['en'][ $key ] : $key );
}

/** URL of a page by key, in the current (or given) language. */
function ni_url( $key, $lang = null ) {
	$lang = $lang ? $lang : ni_lang();
	if ( 'home' === $key ) {
		$id = (int) get_option( 'ar' === $lang ? 'ni_home_ar' : 'ni_home_en' );
		if ( 'en' === $lang || ! $id ) {
			return home_url( '/' );
		}
		return get_permalink( $id );
	}
	$map = get_option( 'ni_pages', array() );
	if ( ! empty( $map[ $lang ][ $key ] ) ) {
		return get_permalink( $map[ $lang ][ $key ] );
	}
	return '#';
}

/** Link to the same page in the other language. */
function ni_lang_switch_url( $target ) {
	if ( is_singular() ) {
		$id = get_queried_object_id();
		if ( ni_lang( $id ) === $target ) {
			return get_permalink( $id );
		}
		$pair = (int) get_post_meta( $id, '_ni_pair', true );
		if ( $pair && 'publish' === get_post_status( $pair ) ) {
			return get_permalink( $pair );
		}
	}
	return ni_url( 'home', $target );
}

function ni_part( $name ) {
	$file = NI_DIR . '/parts/' . $name . '-' . ni_lang() . '.php';
	if ( file_exists( $file ) ) {
		include $file;
	}
}

/** Fallback when no menu is assigned: list top-level pages of the language. */
function ni_fallback_menu() {
	$parent = 'ar' === ni_lang() ? (int) get_option( 'ni_home_ar' ) : 0;
	echo '<ul class="ni-menu">';
	wp_list_pages( array(
		'title_li'    => '',
		'parent'      => $parent,
		'depth'       => 2,
		'sort_column' => 'menu_order',
		'exclude'     => implode( ',', array_filter( array( (int) get_option( 'ni_home_en' ), $parent ? 0 : (int) get_option( 'ni_home_ar' ) ) ) ),
	) );
	echo '</ul>';
}

/* <html> lang/dir follow the page language, not the site locale. */
add_filter( 'language_attributes', function () {
	return ni_is_rtl() ? 'lang="ar" dir="rtl"' : 'lang="en" dir="ltr"';
} );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'ni-lang-' . ni_lang();
	return $classes;
} );

/* Hide the donate/contact/etc. legacy pages from tabs — handled in page.php. */

/* Keep the Arabic home page rendering the home template. */
add_filter( 'template_include', function ( $template ) {
	if ( is_page() && (int) get_option( 'ni_home_ar' ) === get_queried_object_id() ) {
		return NI_DIR . '/front-page.php';
	}
	return $template;
} );

/* -------------------------------------------------------------------------
 * [ni_news] — latest news posts in the current language
 * ---------------------------------------------------------------------- */
add_shortcode( 'ni_news', function ( $atts ) {
	$atts = shortcode_atts( array( 'count' => 10 ), $atts );
	$q    = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => (int) $atts['count'],
		'meta_query'     => 'ar' === ni_lang() // phpcs:ignore WordPress.DB.SlowDBQuery
			? array( array( 'key' => '_ni_lang', 'value' => 'ar' ) )
			: array( array( 'key' => '_ni_lang', 'value' => 'en' ) ),
	) );
	$arrow = ni_is_rtl() ? '←' : '→';
	ob_start();
	echo '<div class="ni-news-list">';
	while ( $q->have_posts() ) {
		$q->the_post();
		printf(
			'<a class="ni-news-item" href="%s"><span class="ni-news-date">%s</span><span class="ni-news-title">%s</span><span class="ni-news-arrow">%s</span></a>',
			esc_url( get_permalink() ),
			esc_html( get_the_date() ),
			esc_html( get_the_title() ),
			esc_html( $arrow )
		);
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
} );

/* Arabic dates for Arabic content. */
add_filter( 'get_the_date', function ( $date, $format, $post ) {
	if ( 'ar' === ni_lang( $post->ID ) && function_exists( 'date_i18n' ) ) {
		$months = array( 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر' );
		$ts     = get_post_timestamp( $post );
		return gmdate( 'j', $ts ) . ' ' . $months[ (int) gmdate( 'n', $ts ) - 1 ] . ' ' . gmdate( 'Y', $ts );
	}
	return $date;
}, 10, 3 );

/* Language box on posts (news). */
add_action( 'add_meta_boxes_post', function () {
	add_meta_box( 'ni_lang', 'Language', function ( $post ) {
		$l = get_post_meta( $post->ID, '_ni_lang', true );
		wp_nonce_field( 'ni_lang_box', 'ni_lang_nonce' );
		echo '<select name="ni_lang_choice" style="width:100%"><option value="en"' . selected( 'ar' !== $l, true, false ) . '>English</option><option value="ar"' . selected( 'ar', $l, false ) . '>العربية (Arabic)</option></select>';
	}, 'post', 'side', 'high' );
} );
add_action( 'save_post_post', function ( $id ) {
	if ( isset( $_POST['ni_lang_choice'], $_POST['ni_lang_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ni_lang_nonce'] ) ), 'ni_lang_box' ) ) {
		update_post_meta( $id, '_ni_lang', 'ar' === $_POST['ni_lang_choice'] ? 'ar' : 'en' );
	}
} );

/* 301 redirects from old URLs (option ni_redirects: path => [key, lang]). */
add_action( 'template_redirect', function () {
	$map = get_option( 'ni_redirects' );
	if ( ! $map || ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$path = trim( strtolower( (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) ), '/' );
	if ( isset( $map[ $path ] ) ) {
		$url = ni_url( $map[ $path ][0], $map[ $path ][1] );
		if ( $url && '#' !== $url && trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' ) !== $path ) {
			wp_safe_redirect( $url, 301 );
			exit;
		}
	}
}, 1 );

/*
 * Elementor Pro: this theme draws its own header and footer, so claim the
 * header/footer locations and never print Elementor's site-wide templates.
 */
add_action( 'elementor/theme/register_locations', function ( $manager ) {
	$manager->register_location( 'header' );
	$manager->register_location( 'footer' );
} );

/* Browser-tab icon: theme default when no Site Icon is set in Customizer. */
add_action( 'wp_head', function () {
	if ( has_site_icon() ) {
		return;
	}
	echo '<link rel="icon" href="' . esc_url( ni_asset( 'img/favicon.ico' ) ) . '" sizes="any">' . "\n";
	echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( ni_asset( 'img/favicon-32.png' ) ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( ni_asset( 'img/apple-touch-icon.png' ) ) . '">' . "\n";
}, 2 );
