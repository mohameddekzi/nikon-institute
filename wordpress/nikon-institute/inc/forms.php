<?php
/**
 * [ni_form type="strategic|institutional|devPartners|joinUs|contact"]
 * Submissions are emailed and stored under "Form submissions" in the admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	register_post_type( 'ni_submission', array(
		'labels'          => array(
			'name'          => 'Form submissions',
			'singular_name' => 'Form submission',
			'menu_name'     => 'Form submissions',
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 26,
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

add_filter( 'manage_ni_submission_posts_columns', function ( $cols ) {
	return array(
		'cb'       => $cols['cb'],
		'title'    => 'Submission',
		'ni_form'  => 'Form',
		'ni_email' => 'Email',
		'date'     => 'Date',
	);
} );
add_action( 'manage_ni_submission_posts_custom_column', function ( $col, $id ) {
	if ( 'ni_form' === $col ) {
		echo esc_html( get_post_meta( $id, '_ni_form_title', true ) );
	}
	if ( 'ni_email' === $col ) {
		$e = get_post_meta( $id, '_ni_email', true );
		echo $e ? '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>' : '';
	}
}, 10, 2 );

function ni_form_config( $type ) {
	$all = ni_json( 'forms.json' );
	return isset( $all['forms'][ $type ] ) ? $all['forms'][ $type ] : null;
}

function ni_form_ui( $lang ) {
	$all = ni_json( 'forms.json' );
	return $all['ui'][ $lang ];
}

add_shortcode( 'ni_form', function ( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'contact' ), $atts );
	$type = sanitize_key( $atts['type'] );
	$cfg  = ni_form_config( $atts['type'] );
	if ( ! $cfg ) {
		return '';
	}
	$lang    = ni_lang();
	$ui      = ni_form_ui( $lang );
	$eyebrow = isset( $cfg['eyebrow'][ $lang ] ) ? $cfg['eyebrow'][ $lang ] : $ui['eyebrow'];
	// phpcs:disable WordPress.Security.NonceVerification
	$sent  = isset( $_GET['ni_sent'] ) && $_GET['ni_sent'] === $atts['type'];
	$error = isset( $_GET['ni_err'] ) && $_GET['ni_err'] === $atts['type'];
	// phpcs:enable
	$sent_text = 'ar' === $lang ? 'تم استلام رسالتك، وسيتواصل معك فريقنا قريبًا.' : 'We have received your message and our team will be in touch soon.';
	$err_text  = 'ar' === $lang ? 'تعذّر إرسال الاستمارة. يرجى التحقق من الحقول المطلوبة والمحاولة مرة أخرى.' : 'We could not send the form. Please check the required fields and try again.';
	$note      = 'ar' === $lang ? 'الحقول المميزة بـ * مطلوبة.' : 'Fields marked * are required.';

	ob_start();
	?>
	<div class="ni-form" id="ni-form-<?php echo esc_attr( $type ); ?>">
		<div class="ni-form__eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
		<h2 class="ni-form__title"><?php echo esc_html( $cfg[ $lang ] ); ?></h2>
		<?php if ( $sent ) : ?>
			<div class="ni-form__ok" role="status"><strong><?php echo esc_html( 'ar' === $lang ? 'شكرًا لك!' : 'Thank you!' ); ?></strong> <?php echo esc_html( $sent_text ); ?></div>
		<?php endif; ?>
		<?php if ( $error ) : ?>
			<div class="ni-form__err" role="alert"><?php echo esc_html( $err_text ); ?></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ni_form">
			<input type="hidden" name="ni_type" value="<?php echo esc_attr( $atts['type'] ); ?>">
			<input type="hidden" name="ni_lang" value="<?php echo esc_attr( $lang ); ?>">
			<input type="hidden" name="ni_back" value="<?php echo esc_url( get_permalink() ); ?>">
			<?php wp_nonce_field( 'ni_form_' . $atts['type'], 'ni_nonce' ); ?>
			<div class="ni-hp" aria-hidden="true"><label>Website <input type="text" name="ni_website" tabindex="-1" autocomplete="off"></label></div>
			<div class="ni-form__grid">
				<?php foreach ( $cfg['fields'] as $f ) : ?>
					<?php
					$name  = 'f_' . $f['name'];
					$label = $f[ $lang ];
					$req   = ! empty( $f['req'] );
					?>
					<label class="ni-field<?php echo ! empty( $f['full'] ) ? ' ni-field--full' : ''; ?>">
						<span><?php echo esc_html( $label ); ?><?php echo $req ? '<span class="ni-req"> *</span>' : ''; ?></span>
						<?php if ( 'select' === $f['type'] ) : ?>
							<select name="<?php echo esc_attr( $name ); ?>" <?php echo $req ? 'required' : ''; ?>>
								<option value=""><?php echo esc_html( $ui['choose'] ); ?></option>
								<?php foreach ( (array) $f['opts'] as $o ) : ?>
									<option value="<?php echo esc_attr( $o[ 'ar' === $lang ? 1 : 0 ] ); ?>"><?php echo esc_html( $o[ 'ar' === $lang ? 1 : 0 ] ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php elseif ( 'textarea' === $f['type'] ) : ?>
							<textarea name="<?php echo esc_attr( $name ); ?>" rows="5" <?php echo $req ? 'required' : ''; ?>></textarea>
						<?php else : ?>
							<input type="<?php echo esc_attr( $f['type'] ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo $req ? 'required' : ''; ?>>
						<?php endif; ?>
					</label>
				<?php endforeach; ?>
			</div>
			<div class="ni-form__foot">
				<button type="submit" class="ni-btn"><?php echo esc_html( $ui['submit'] ); ?></button>
				<span class="ni-form__note"><?php echo esc_html( $note ); ?></span>
			</div>
		</form>
	</div>
	<?php
	return ob_get_clean();
} );

function ni_handle_form() {
	$type = isset( $_POST['ni_type'] ) ? sanitize_text_field( wp_unslash( $_POST['ni_type'] ) ) : '';
	$back = isset( $_POST['ni_back'] ) ? esc_url_raw( wp_unslash( $_POST['ni_back'] ) ) : home_url( '/' );
	$back = wp_validate_redirect( $back, home_url( '/' ) );
	$lang = ( isset( $_POST['ni_lang'] ) && 'ar' === $_POST['ni_lang'] ) ? 'ar' : 'en';
	$cfg  = ni_form_config( $type );
	$fail = add_query_arg( 'ni_err', $type, $back ) . '#ni-form-' . sanitize_key( $type );

	if ( ! $cfg || ! isset( $_POST['ni_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ni_nonce'] ) ), 'ni_form_' . $type ) ) {
		wp_safe_redirect( $fail );
		exit;
	}
	if ( ! empty( $_POST['ni_website'] ) ) { // honeypot
		wp_safe_redirect( add_query_arg( 'ni_sent', $type, $back ) . '#ni-form-' . sanitize_key( $type ) );
		exit;
	}

	$lines = array();
	$email = '';
	$who   = '';
	foreach ( $cfg['fields'] as $f ) {
		$key = 'f_' . $f['name'];
		$val = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$val = 'textarea' === $f['type'] ? sanitize_textarea_field( $val ) : sanitize_text_field( $val );
		if ( ! empty( $f['req'] ) && '' === trim( $val ) ) {
			wp_safe_redirect( $fail );
			exit;
		}
		if ( 'email' === $f['type'] && $val ) {
			if ( ! is_email( $val ) ) {
				wp_safe_redirect( $fail );
				exit;
			}
			$email = $val;
		}
		if ( in_array( $f['name'], array( 'org', 'name' ), true ) && $val && ! $who ) {
			$who = $val;
		}
		if ( '' !== $val ) {
			$lines[] = $f['en'] . ': ' . $val;
		}
	}

	$title   = $cfg['en'] . ( $who ? ' — ' . $who : '' );
	$body    = implode( "\n\n", $lines );
	$post_id = wp_insert_post( array(
		'post_type'    => 'ni_submission',
		'post_status'  => 'private',
		'post_title'   => $title,
		'post_content' => $body,
	) );
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_ni_form_title', $cfg['en'] );
		update_post_meta( $post_id, '_ni_email', $email );
		update_post_meta( $post_id, '_ni_lang', $lang );
	}

	$to      = get_option( 'ni_form_email', 'info@nikoninstitute.org' );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $email ) {
		$headers[] = 'Reply-To: ' . $email;
	}
	wp_mail( $to, '[Website] ' . $title, $body . "\n\n— " . home_url( '/' ), $headers );

	wp_safe_redirect( add_query_arg( 'ni_sent', $type, remove_query_arg( 'ni_err', $back ) ) . '#ni-form-' . sanitize_key( $type ) );
	exit;
}
add_action( 'admin_post_ni_form', 'ni_handle_form' );
add_action( 'admin_post_nopriv_ni_form', 'ni_handle_form' );
