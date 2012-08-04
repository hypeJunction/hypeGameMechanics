<?php

$user_guid = get_input('user_guid');

if (!$user = get_entity($user_guid)) {
	register_error(elgg_echo('hj:framework:error'));
	forward(REFERER);
}

$options = array(
	'annotation_owner_guids' => array($user->guid),
	'annotation_names' => array('gm_score'),
	'limit' => 0
);
elgg_delete_annotations($options);

$gm_score_history = elgg_get_entities_from_metadata(array(
	'type' => 'object',
	'subtype' => 'hjannotation',
	'owner_guid' => $user->guid,
	'limit' => 0,
	'metadata_name' => 'annotation_name',
	'metadata_value' => 'gm_score_history'
		));

foreach ($gm_score_history as $gmsh) {
	system_message("$gm_score_history->guid");
	$gmsh->delete();
}

system_message(elgg_echo('hj:framework:success'));

forward(REFERER);
