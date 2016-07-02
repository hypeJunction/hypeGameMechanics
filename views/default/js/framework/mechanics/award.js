define(function (require) {

	var $ = require('jquery');
	var elgg = require('elgg');
	require('jquery.form');

	$(document).on('submit', '#colorbox .elgg-form-points-award', function (e) {
		var $form = $(this);
		$form.ajaxSubmit({
			dataType: 'json',
			data: {
				'X-Requested-With': 'XMLHttpRequest', // simulate XHR
			},
			beforeSend: function () {
				$('body').addClass('gm-state-loading');
			},
			success: function (data) {
				if (data.status >= 0) {
					$.fancybox.close();
				}
				if (data.system_messages) {
					elgg.register_error(data.system_messages.error);
					elgg.system_message(data.system_messages.success);
				}
			},
			error: function () {
				elgg.register_error(elgg.echo('mechanics:ajax:error'));
			},
			complete: function () {
				$('body').removeClass('gm-state-loading');
			}
		});
		return false;
	});
});