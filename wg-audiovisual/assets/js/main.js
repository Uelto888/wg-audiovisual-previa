/* WG Audiovisual — interações do site (sem dependências). */
(function () {
	'use strict';

	var doc = document;
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function $(sel, ctx) { return (ctx || doc).querySelector(sel); }
	function $$(sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); }

	/* Medição (dataLayer) ------------------------------------------------ */
	function track(event, params) {
		window.dataLayer = window.dataLayer || [];
		var data = { event: event };
		for (var k in params) { if (Object.prototype.hasOwnProperty.call(params, k)) { data[k] = params[k]; } }
		window.dataLayer.push(data);
		if (typeof window.gtag === 'function') { window.gtag('event', event, params || {}); }
	}

	/* Cabeçalho ---------------------------------------------------------- */
	var header = $('[data-header]');
	function onScroll() {
		if (header) { header.classList.toggle('is-scrolled', window.scrollY > 24); }
	}
	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });

	/* Menu mobile -------------------------------------------------------- */
	var burger = $('[data-burger]');
	var nav = $('[data-nav]');
	function closeNav() {
		if (!burger || !nav) { return; }
		burger.setAttribute('aria-expanded', 'false');
		nav.classList.remove('is-open');
		doc.body.classList.remove('wg-nav-open');
	}
	if (burger && nav) {
		burger.addEventListener('click', function () {
			var open = burger.getAttribute('aria-expanded') !== 'true';
			burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			nav.classList.toggle('is-open', open);
			doc.body.classList.toggle('wg-nav-open', open);
		});
		doc.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeNav(); } });
		$$('a', nav).forEach(function (a) {
			a.addEventListener('click', function () { if (a.getAttribute('href').charAt(0) === '#') { closeNav(); } });
		});
	}

	// Botão de submenu (o menu automático não traz o botão).
	$$('.wg-menu .menu-item-has-children').forEach(function (li) {
		var btn = li.querySelector(':scope > .wg-submenu-toggle');
		var sub = li.querySelector(':scope > .sub-menu');
		if (!sub) { return; }
		if (!btn) {
			btn = doc.createElement('button');
			btn.className = 'wg-submenu-toggle';
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-label', 'Abrir submenu');
			btn.innerHTML = '<svg class="wg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
			li.insertBefore(btn, sub);
		}
		btn.addEventListener('click', function () {
			var open = btn.getAttribute('aria-expanded') !== 'true';
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			sub.classList.toggle('is-open', open);
		});
	});

	/* Vídeo do topo (apenas telas grandes, sem economia de dados) -------- */
	var video = $('[data-hero-video]');
	if (video) {
		var conn = navigator.connection || {};
		var allow = window.matchMedia('(min-width: 768px)').matches && !reduceMotion && !conn.saveData;
		if (allow) {
			if (video.dataset.webm) {
				var s1 = doc.createElement('source');
				s1.src = video.dataset.webm; s1.type = 'video/webm';
				video.appendChild(s1);
			}
			var s2 = doc.createElement('source');
			s2.src = video.dataset.mp4; s2.type = 'video/mp4';
			video.appendChild(s2);
			video.load();
			video.addEventListener('playing', function () { video.classList.add('is-playing'); }, { once: true });
			var p = video.play();
			if (p && p.catch) { p.catch(function () {}); }
		}
	}

	/* Animações de entrada ----------------------------------------------- */
	var reveals = $$('.wg-reveal');
	if ('IntersectionObserver' in window && !reduceMotion) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) {
					en.target.classList.add('is-in');
					io.unobserve(en.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		// Pequeno atraso em cascata dentro do mesmo grupo.
		reveals.forEach(function (el) {
			var parent = el.parentElement;
			if (parent) {
				var siblings = Array.prototype.filter.call(parent.children, function (c) { return c.classList.contains('wg-reveal'); });
				var idx = siblings.indexOf(el);
				if (idx > 0) { el.style.setProperty('--d', Math.min(idx * 0.06, 0.36) + 's'); }
			}
			io.observe(el);
		});
	} else {
		reveals.forEach(function (el) { el.classList.add('is-in'); });
	}

	/* Contadores dos indicadores ----------------------------------------- */
	$$('[data-count]').forEach(function (el) {
		var raw = el.getAttribute('data-count');
		var m = raw.match(/^(\D*)([\d.]+)(.*)$/);
		if (!m || reduceMotion || !('IntersectionObserver' in window)) { return; }
		var target = parseFloat(m[2].replace(/\./g, ''));
		if (!isFinite(target) || target <= 0) { return; }
		var fmt = function (n) { return m[1] + Math.round(n).toLocaleString('pt-BR') + m[3]; };
		el.textContent = fmt(0);
		var obs = new IntersectionObserver(function (entries) {
			if (!entries[0].isIntersecting) { return; }
			obs.disconnect();
			var start = null;
			function step(ts) {
				if (!start) { start = ts; }
				var t = Math.min((ts - start) / 1400, 1);
				el.textContent = fmt(target * (1 - Math.pow(1 - t, 3)));
				if (t < 1) { requestAnimationFrame(step); } else { el.textContent = raw; }
			}
			requestAnimationFrame(step);
		});
		obs.observe(el);
	});

	/* Filtro do portfólio ------------------------------------------------ */
	$$('[data-filter-group]').forEach(function (group) {
		var items = $$('[data-filter-items] [data-seg]', group.parentElement);
		group.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-filter]');
			if (!btn) { return; }
			var f = btn.getAttribute('data-filter');
			$$('[data-filter]', group).forEach(function (b) { b.classList.toggle('is-active', b === btn); });
			items.forEach(function (it) {
				var segs = (it.getAttribute('data-seg') || '').split(' ');
				it.classList.toggle('is-hidden', f !== '*' && segs.indexOf(f) === -1);
			});
		});
	});

	/* Galeria com ampliação ---------------------------------------------- */
	$$('[data-gallery]').forEach(function (gal) {
		gal.addEventListener('click', function (e) {
			var a = e.target.closest('a');
			if (!a) { return; }
			e.preventDefault();
			var box = doc.createElement('div');
			box.className = 'wg-lightbox';
			box.setAttribute('role', 'dialog');
			box.setAttribute('aria-modal', 'true');
			box.innerHTML = '<img alt=""><button type="button" aria-label="Fechar">&times;</button>';
			box.querySelector('img').src = a.href;
			function close() { box.remove(); doc.removeEventListener('keydown', esc); a.focus(); }
			function esc(ev) { if (ev.key === 'Escape') { close(); } }
			box.addEventListener('click', function (ev) { if (ev.target === box || ev.target.tagName === 'BUTTON') { close(); } });
			doc.addEventListener('keydown', esc);
			doc.body.appendChild(box);
			box.querySelector('button').focus();
		});
	});

	/* Cliques no WhatsApp ------------------------------------------------ */
	doc.addEventListener('click', function (e) {
		var a = e.target.closest('[data-wa-click]');
		if (a) { track('whatsapp_click', { position: a.getAttribute('data-wa-click'), page_path: location.pathname }); }
	});

	/* Origem da visita (UTM) --------------------------------------------- */
	try {
		var params = new URLSearchParams(location.search);
		var utm = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid']
			.filter(function (k) { return params.get(k); })
			.map(function (k) { return k + '=' + params.get(k); }).join('&');
		if (utm) { sessionStorage.setItem('wg_utm', utm); }
		if (!sessionStorage.getItem('wg_landing')) { sessionStorage.setItem('wg_landing', location.pathname + (doc.referrer ? ' (vindo de ' + doc.referrer + ')' : '')); }
	} catch (err) { /* armazenamento indisponível */ }

	/* Formulário de orçamento -------------------------------------------- */
	var wrap = $('[data-quote]');
	if (wrap) { initQuote(wrap); }

	function initQuote(wrap) {
		var form = $('[data-form]', wrap);
		if (!form) { return; }
		var steps = $$('[data-step]', form);
		var progress = $$('[data-progress] li', form);
		var alertBox = $('[data-form-alert]', form);
		var current = 0;

		try {
			$('[data-utm]', form).value = sessionStorage.getItem('wg_utm') || '';
			$('[data-origin]', form).value = sessionStorage.getItem('wg_landing') || doc.referrer || '';
		} catch (err) { /* sem armazenamento */ }

		// Máscara simples de telefone.
		$$('[data-mask="phone"]', form).forEach(function (inp) {
			inp.addEventListener('input', function () {
				var d = inp.value.replace(/\D/g, '').slice(0, 11);
				var out = d;
				if (d.length > 2) { out = '(' + d.slice(0, 2) + ') ' + d.slice(2); }
				if (d.length > 7) { out = '(' + d.slice(0, 2) + ') ' + d.slice(2, d.length - 4) + '-' + d.slice(-4); }
				inp.value = out;
			});
		});

		// Nome do arquivo escolhido.
		var file = $('input[type="file"]', form);
		if (file) {
			file.addEventListener('change', function () {
				var label = $('[data-file-label]', form);
				var f = file.files && file.files[0];
				if (f && f.size > 10 * 1024 * 1024) {
					setError(file.closest('.wg-field'), 'O arquivo deve ter até 10 MB.');
					file.value = '';
					label.textContent = 'Escolher arquivo';
					return;
				}
				clearError(file.closest('.wg-field'));
				label.textContent = f ? f.name : 'Escolher arquivo';
			});
		}

		function show(i) {
			current = i;
			steps.forEach(function (s, n) { s.classList.toggle('is-active', n === i); });
			progress.forEach(function (p, n) {
				p.classList.toggle('is-active', n === i);
				p.classList.toggle('is-done', n < i);
			});
			var top = wrap.getBoundingClientRect().top + window.scrollY - 100;
			if (window.scrollY > top) { window.scrollTo({ top: top, behavior: reduceMotion ? 'auto' : 'smooth' }); }
			var first = steps[i].querySelector('input:not([type=hidden]), select, textarea');
			if (first) { first.focus({ preventScroll: true }); }
			track('quote_step', { step: i + 1 });
		}

		function setError(container, msg) {
			if (!container) { return; }
			container.classList.add('has-error');
			var el = container.querySelector('.wg-error');
			if (!el) {
				el = doc.createElement('span');
				el.className = 'wg-error';
				el.setAttribute('role', 'alert');
				container.appendChild(el);
			}
			el.textContent = msg;
			var input = container.querySelector('input, select, textarea');
			if (input) { input.setAttribute('aria-invalid', 'true'); }
		}
		function clearError(container) {
			if (!container) { return; }
			container.classList.remove('has-error');
			var el = container.querySelector('.wg-error');
			if (el) { el.remove(); }
			var input = container.querySelector('[aria-invalid]');
			if (input) { input.removeAttribute('aria-invalid'); }
		}

		var messages = {
			nome: 'Informe seu nome.',
			empresa: 'Informe a empresa.',
			email: 'Informe um e-mail válido.',
			whatsapp: 'Informe o número com DDD.',
			tipo_evento: 'Selecione o tipo de evento.',
			data_evento: 'Informe a data (mesmo que aproximada).',
			cidade: 'Informe a cidade.',
			participantes: 'Informe o número estimado de participantes.',
			servicos: 'Selecione pelo menos uma opção.',
			lgpd: 'É preciso concordar para receber a proposta.'
		};

		function validateStep(i) {
			var ok = true;
			var firstBad = null;
			$$('[required]', steps[i]).forEach(function (inp) {
				var box = inp.closest('.wg-field') || inp.closest('.wg-consent-check');
				var bad = inp.type === 'checkbox' ? !inp.checked : !inp.value.trim();
				if (inp.type === 'email' && inp.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inp.value)) { bad = true; }
				if (inp.name === 'whatsapp' && inp.value.replace(/\D/g, '').length < 10) { bad = true; }
				if (bad) {
					ok = false;
					if (inp.type === 'checkbox') { box.classList.add('has-error'); } else { setError(box, messages[inp.name] || 'Campo obrigatório.'); }
					firstBad = firstBad || inp;
				} else if (box) {
					if (inp.type === 'checkbox') { box.classList.remove('has-error'); } else { clearError(box); }
				}
			});
			var group = $('[data-group="servicos"]', steps[i]);
			if (group) {
				var any = $$('input:checked', group).length > 0;
				group.classList.toggle('has-error', !any);
				var hint = $('#q-srv-hint', form);
				if (!any) {
					ok = false;
					if (hint) { hint.textContent = messages.servicos; hint.style.color = 'var(--c-error)'; }
					firstBad = firstBad || group.querySelector('input');
				} else if (hint) {
					hint.textContent = 'Selecione uma ou mais opções.'; hint.style.color = '';
				}
			}
			if (firstBad) { firstBad.focus(); }
			return ok;
		}

		form.addEventListener('input', function (e) {
			var box = e.target.closest('.wg-field');
			if (box && box.classList.contains('has-error')) { clearError(box); }
		});
		form.addEventListener('change', function (e) {
			var c = e.target.closest('.wg-consent-check');
			if (c && e.target.checked) { c.classList.remove('has-error'); }
			var g = e.target.closest('[data-group]');
			if (g && $$('input:checked', g).length) { g.classList.remove('has-error'); var h = $('#q-srv-hint', form); if (h) { h.textContent = 'Selecione uma ou mais opções.'; h.style.color = ''; } }
		});

		$$('[data-next]', form).forEach(function (b) {
			b.addEventListener('click', function () { if (validateStep(current)) { show(current + 1); } });
		});
		$$('[data-prev]', form).forEach(function (b) {
			b.addEventListener('click', function () { show(current - 1); });
		});
		// Enter em campos de texto avança a etapa em vez de enviar.
		form.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' && e.target.tagName === 'INPUT' && current < steps.length - 1) {
				e.preventDefault();
				if (validateStep(current)) { show(current + 1); }
			}
		});

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			for (var i = 0; i < steps.length; i++) {
				if (!validateStep(i)) { show(i); validateStep(i); return; }
			}
			var btn = $('[data-submit]', form);
			btn.classList.add('is-loading');
			btn.setAttribute('aria-busy', 'true');
			alertBox.hidden = true;

			var data = new FormData(form);
			data.set('action', 'wg_quote');
			fetch(window.WG && WG.ajax ? WG.ajax : form.action, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (r) { return r.json().catch(function () { return { ok: false }; }); })
				.then(function (res) {
					btn.classList.remove('is-loading');
					btn.removeAttribute('aria-busy');
					if (res && res.ok) {
						var success = $('[data-success]', wrap);
						$('[data-protocol]', success).textContent = '#' + res.id;
						var wa = $('[data-success-wa]', success);
						if (res.has_wa) { wa.href = res.whatsapp; } else { wa.hidden = true; }
						form.hidden = true;
						success.hidden = false;
						window.scrollTo({ top: wrap.getBoundingClientRect().top + window.scrollY - 110, behavior: 'auto' });
						success.focus({ preventScroll: true });
						track('generate_lead', { form: 'orcamento', lead_id: res.id });
						if (window.WG_TAGS && WG_TAGS.ads && WG_TAGS.adsLabel && typeof window.gtag === 'function') {
							window.gtag('event', 'conversion', { send_to: WG_TAGS.ads + '/' + WG_TAGS.adsLabel });
						}
						return;
					}
					var errs = (res && res.errors) || {};
					var firstStep = null;
					Object.keys(errs).forEach(function (k) {
						if (k === '_form') { return; }
						var inp = form.querySelector('[name="' + k + '"], [name="' + k + '[]"]');
						if (!inp) { return; }
						var stepEl = inp.closest('[data-step]');
						var idx = steps.indexOf(stepEl);
						if (firstStep === null || idx < firstStep) { firstStep = idx; }
						if (k === 'servicos') { $('[data-group="servicos"]', form).classList.add('has-error'); }
						else if (k === 'lgpd') { inp.closest('.wg-consent-check').classList.add('has-error'); }
						else { setError(inp.closest('.wg-field'), errs[k]); }
					});
					alertBox.textContent = errs._form || 'Alguns campos precisam de atenção. Revise e envie novamente.';
					alertBox.hidden = false;
					if (firstStep !== null && firstStep !== current) { show(firstStep); }
					if (window.turnstile) { try { window.turnstile.reset(); } catch (err) {} }
				})
				.catch(function () {
					btn.classList.remove('is-loading');
					alertBox.textContent = 'Não foi possível enviar agora. Verifique sua conexão ou fale conosco pelo WhatsApp.';
					alertBox.hidden = false;
				});
		});
	}

	/* Consentimento de cookies ------------------------------------------- */
	var consent = $('#wg-consent');
	function loadTags() {
		if (!window.WG_TAGS || window.WG_TAGS.loaded) { return; }
		window.WG_TAGS.loaded = true;
		window.gtag('consent', 'update', { ad_storage: 'granted', analytics_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted' });
		var s = doc.createElement('script');
		s.async = true;
		s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(WG_TAGS.ids[0]);
		doc.head.appendChild(s);
		window.gtag('js', new Date());
		WG_TAGS.ids.forEach(function (id) { window.gtag('config', id); });
	}
	if (consent && window.WG_TAGS) {
		var choice = null;
		try { choice = localStorage.getItem('wg_consent'); } catch (err) {}
		if (choice === 'accept') { loadTags(); }
		else if (!choice) { consent.hidden = false; }
		consent.addEventListener('click', function (e) {
			var b = e.target.closest('[data-consent]');
			if (!b) { return; }
			var v = b.getAttribute('data-consent');
			try { localStorage.setItem('wg_consent', v); } catch (err) {}
			consent.hidden = true;
			if (v === 'accept') { loadTags(); }
		});
	}
})();
