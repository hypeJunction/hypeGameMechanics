//<script>

	elgg.provide('elgg.mechanics');

	elgg.mechanics.init = function() {

		/**
		 * Order badges
		 */
		$(".gm-badge-gallery:has(.elgg-state-sortable)").sortable({
			items: 'li.elgg-item',
			//connectWith: '.gm-badge-gallery',
			handle: 'img',
			forcePlaceholderSize: true,
			placeholder: 'gm-badge-placeholder',
			opacity: 0.8,
			revert: 500,
			stop: elgg.mechanics.orderBadges
		});

		$('#fancybox-content .elgg-form-points-award').live('submit', function(e) {
			var $form = $(this);
			$form.ajaxSubmit({
				dataType: 'json',
				data: {
					'X-Requested-With': 'XMLHttpRequest', // simulate XHR
				},
				beforeSend: function() {
					$('body').addClass('gm-state-loading');
				},
				success: function(data) {
					if (data.status >= 0) {
						$.fancybox.close();
					}
					if (data.system_messages) {
						elgg.register_error(data.system_messages.error);
						elgg.system_message(data.system_messages.success);
					}
				},
				error: function() {
					elgg.register_error(elgg.echo('mechanics:ajax:error'));
				},
				complete: function() {
					$('body').removeClass('gm-state-loading');
				}
			});
			return false;
		});

	};

	elgg.mechanics.orderBadges = function(event, ui) {

		var data = ui.item.closest('.gm-badge-gallery').sortable('serialize');

		elgg.action('action/badge/order?' + data);

		ui.item.css('top', 0);
		ui.item.css('left', 0);
	};

	elgg.register_hook_handler('init', 'system', elgg.mechanics.init);

