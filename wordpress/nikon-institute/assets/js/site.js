/* Nikon Institute — mobile menu + hero slider. */
(function () {
	'use strict';

	// Mobile menu
	var burger = document.querySelector('.ni-burger');
	var mnav = document.getElementById('ni-mnav');
	if (burger && mnav) {
		burger.addEventListener('click', function () {
			var open = mnav.classList.toggle('is-open');
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	// Hero slider
	var hero = document.querySelector('[data-screen-label="Hero"]');
	if (!hero) return;
	var stage = hero.firstElementChild;
	var slides = Array.prototype.slice.call(stage.children, 0, 5).filter(function (el) { return el.querySelector('img'); });
	var dots = Array.prototype.slice.call(hero.querySelectorAll('button[aria-label^="Slide"]'));
	var fills = dots.map(function (b) { return b.querySelector('span span'); });
	var counter = hero.querySelector('span[dir="ltr"]');
	var caps = [];
	try { caps = JSON.parse(hero.getAttribute('data-ni-caps') || '[]'); } catch (e) {}
	var caption = dots.length ? dots[0].parentElement.nextElementSibling : null;
	var prev = hero.querySelector('button[aria-label="Previous slide"]');
	var next = hero.querySelector('button[aria-label="Next slide"]');
	var n = slides.length, i = 0, timer = null;
	if (!n) return;

	function show(k) {
		i = (k + n) % n;
		slides.forEach(function (s, j) {
			s.style.opacity = j === i ? '1' : '0';
			s.style.pointerEvents = j === i ? 'auto' : 'none';
			s.style.transform = j === i ? 'scale(1.09)' : 'scale(1)';
		});
		fills.forEach(function (f, j) {
			if (!f) return;
			f.style.transition = 'none';
			f.style.width = '0%';
			if (j === i) {
				void f.offsetWidth;
				f.style.transition = 'width 5s linear';
				f.style.width = '100%';
			}
		});
		if (counter && counter.firstChild) counter.firstChild.nodeValue = '0' + (i + 1) + ' ';
		if (caption && caps[i]) {
			var last = caption.lastChild;
			if (last && last.nodeType === 3) last.nodeValue = caps[i];
			else caption.appendChild(document.createTextNode(caps[i]));
		}
	}
	function start() { clearInterval(timer); timer = setInterval(function () { show(i + 1); }, 5000); }
	dots.forEach(function (d, j) { d.addEventListener('click', function () { show(j); start(); }); });
	if (prev) prev.addEventListener('click', function () { show(i - 1); start(); });
	if (next) next.addEventListener('click', function () { show(i + 1); start(); });
	requestAnimationFrame(function () { requestAnimationFrame(function () { show(0); start(); }); });
})();

/* v1.1 — donate box, copy buttons, book viewer, email chooser */
(function () {
	'use strict';
	function copyText(t, btn) {
		var done = function () { if (!btn) return; var old = btn.getAttribute('data-label') || btn.textContent; btn.setAttribute('data-label', old); var lbl = btn.querySelector('span') || btn; var prev = lbl.textContent; lbl.textContent = btn.getAttribute('data-done') || '✓'; setTimeout(function () { lbl.textContent = prev; }, 1600); };
		if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(t).then(done, done); }
		else { var a = document.createElement('textarea'); a.value = t; document.body.appendChild(a); a.select(); try { document.execCommand('copy'); } catch (e) {} a.remove(); done(); }
	}
	document.addEventListener('click', function (e) {
		var c = e.target.closest('.ni-copy');
		if (c) { copyText(c.getAttribute('data-copy'), c); }
	});

	// Donate
	var give = document.querySelector('.ni-give');
	if (give) {
		var freq = 'once', amt = 100;
		var fBtns = give.querySelectorAll('[data-freq]');
		var aBtns = give.querySelectorAll('[data-amt]');
		var other = give.querySelector('.ni-give__other input');
		var purpose = give.querySelector('.ni-give__purpose');
		var goTxt = give.querySelector('.ni-give__go-txt');
		var banks = document.getElementById('ni-banks');
		var sum = banks ? banks.querySelector('.ni-banks__sum') : null;
		var giftField = banks ? banks.querySelector('input[name="f_gift"]') : null;
		if (giftField) giftField.readOnly = true;
		var D = give.dataset;
		function label() { return '$' + amt + (freq === 'monthly' ? ' ' + D.month : ''); }
		function sync() {
			goTxt.textContent = D.donate + ' ' + label();
			var line = '$' + amt + ' · ' + (freq === 'monthly' ? D.monthly : D.once) + ' · ' + purpose.value;
			if (sum) sum.textContent = line;
			if (giftField) giftField.value = line;
		}
		fBtns.forEach(function (b) { b.addEventListener('click', function () { freq = b.getAttribute('data-freq'); fBtns.forEach(function (x) { var on = x === b; x.classList.toggle('is-on', on); x.setAttribute('aria-checked', on); }); sync(); }); });
		aBtns.forEach(function (b) { b.addEventListener('click', function () { amt = parseInt(b.getAttribute('data-amt'), 10); other.value = ''; aBtns.forEach(function (x) { var on = x === b; x.classList.toggle('is-on', on); x.setAttribute('aria-checked', on); }); sync(); }); });
		other.addEventListener('input', function () { var v = parseInt(other.value, 10); if (v > 0) { amt = v; aBtns.forEach(function (x) { x.classList.remove('is-on'); x.setAttribute('aria-checked', 'false'); }); sync(); } });
		purpose.addEventListener('change', sync);
		give.querySelector('.ni-give__go').addEventListener('click', function () {
			sync();
			if (!banks) return;
			banks.scrollIntoView({ behavior: 'smooth', block: 'start' });
			banks.classList.remove('is-flash'); void banks.offsetWidth; banks.classList.add('is-flash');
		});
		sync();
	}

	// Book viewer
	var viewer = document.querySelector('.ni-viewer');
	if (viewer) {
		var frame = viewer.querySelector('.ni-viewer__frame');
		var vt = viewer.querySelector('.ni-viewer__title');
		var vdl = viewer.querySelector('.ni-viewer__dl');
		var close = function () { viewer.hidden = true; frame.src = 'about:blank'; document.documentElement.style.overflow = ''; };
		document.addEventListener('click', function (e) {
			var b = e.target.closest('[data-pdf]');
			if (!b) return;
			var url = b.getAttribute('data-pdf');
			if (window.matchMedia('(max-width: 700px)').matches) { window.open(url, '_blank', 'noopener'); return; }
			vt.textContent = b.getAttribute('data-title') || '';
			vdl.href = url; frame.src = url + '#view=FitH';
			viewer.hidden = false; document.documentElement.style.overflow = 'hidden';
		});
		viewer.querySelector('.ni-viewer__close').addEventListener('click', close);
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !viewer.hidden) close(); });
	}

	// Email chooser for every mailto link
	var box = document.querySelector('.ni-mailbox');
	if (box) {
		var addrEl = box.querySelector('.ni-mailbox__addr');
		var current = '';
		var shut = function () { box.hidden = true; };
		document.addEventListener('click', function (e) {
			var a = e.target.closest('a[href^="mailto:"]');
			if (!a || box.contains(a)) return;
			e.preventDefault();
			current = a.getAttribute('href').replace(/^mailto:/, '').split('?')[0];
			addrEl.textContent = current;
			var to = encodeURIComponent(current);
			box.querySelector('[data-kind="gmail"]').href = 'https://mail.google.com/mail/?view=cm&fs=1&to=' + to;
			box.querySelector('[data-kind="outlook"]').href = 'https://outlook.live.com/mail/0/deeplink/compose?to=' + to;
			box.querySelector('[data-kind="mailto"]').href = 'mailto:' + current;
			box.hidden = false;
		});
		box.addEventListener('click', function (e) {
			if (e.target === box || e.target.closest('.ni-mailbox__x')) { shut(); return; }
			var cp = e.target.closest('[data-kind="copy"]');
			if (cp) { copyText(current, cp); return; }
			if (e.target.closest('.ni-mailbox__opt')) setTimeout(shut, 150);
		});
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !box.hidden) shut(); });
	}
})();

