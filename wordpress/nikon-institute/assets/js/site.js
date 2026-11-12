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
