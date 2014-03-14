<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

	elgg.provide('hj.mechanics.base');

	hj.mechanics.base.init = function() {
		$('.hj-mechanics-progressbar')
		.each(function() {
			var val = parseInt($(this).attr('data'));
			$(this).progressbar({value : val});
		})
	};

	elgg.register_hook_handler('init', 'system', hj.mechanics.base.init);
	elgg.register_hook_handler('success', 'framework:ajax', hj.mechanics.base.init);
	
<?php if (FALSE) : ?>
	</script>
	<?php endif;
?>