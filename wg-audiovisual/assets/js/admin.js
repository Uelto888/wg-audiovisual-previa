/* Seletor de imagens do painel WG. */
jQuery(function ($) {
	$(document).on('click', '.wg-gallery-pick', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.wg-field');
		var $input = $wrap.find('.wg-gallery-input');
		var single = !!$(this).data('single');
		var frame = wp.media({
			title: single ? 'Selecionar imagem' : 'Selecionar fotos',
			button: { text: 'Usar' },
			multiple: single ? false : 'add',
			library: { type: 'image' }
		});
		frame.on('open', function () {
			var sel = frame.state().get('selection');
			String($input.val() || '').split(',').forEach(function (id) {
				id = parseInt(id, 10);
				if (id) { var a = wp.media.attachment(id); a.fetch(); sel.add(a); }
			});
		});
		frame.on('select', function () {
			var ids = [], html = '';
			frame.state().get('selection').each(function (a) {
				ids.push(a.id);
				var s = a.attributes.sizes || {};
				var url = (s.thumbnail || s.medium || s.full || {}).url || a.attributes.url;
				html += '<img src="' + url + '" alt="">';
			});
			$input.val(ids.join(','));
			$wrap.find('.wg-gallery-preview').html(html);
		});
		frame.open();
	});
	$(document).on('click', '.wg-gallery-clear', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.wg-field');
		$wrap.find('.wg-gallery-input').val('');
		$wrap.find('.wg-gallery-preview').empty();
	});
});