/* v1.3 — article enhancements: TOC, progress, drop cap, lists, pull quote */
(function () {
	'use strict';
	var art = document.querySelector('.ni-article');
	if (!art) return;
	var bar = document.querySelector('.ni-progress span');
	function progress() {
		var r = art.getBoundingClientRect(), h = art.offsetHeight - window.innerHeight * 0.6;
		var p = Math.min(1, Math.max(0, -r.top / Math.max(1, h)));
		if (bar) bar.style.width = (p * 100).toFixed(1) + '%';
	}
	window.addEventListener('scroll', progress, { passive: true }); progress();
	// drop cap on first real paragraph
	var paras = art.querySelectorAll('p');
	for (var i = 0; i < paras.length; i++) { var p = paras[i]; if (!p.className && p.textContent.trim().length > 140) { p.classList.add('ni-dropcap'); break; } }
	// lists: short items -> chips, long items -> numbered steps
	art.querySelectorAll('ol').forEach(function (ol) {
		var items = ol.querySelectorAll(':scope > li'); var max = 0;
		items.forEach(function (li) { max = Math.max(max, li.textContent.trim().length); });
		ol.classList.add(max < 60 ? 'ni-chips' : 'ni-steps');
	});
	// closing statement -> pull quote
	var ps = art.querySelectorAll('p');
	if (ps.length) { var last = ps[ps.length - 1], st = last.querySelector('strong'); if (st && st.textContent.trim() === last.textContent.trim() && last.textContent.length > 60) last.classList.add('ni-pull'); }
	// table of contents
	var hs = art.querySelectorAll('h2'), toc = document.querySelector('.ni-toc'), list = toc ? toc.querySelector('ol') : null;
	if (toc && list && hs.length > 2) {
		var links = [];
		hs.forEach(function (h, n) {
			if (!h.id) h.id = 'section-' + (n + 1);
			var li = document.createElement('li'), a = document.createElement('a');
			a.href = '#' + h.id; a.textContent = h.textContent.replace(/:.*$/, '');
			li.appendChild(a); list.appendChild(li); links.push(a);
		});
		toc.hidden = false;
		var spy = function () {
			var cur = 0; hs.forEach(function (h, n) { if (h.getBoundingClientRect().top < 160) cur = n; });
			links.forEach(function (a, n) { a.classList.toggle('is-active', n === cur); });
		};
		window.addEventListener('scroll', spy, { passive: true }); spy();
	}
	// copy-link button feedback
	document.addEventListener('click', function (e) { var b = e.target.closest('.ni-share__copy'); if (b) { b.classList.add('is-done'); setTimeout(function () { b.classList.remove('is-done'); }, 1600); } });
})();

