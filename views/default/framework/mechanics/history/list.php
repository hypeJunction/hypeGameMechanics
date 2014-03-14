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

$limit = get_input('limit', 10);
$offset = get_input('offset', 0);

$user = elgg_extract('user', $vars, elgg_get_page_owner_entity());
$params = array(
	'type' => 'object',
	'subtype' => 'hjannotation',
	'limit' => $limit,
	'offset' => $offset,
	'container_guid' => $user->guid,
	'metadata_name_value_pairs' => array(
		array('name' => 'annotation_name', 'value' => 'gm_score_history'),
	),
	'count' => true,
);

if ($created_time_lower) {
	$params['wheres'] = array("e.time_created > $created_time_lower");
}

$score = '<div>' . elgg_echo('mechanics:currentscore', array(get_user_score($user))) . '</div>';
$list = elgg_list_entities_from_metadata($params);


echo $score;
echo $list;
