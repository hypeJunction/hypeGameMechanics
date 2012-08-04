<?php

$period = get_input('period', null);
switch ($period) {
	case 'year' :
		$created_time_lower = time() - 365*24*60*60;
		break;

	case 'month' :
		$created_time_lower = time() - 30*24*60*60;
		break;

	case 'week' :
		$created_time_lower = time() - 7*24*60*60;
		break;

	case 'day' :
		$created_time_lower = time() - 1*24*60*60;
		break;

	default :
		$created_time_lower = null;
		break;
}

$options = array(
	'type' => 'user',
	'annotation_names' => array('gm_score'),
	'limit' => get_input('limit', 10),
	'offset' => get_input('offset', 0),
	'annotation_created_time_lower' => $created_time_lower
);

$users = elgg_get_entities_from_annotation_calculation($options);

foreach ($users as $user) {
	$icon = elgg_view_entity_icon($user, 'tiny');
	$name = elgg_view('output/url', array(
		'text' => $user->name,
		'href' => $user->getURL(),
		'is_trusted' => true
			));
	$user_str = elgg_view_image_block ($icon, $name);
	$score = elgg_get_annotations(array(
		'annotation_names' => array('gm_score'),
		'annotation_owner_guids' => array($user->guid),
		'annotation_calculation' => 'sum',
		'annotation_created_time_lower' => $created_time_lower
			));
	
	$rows .= <<<HTML
<tr>
	<td>$user_str</td>
	<td>$score</td>
</tr>
HTML;
}

$table = <<<HTML
<table class="elgg-table">
	$rows
</table>
HTML;

echo $table;
