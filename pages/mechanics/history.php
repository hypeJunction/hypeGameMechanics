<?php

$user = elgg_get_logged_in_user_entity();

if (!$user) {
	forward(REFERER);
}

$limit = get_input('limit', 50);
$offset = get_input('offset', 0);

$params = array(
	'type' => 'object',
	'subtype' => 'hjannotation',
	'limit' => $limit,
	'offset' => $offset,
	'container_guid' => $user->guid,
	'metadata_name_value_pairs' => array(
		array('name' => 'annotation_name', 'value' => 'gm_score_history'),
	),
	'count' => true
);

$count = elgg_get_entities_from_metadata($params);

$params['count'] = false;

$entities = elgg_get_entities_from_metadata($params);

$title = elgg_view_title(elgg_echo('hj:machanics:points:history'));

$html = elgg_view_entity_list($entities, array(
	'pagination' => true,
	'count' => $count,
	'limit' => $limit,
	'offset' => $offset
));

$html = elgg_view_layout('one_sidebar', array(
	'content' => $title . $html
));

echo elgg_view_page(null, $html);