/* v1.3.1 — numeric bars in article tables */
(function () {
  document.querySelectorAll('.ni-article table').forEach(function (t) {
    var rows = Array.prototype.slice.call(t.querySelectorAll('tbody tr'));
    if (!rows.length) return;
    var data = rows.map(function (r) { var c = r.cells[r.cells.length - 1]; return { r: r, c: c, v: c ? parseFloat(c.textContent.trim()) : NaN }; });
    var body = data.filter(function (d) { return !/total|wadar|المجموع|الإجمالي/i.test(d.r.cells[0].textContent); });
    if (body.length < 2 || body.some(function (d) { return isNaN(d.v) || !/^\d+(\.\d+)?$/.test(d.c.textContent.trim()); })) return;
    var max = Math.max.apply(null, body.map(function (d) { return d.v; })) || 1;
    body.forEach(function (d) {
      var n = d.c.textContent.trim();
      d.c.innerHTML = '<span class="ni-bar"><span class="ni-bar__n">' + n + '</span><span class="ni-bar__t"><span class="ni-bar__f" data-w="' + (d.v / max * 100).toFixed(1) + '"></span></span></span>';
    });
    var fill = function () { t.querySelectorAll('.ni-bar__f').forEach(function (f) { f.style.width = f.getAttribute('data-w') + '%'; }); };
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (e) { if (e[0].isIntersecting) { fill(); io.disconnect(); } }, { threshold: .3 });
      io.observe(t);
    } else fill();
  });
})();
