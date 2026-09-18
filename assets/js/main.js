(function () {
	'use strict';

	function restUrl(path, params) {
		var base = (window.bioFront && bioFront.rest) || '/wp-json/bionaut/v1/';
		var url = new URL(base + path, window.location.origin);
		Object.keys(params || {}).forEach(function (key) {
			if (params[key] !== undefined && params[key] !== '') {
				url.searchParams.set(key, params[key]);
			}
		});
		return url.toString();
	}

	function initBackground() {
		var article = document.querySelector('article[data-bio-bg-src]');
		var overlay = document.querySelector('.page-bg');
		if (!article || window.innerWidth <= 800) {
			return;
		}

		var src = article.getAttribute('data-bio-bg-src');
		if (!src) {
			document.body.style.backgroundImage = 'none';
			return;
		}

		var img = new Image();
		img.onload = function () {
			document.body.style.backgroundImage = 'url(' + src + ')';
			if (overlay) {
				overlay.classList.add('show');
			}
		};
		img.src = src;
	}

	function loadSectionProjects(section) {
		if (section.getAttribute('data-loaded') === '1') {
			return Promise.resolve();
		}
		var remaining = parseInt(section.getAttribute('data-remaining') || '0', 10);
		if (!remaining) {
			section.setAttribute('data-loaded', '1');
			return Promise.resolve();
		}
		if (section.getAttribute('data-loading') === '1') {
			return Promise.resolve();
		}
		section.setAttribute('data-loading', '1');
		var term = section.getAttribute('data-term');
		var offset = section.getAttribute('data-offset') || '4';
		var lang = (window.bioFront && bioFront.lang) || '';
		return fetch(restUrl('projects', { term: term, offset: offset, lang: lang }))
			.then(function (res) {
				return res.json();
			})
			.then(function (data) {
				if (data && data.html) {
					var toggle = section.querySelector('.project-overview--toggle');
					if (toggle) {
						toggle.insertAdjacentHTML('beforebegin', data.html);
					} else {
						section.insertAdjacentHTML('beforeend', data.html);
					}
				}
				section.setAttribute('data-loaded', '1');
				section.setAttribute('data-loading', '0');
			})
			.catch(function () {
				section.setAttribute('data-loading', '0');
			});
	}

	function initCategoryToggles() {
		var sections = document.querySelectorAll('.project-overview');
		if (!sections.length) {
			return;
		}

		function expand(section) {
			section.classList.add('expanded');
			if (section.id) {
				history.replaceState({}, document.title, '#' + section.id);
				section.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		}

		function collapse(section) {
			section.classList.remove('expanded');
			if (location.hash) {
				history.replaceState({}, document.title, location.pathname + location.search);
			}
		}

		sections.forEach(function (section) {
			var toggle = section.querySelector('.project-overview--toggle');
			var header = section.querySelector('.project-overview--header');
			function onToggle() {
				if (section.classList.contains('expanded')) {
					collapse(section);
					return;
				}
				loadSectionProjects(section).then(function () {
					expand(section);
				});
			}
			if (toggle) {
				toggle.addEventListener('click', onToggle);
			}
			if (header) {
				header.addEventListener('click', onToggle);
			}
		});

		if (location.hash) {
			var target = document.getElementById(location.hash.slice(1));
			if (target && target.classList.contains('project-overview')) {
				loadSectionProjects(target).then(function () {
					expand(target);
				});
			}
		}
	}

	function initNewsMore() {
		var btn = document.querySelector('[data-bio-news-more]');
		var grid = document.querySelector('[data-bio-news]');
		if (!btn || !grid) {
			return;
		}
		btn.addEventListener('click', function () {
			if (btn.disabled) {
				return;
			}
			var page = parseInt(grid.getAttribute('data-page') || '1', 10) + 1;
			var lang = (window.bioFront && bioFront.lang) || '';
			btn.disabled = true;
			fetch(restUrl('news', { news_page: page, lang: lang }))
				.then(function (res) {
					return res.json();
				})
				.then(function (data) {
					if (data && data.html) {
						grid.insertAdjacentHTML('beforeend', data.html);
						grid.setAttribute('data-page', String(page));
					}
					if (!data || !data.has_more) {
						btn.parentNode.remove();
					} else {
						btn.disabled = false;
					}
				})
				.catch(function () {
					btn.disabled = false;
				});
		});
	}

	function initSiteHeader() {
		var siteHeader = document.getElementById('masthead');
		var menuBtn = document.getElementById('menuBtnID');
		if (!siteHeader) {
			return;
		}

		var lastScrollTop = 0;
		var scrollDist = 0;
		var scrollMin = 20;
		var ticking = false;

		function isPortrait() {
			return window.matchMedia('(orientation: portrait)').matches;
		}

		function setMenuOpen(open) {
			siteHeader.classList.toggle('is-open', open);
			document.body.classList.toggle('nav-open', open);
			if (menuBtn) {
				menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
			}
		}

		function toggleSiteHeader(hide) {
			if (hide) {
				siteHeader.classList.add('scrolled-OFF');
				setMenuOpen(false);
			} else {
				siteHeader.classList.remove('scrolled-OFF');
			}
		}

		if (menuBtn) {
			menuBtn.addEventListener('click', function () {
				setMenuOpen(menuBtn.getAttribute('aria-expanded') !== 'true');
			});
		}

		siteHeader.addEventListener('click', function (event) {
			if (event.target.closest('.menu-container a')) {
				setMenuOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				setMenuOpen(false);
			}
		});

		window.addEventListener('resize', function () {
			if (!isPortrait()) {
				setMenuOpen(false);
			}
		});

		window.addEventListener(
			'scroll',
			function () {
				if (ticking) {
					return;
				}
				ticking = true;
				window.requestAnimationFrame(function () {
					var scrollTop = document.scrollingElement ? document.scrollingElement.scrollTop : window.pageYOffset;
					var scrollDir = scrollTop > lastScrollTop ? 'down' : 'up';

					if (scrollDir === 'up') {
						scrollDist = lastScrollTop - scrollTop;
					}

					lastScrollTop = scrollTop < 0 ? 0 : scrollTop;

					if (
						(scrollDir === 'up' && scrollTop < 150) ||
						(scrollDir === 'up' && scrollDist > scrollMin)
					) {
						toggleSiteHeader(false);
					} else if (scrollDir === 'down' && scrollTop > 150) {
						toggleSiteHeader(true);
					}

					ticking = false;
				});
			},
			{ passive: true }
		);
	}

	document.addEventListener('DOMContentLoaded', function () {
		initBackground();
		initCategoryToggles();
		initNewsMore();
		initSiteHeader();
	});
})();
