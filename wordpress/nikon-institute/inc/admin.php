<?php
/**
 * Appearance → Nikon Institute: import content, form email.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	add_theme_page( 'Nikon Institute', 'Nikon Institute', 'manage_options', 'ni-setup', 'ni_admin_page' );
} );

add_action( 'admin_post_ni_setup', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'ni_setup' ) ) {
		wp_die( 'Not allowed.' );
	}
	$msg = 'saved';
	if ( isset( $_POST['ni_email'] ) ) {
		$e = sanitize_email( wp_unslash( $_POST['ni_email'] ) );
		if ( is_email( $e ) ) {
			update_option( 'ni_form_email', $e );
		}
	}
	if ( ! empty( $_POST['ni_import'] ) ) {
		$r   = ni_import_content( ! empty( $_POST['ni_overwrite'] ) );
		$msg = 'imported-' . (int) $r['pages'];
	}
	wp_safe_redirect( admin_url( 'themes.php?page=ni-setup&ni_msg=' . $msg ) );
	exit;
} );

function ni_admin_page() {
	// phpcs:ignore WordPress.Security.NonceVerification
	$msg = isset( $_GET['ni_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['ni_msg'] ) ) : '';
	?>
	<div class="wrap">
		<h1>Nikon Institute</h1>
		<?php if ( 0 === strpos( $msg, 'imported' ) ) : ?>
			<div class="notice notice-success"><p>Content imported (<?php echo esc_html( substr( $msg, 9 ) ); ?> pages in English and Arabic). Menus and the front page have been set.</p></div>
		<?php elseif ( 'saved' === $msg ) : ?>
			<div class="notice notice-success"><p>Settings saved.</p></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ni_setup">
			<?php wp_nonce_field( 'ni_setup' ); ?>
			<h2>Forms</h2>
			<table class="form-table"><tr>
				<th scope="row"><label for="ni_email">Send form submissions to</label></th>
				<td><input class="regular-text" type="email" id="ni_email" name="ni_email" value="<?php echo esc_attr( get_option( 'ni_form_email', 'info@nikoninstitute.org' ) ); ?>">
				<p class="description">Every submission is also saved under <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ni_submission' ) ); ?>">Form submissions</a>.</p></td>
			</tr></table>
			<h2>Website content</h2>
			<p>Status: <?php echo get_option( 'ni_imported' ) ? '<strong>imported</strong> (version ' . esc_html( get_option( 'ni_imported' ) ) . ')' : '<strong>not imported yet</strong>'; ?></p>
			<p><label><input type="checkbox" name="ni_import" value="1" <?php checked( ! get_option( 'ni_imported' ) ); ?>> Import / re-link all pages, news, menus and front page (English + Arabic)</label></p>
			<p><label><input type="checkbox" name="ni_overwrite" value="1"> Overwrite the text of pages that already exist (your edits will be replaced)</label></p>
			<?php submit_button( 'Save' ); ?>
		</form>
	</div>
	<?php
}
