define(function (require) {

	var $ = require('jquery');
	var elgg = require('elgg');
	var spinner = require('elgg/spinner');
	
	$(document).on('submit', '#colorbox .elgg-form-points-award', function (e) {
		e.preventDefault();

		var $form = $(this);
		elgg.action($form.prop('action'), {
			data: $form.serialize(),
			beforeSend: spinner.start,
			complete: spinner.stop,
			success: function (data) {
				if (data.status >= 0) {
					$.colorbox.close();
				}
			},
		});
		return false;
	});
});