<?php
/**
 * Design sections: programme cards, donate experience, books library,
 * email chooser. All bilingual (en / ar).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Pick the string for the current language. */
function ni_l( $en, $ar ) {
	return 'ar' === ni_lang() ? $ar : $en;
}

/** Image URL for an attachment ID (falls back to empty). */
function ni_img( $id, $size = 'large' ) {
	$u = $id ? wp_get_attachment_image_url( (int) $id, $size ) : '';
	return $u ? $u : '';
}

/* -------------------------------------------------------------------------
 * Programmes
 * ---------------------------------------------------------------------- */
function ni_programs_data() {
	$p = array(
		array( 'rnd', 'rnd', 4250, 'Research & Development', 'البحث والتطوير',
			'Multidisciplinary, participatory research that turns local evidence into better policy and practice.',
			'بحوث تشاركية متعددة التخصصات تحوّل الأدلة المحلية إلى سياسات وممارسات أفضل.' ),
		array( 'community', 'community', 3887, 'Community Development & Resilience', 'تنمية المجتمع والصمود المحلي',
			'Putting vulnerable and grassroots communities at the centre as architects of their own resilience.',
			'نضع المجتمعات الهشّة والقاعدية في المركز لتكون صانعة صمودها بنفسها.' ),
		array( 'governance', 'governance', 1803, 'Inclusive Governance & Leadership', 'الحوكمة الشاملة والقيادة',
			'Strengthening institutions, accountability and participatory decision-making at every level.',
			'تقوية المؤسسات والمساءلة وصنع القرار التشاركي على جميع المستويات.' ),
		array( 'climate', 'climate', 3313, 'Climate Action & Resilience', 'العمل المناخي والقدرة على الصمود',
			'Locally led adaptation that protects ecosystems, livelihoods and natural resources.',
			'تكيّف بقيادة محلية يحمي النظم البيئية وسبل العيش والموارد الطبيعية.' ),
		array( 'economic', 'economic', 3300, 'Livelihoods & Economic Empowerment', 'سبل العيش والتمكين الاقتصادي',
			'Co-creating practical solutions that grow incomes, enterprise and decent work.',
			'نبتكر مع المجتمعات حلولًا عملية تنمّي الدخل وريادة الأعمال والعمل اللائق.' ),
		array( 'peace', 'peace', 3889, 'Peacebuilding & Social Cohesion', 'بناء السلام والتماسك الاجتماعي',
			'Dialogue and collaboration that build trust, prevent conflict and bring communities together.',
			'حوار وتعاون يبنيان الثقة ويمنعان النزاعات ويقرّبان بين المجتمعات.' ),
		array( 'training', 'grp_menuTraining', 3298, 'Training & Capacity Building', 'التدريب وبناء القدرات',
			'Practical training, workshops and fellowships that build skills for people and institutions.',
			'تدريب عملي وورش عمل وزمالات تبني مهارات الأفراد والمؤسسات.' ),
		array( 'policy', 'sharing', 4048, 'Policy Dialogue & Knowledge Sharing', 'حوار السياسات وتبادل المعرفة',
			'Translating research into actionable recommendations that shape local, national and regional decisions.',
			'نحوّل البحوث إلى توصيات قابلة للتنفيذ تؤثّر في القرارات المحلية والوطنية والإقليمية.' ),
	);
	$out = array();
	foreach ( $p as $row ) {
		$out[] = array(
			'id'    => $row[0],
			'key'   => $row[1],
			'img'   => $row[2],
			'title' => ni_l( $row[3], $row[4] ),
			'text'  => ni_l( $row[5], $row[6] ),
		);
	}
	return $out;
}

