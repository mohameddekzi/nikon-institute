<?php
/**
 * Site header: utility bar, logo, primary menu, Donate button, mobile menu.
 */
$ni_lang     = ni_lang();
$ni_location = 'primary_' . $ni_lang;
$ni_logo     = ni_json( 'assets.json' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="ni-site" dir="<?php echo ni_is_rtl() ? 'rtl' : 'ltr'; ?>" lang="<?php echo esc_attr( $ni_lang ); ?>">

<?php ni_part( 'utility' ); ?>

<header class="ni-header">
	<div class="ni-header__in">
		<a class="ni-logo" href="<?php echo esc_url( ni_url( 'home' ) ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( ni_asset( 'img/' . $ni_logo['logo'] ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php endif; ?>
		</a>
		<nav class="ni-nav" aria-label="Primary">
			<?php
			wp_nav_menu( array(
				'theme_location' => $ni_location,
				'container'      => false,
				'menu_class'     => 'ni-menu',
				'depth'          => 2,
				'fallback_cb'    => 'ni_fallback_menu',
			) );
			?>
		</nav>
		<a class="ni-donate" href="<?php echo esc_url( ni_url( 'donatePage' ) ); ?>"><?php echo esc_html( ni_t( 'donate' ) ); ?></a>
		<button class="ni-burger" type="button" aria-label="Menu" aria-expanded="false" aria-controls="ni-mnav"><span></span><span></span><span></span></button>
	</div>
	<nav class="ni-mnav" id="ni-mnav" aria-label="Mobile">
		<?php
		wp_nav_menu( array(
			'theme_location' => $ni_location,
			'container'      => false,
			'menu_class'     => 'ni-mmenu',
			'depth'          => 2,
			'fallback_cb'    => 'ni_fallback_menu',
		) );
		?>
		<a class="ni-mdonate" href="<?php echo esc_url( ni_url( 'donatePage' ) ); ?>"><?php echo esc_html( ni_t( 'donate' ) ); ?></a>
	</nav>
</header>
