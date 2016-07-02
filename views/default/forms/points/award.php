<?php

$entity = elgg_extract('entity', $vars);

if (!elgg_instanceof($entity, 'user')
		|| !$entity->canAnnotate(0, 'gm_score_award')) {
	return;
}

echo '<div class="mbl">';
echo elgg_view_title($entity->name);
echo elgg_view('framework/mechanics/user_score', $vars);
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('mechanics:admin:award:amount') . '</label>';
echo elgg_view('input/text', array(
	'name' => 'amount',
	'required' => true,
	'value' => elgg_extract('amount', $vars, 0)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('mechanics:admin:award:note') . '</label>';
echo elgg_view('input/longtext', array(
	'name' => 'note',
	'value' => elgg_extract('note', $vars, '')
));
echo '</div>';

echo '<div class="elgg-foot">';
echo elgg_view('input/hidden', array(
	'name' => 'guid',
	'value' => $entity->guid
));
echo elgg_view('input/submit', array(
	'value' => elgg_echo('mechanics:admin:award')
));
echo '</div>';

?>
<script>
	require(['framework/mechanics/award']);
</script>