function ni_program_cards() {
	$more  = ni_l( 'Learn more', 'اقرأ المزيد' );
	$arrow = ni_is_rtl() ? '←' : '→';
	$html  = '<div class="ni-pgrid">';
	foreach ( ni_programs_data() as $i => $p ) {
		$img   = ni_img( $p['img'], 'large' );
		$html .= sprintf(
			'<a class="ni-pcard" href="%1$s">%2$s<span class="ni-pcard__shade"></span><span class="ni-pcard__body"><span class="ni-pcard__num">%3$s</span><span class="ni-pcard__title">%4$s</span><span class="ni-pcard__text">%5$s</span><span class="ni-pcard__more">%6$s %7$s</span></span></a>',
			esc_url( ni_url( $p['key'] ) ),
			$img ? '<img class="ni-pcard__img" src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">' : '',
			esc_html( sprintf( '%02d', $i + 1 ) ),
			esc_html( $p['title'] ),
			esc_html( $p['text'] ),
			esc_html( $more ),
			esc_html( $arrow )
		);
	}
	return $html . '</div>';
}

/** Home page programmes section. */
function ni_programs_section() {
	ob_start();
	?>
	<section class="ni-programs" data-screen-label="Programs" id="programs">
		<div class="ni-programs__rings"><div></div><div></div></div>
		<div class="ni-programs__in">
			<div class="ni-programs__head">
				<div>
					<div class="ni-eyebrow ni-eyebrow--light"><span></span><?php echo esc_html( ni_l( 'Our programs', 'برامجنا' ) ); ?></div>
					<h2 class="ni-programs__title"><?php echo esc_html( ni_l( 'Connecting evidence with action.', 'نربط الأدلة بالعمل.' ) ); ?></h2>
					<p class="ni-programs__lead"><?php echo esc_html( ni_l( 'Eight programme areas that generate knowledge, strengthen institutions and drive sustainable development across Somalia and the Horn of Africa.', 'ثمانية مجالات برامجية تولّد المعرفة وتقوّي المؤسسات وتدفع التنمية المستدامة في الصومال والقرن الأفريقي.' ) ); ?></p>
				</div>
				<a class="ni-btn-outline" href="<?php echo esc_url( ni_url( 'grp_menuPrograms' ) ); ?>"><?php echo esc_html( ni_l( 'View all programs', 'عرض جميع البرامج' ) ); ?> <span><?php echo ni_is_rtl() ? '←' : '→'; ?></span></a>
			</div>
			<?php echo ni_program_cards(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

add_shortcode( 'ni_programs', function () {
	return '<div class="ni-programs-page">' . ni_program_cards() . '</div>';
} );

/* -------------------------------------------------------------------------
 * Donate
 * ---------------------------------------------------------------------- */
function ni_banks() {
	return array(
		array( 'Premier Bank', 'بنك بريمير', 695, '020612833001' ),
		array( 'Dahabshil Bank', 'بنك دهبشيل', 696, '301019603101' ),
	);
}

add_shortcode( 'ni_donate', function () {
	$amounts = array( 25, 50, 100, 250, 500 );
	$purpose = array( ni_l( 'Where it is needed most', 'حيث تشتد الحاجة' ) );
	foreach ( ni_programs_data() as $p ) {
		$purpose[] = $p['title'];
	}
	$t = array(
		'once'    => ni_l( 'One-time', 'مرة واحدة' ),
		'monthly' => ni_l( 'Monthly', 'شهريًا' ),
		'other'   => ni_l( 'Other', 'مبلغ آخر' ),
		'direct'  => ni_l( 'Direct my gift to', 'وجّه تبرعي إلى' ),
		'donate'  => ni_l( 'Donate', 'تبرّع بـ' ),
		'month'   => ni_l( '/ month', '/ شهريًا' ),
		'secure'  => ni_l( 'Gifts are made by bank transfer to the Institute’s accounts below.', 'تُقدَّم التبرعات بالتحويل المصرفي إلى حسابات المعهد أدناه.' ),
	);
	$arrow = ni_is_rtl() ? '←' : '→';
	ob_start();
	?>
	<div class="ni-give" data-month="<?php echo esc_attr( $t['month'] ); ?>" data-donate="<?php echo esc_attr( $t['donate'] ); ?>" data-once="<?php echo esc_attr( $t['once'] ); ?>" data-monthly="<?php echo esc_attr( $t['monthly'] ); ?>">
		<div class="ni-give__intro">
			<p class="ni-give__lead"><?php echo esc_html( ni_l( 'Every contribution counts. Your gift turns evidence into opportunity, resilience and lasting change for communities across Somalia.', 'كل مساهمة لها قيمتها. عطاؤك يحوّل الأدلة إلى فرص وصمود وتغيير دائم للمجتمعات في جميع أنحاء الصومال.' ) ); ?></p>
			<?php $ph = ni_img( 1669, 'large' ); ?>
			<?php if ( $ph ) : ?><img class="ni-give__photo" src="<?php echo esc_url( $ph ); ?>" alt="<?php echo esc_attr( ni_l( 'Students studying in a classroom in Mogadishu', 'طالبات يدرسن في أحد فصول مقديشو' ) ); ?>" loading="lazy"><?php endif; ?>
		</div>
		<div class="ni-give__card">
			<div class="ni-give__freq" role="radiogroup" aria-label="<?php echo esc_attr( ni_l( 'Gift frequency', 'تكرار التبرع' ) ); ?>">
				<button type="button" class="is-on" role="radio" aria-checked="true" data-freq="once"><?php echo esc_html( $t['once'] ); ?></button>
				<button type="button" role="radio" aria-checked="false" data-freq="monthly"><?php echo esc_html( $t['monthly'] ); ?></button>
			</div>
			<div class="ni-give__amounts" role="radiogroup" aria-label="<?php echo esc_attr( ni_l( 'Amount', 'المبلغ' ) ); ?>">
				<?php foreach ( $amounts as $a ) : ?>
					<button type="button" role="radio" data-amt="<?php echo (int) $a; ?>" class="<?php echo 100 === $a ? 'is-on' : ''; ?>" aria-checked="<?php echo 100 === $a ? 'true' : 'false'; ?>">$<?php echo (int) $a; ?></button>
				<?php endforeach; ?>
				<label class="ni-give__other"><span>$</span><input type="number" min="1" step="1" inputmode="numeric" placeholder="<?php echo esc_attr( $t['other'] ); ?>" aria-label="<?php echo esc_attr( $t['other'] ); ?>"></label>
			</div>
			<div class="ni-give__label"><?php echo esc_html( $t['direct'] ); ?></div>
			<select class="ni-give__purpose" aria-label="<?php echo esc_attr( $t['direct'] ); ?>">
				<?php foreach ( $purpose as $o ) : ?><option><?php echo esc_html( $o ); ?></option><?php endforeach; ?>
			</select>
			<button type="button" class="ni-give__go"><span class="ni-give__go-txt"><?php echo esc_html( $t['donate'] ); ?> $100</span> <span aria-hidden="true"><?php echo esc_html( $arrow ); ?></span></button>
			<p class="ni-give__note"><?php echo esc_html( $t['secure'] ); ?></p>
		</div>
	</div>

	<div class="ni-banks" id="ni-banks">
		<div class="ni-banks__head">
			<div class="ni-eyebrow"><span></span><?php echo esc_html( ni_l( 'Bank transfer', 'التحويل المصرفي' ) ); ?></div>
			<h2 class="ni-banks__title"><?php echo esc_html( ni_l( 'Complete your gift', 'أكمل تبرعك' ) ); ?></h2>
			<p class="ni-banks__summary"><?php echo esc_html( ni_l( 'Your gift:', 'تبرعك:' ) ); ?> <strong class="ni-banks__sum">$100 · <?php echo esc_html( $t['once'] ); ?> · <?php echo esc_html( $purpose[0] ); ?></strong></p>
		</div>
		<div class="ni-banks__grid">
			<?php foreach ( ni_banks() as $b ) : ?>
				<?php $logo = ni_img( $b[2], 'full' ); ?>
				<div class="ni-bank">
					<div class="ni-bank__logo"><?php if ( $logo ) : ?><img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $b[0] ); ?>"><?php else : ?><strong><?php echo esc_html( $b[0] ); ?></strong><?php endif; ?></div>
					<div class="ni-bank__name"><?php echo esc_html( ni_l( $b[0], $b[1] ) ); ?></div>
					<dl class="ni-bank__dl">
						<div><dt><?php echo esc_html( ni_l( 'Account name', 'اسم الحساب' ) ); ?></dt><dd>Nikon Institute</dd></div>
						<div><dt><?php echo esc_html( ni_l( 'Account number', 'رقم الحساب' ) ); ?></dt><dd><span dir="ltr" class="ni-bank__no"><?php echo esc_html( $b[3] ); ?></span> <button type="button" class="ni-copy" data-copy="<?php echo esc_attr( $b[3] ); ?>" data-done="<?php echo esc_attr( ni_l( 'Copied', 'تم النسخ' ) ); ?>"><?php echo esc_html( ni_l( 'Copy', 'نسخ' ) ); ?></button></dd></div>
					</dl>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="ni-banks__after"><?php echo esc_html( ni_l( 'After your transfer, tell us about your gift below so we can thank you and send a receipt.', 'بعد التحويل، أخبرنا عن تبرعك أدناه لنشكرك ونرسل لك إيصالًا.' ) ); ?></p>
		<?php echo do_shortcode( '[ni_form type="donation"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<?php
	return ob_get_clean();
} );

/* -------------------------------------------------------------------------
 * Books (CPT "ni_book" registered via Custom Post Type UI, fields via ACF)
 * ---------------------------------------------------------------------- */
function ni_book_pdf( $id ) {
	$f = function_exists( 'get_field' ) ? get_field( 'book_pdf', $id ) : get_post_meta( $id, 'book_pdf', true );
	if ( is_array( $f ) ) {
		return $f['url'] ?? '';
	}
	if ( is_numeric( $f ) ) {
		return wp_get_attachment_url( (int) $f );
	}
	return is_string( $f ) ? $f : '';
}

add_shortcode( 'ni_books', function () {
	$books = get_posts( array( 'post_type' => 'ni_book', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order date', 'order' => 'DESC' ) );
	$ar    = ni_is_rtl();
	ob_start();
	echo '<div class="ni-books">';
	foreach ( $books as $b ) {
		$pdf    = ni_book_pdf( $b->ID );
		$cover  = get_the_post_thumbnail_url( $b, 'large' );
		$author = get_post_meta( $b->ID, 'book_author', true );
		$year   = get_post_meta( $b->ID, 'book_year', true );
		$pages  = get_post_meta( $b->ID, 'book_pages', true );
		$meta   = array_filter( array( $author, $year, $pages ? $pages . ' ' . ( $ar ? 'صفحة' : 'pages' ) : '' ) );
		?>
		<article class="ni-book">
			<button type="button" class="ni-book__cover" <?php echo $pdf ? 'data-pdf="' . esc_url( $pdf ) . '" data-title="' . esc_attr( get_the_title( $b ) ) . '"' : 'disabled'; ?> aria-label="<?php echo esc_attr( ( $ar ? 'معاينة: ' : 'Preview: ' ) . get_the_title( $b ) ); ?>">
				<?php if ( $cover ) : ?>
					<img src="<?php echo esc_url( $cover ); ?>" alt="" loading="lazy">
				<?php else : ?>
					<span class="ni-book__blank"><span class="ni-book__blank-mark">NIKON</span><span class="ni-book__blank-title"><?php echo esc_html( get_the_title( $b ) ); ?></span></span>
				<?php endif; ?>
				<span class="ni-book__spine"></span>
			</button>
			<div class="ni-book__body">
				<h3 class="ni-book__title"><?php echo esc_html( get_the_title( $b ) ); ?></h3>
				<?php if ( $meta ) : ?><div class="ni-book__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?></div><?php endif; ?>
				<?php if ( has_excerpt( $b ) ) : ?><p class="ni-book__text"><?php echo esc_html( get_the_excerpt( $b ) ); ?></p><?php endif; ?>
				<div class="ni-book__actions">
					<?php if ( $pdf ) : ?>
						<button type="button" class="ni-book__btn" data-pdf="<?php echo esc_url( $pdf ); ?>" data-title="<?php echo esc_attr( get_the_title( $b ) ); ?>"><?php echo esc_html( $ar ? 'معاينة الكتاب' : 'Preview book' ); ?></button>
						<a class="ni-book__link" href="<?php echo esc_url( $pdf ); ?>" download><?php echo esc_html( $ar ? 'تحميل PDF' : 'Download PDF' ); ?></a>
					<?php else : ?>
						<span class="ni-book__soon"><?php echo esc_html( $ar ? 'المعاينة قريبًا' : 'Preview coming soon' ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
	}
	if ( ! $books ) {
		?>
		<article class="ni-book ni-book--empty">
			<div class="ni-book__cover" aria-hidden="true"><span class="ni-book__blank"><span class="ni-book__blank-mark">NIKON</span><span class="ni-book__blank-title"><?php echo esc_html( $ar ? 'إصدارات قادمة' : 'Forthcoming titles' ); ?></span></span><span class="ni-book__spine"></span></div>
			<div class="ni-book__body">
				<h3 class="ni-book__title"><?php echo esc_html( $ar ? 'كتب المعهد قيد الإعداد' : 'Our first books are on the way' ); ?></h3>
				<p class="ni-book__text"><?php echo esc_html( $ar ? 'ستُنشر هنا كتب المعهد ودراساته المطوّلة، مع إمكانية معاينة كل كتاب مباشرة في المتصفح أو تحميله.' : 'Books and long-form studies from the Institute will appear here. Each title can be previewed right in your browser or downloaded as a PDF.' ); ?></p>
			</div>
		</article>
		<?php
	}
	echo '</div>';
	?>
	<div class="ni-viewer" hidden>
		<div class="ni-viewer__bar">
			<strong class="ni-viewer__title"></strong>
			<a class="ni-viewer__dl" href="#" download><?php echo esc_html( $ar ? 'تحميل' : 'Download' ); ?></a>
			<button type="button" class="ni-viewer__close" aria-label="<?php echo esc_attr( $ar ? 'إغلاق' : 'Close' ); ?>">×</button>
		</div>
		<iframe class="ni-viewer__frame" title="PDF" src="about:blank"></iframe>
	</div>
	<?php
	return ob_get_clean();
} );

/* -------------------------------------------------------------------------
 * Email chooser: every mailto link opens a small "write to us" box.
 * ---------------------------------------------------------------------- */
add_action( 'wp_footer', function () {
	$ar = ni_is_rtl();
	?>
	<div class="ni-mailbox" hidden role="dialog" aria-modal="true" aria-labelledby="ni-mailbox-title">
		<div class="ni-mailbox__card">
			<button type="button" class="ni-mailbox__x" aria-label="<?php echo esc_attr( $ar ? 'إغلاق' : 'Close' ); ?>">×</button>
			<div class="ni-eyebrow"><span></span><?php echo esc_html( $ar ? 'راسلنا' : 'Write to us' ); ?></div>
			<h3 id="ni-mailbox-title" class="ni-mailbox__addr" dir="ltr">info@nikoninstitute.org</h3>
			<div class="ni-mailbox__opts">
				<a class="ni-mailbox__opt" data-kind="gmail" target="_blank" rel="noopener" href="#"><i class="ph ph-google-logo"></i><?php echo esc_html( $ar ? 'الكتابة عبر Gmail' : 'Write in Gmail' ); ?></a>
				<a class="ni-mailbox__opt" data-kind="outlook" target="_blank" rel="noopener" href="#"><i class="ph ph-microsoft-outlook-logo"></i><?php echo esc_html( $ar ? 'الكتابة عبر Outlook' : 'Write in Outlook' ); ?></a>
				<a class="ni-mailbox__opt" data-kind="mailto" href="#"><i class="ph ph-envelope-simple"></i><?php echo esc_html( $ar ? 'تطبيق البريد على جهازك' : 'Your email app' ); ?></a>
				<button type="button" class="ni-mailbox__opt" data-kind="copy" data-done="<?php echo esc_attr( $ar ? 'تم نسخ العنوان' : 'Address copied' ); ?>"><i class="ph ph-copy"></i><span><?php echo esc_html( $ar ? 'نسخ العنوان' : 'Copy address' ); ?></span></button>
				<a class="ni-mailbox__opt ni-mailbox__opt--main" href="<?php echo esc_url( ni_url( 'contact' ) ); ?>#ni-form-contact"><i class="ph ph-paper-plane-tilt"></i><?php echo esc_html( $ar ? 'أرسل رسالة من الموقع' : 'Send a message from this site' ); ?></a>
			</div>
		</div>
	</div>
	<?php
} );
