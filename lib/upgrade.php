<?php

namespace hypeJunction\GameMechanics;

ini_set('memory_limit', '512M');
set_time_limit(0);

$ia = elgg_set_ignore_access(true);
$ha = access_get_show_hidden_status();
access_show_hidden_entities(true);

run_function_once('hypeJunction\\GameMechanics\\upgrade_1383300476');
run_function_once('hypeJunction\\GameMechanics\\upgrade_1394887562');

elgg_set_ignore_access($ia);
access_show_hidden_entities($ha);


function upgrade_1383300476() {
	
	$dbprefix = elgg_get_config('dbprefix');

	$subtypeIdAnnotation = get_subtype_id('object', 'hjannotation');
	if (!$subtypeIdAnnotation) {
		return true;
	}

	// Convert badge rules to their own subtype
	add_subtype('object', 'hjbadgerule', 'hypeJunction\\GameMechanics\\hjBadgeRule');
	$subtypeIdRule = get_subtype_id('object', 'hjbadgerule');

	$query = "	UPDATE {$dbprefix}entities e
				JOIN {$dbprefix}metadata md ON md.entity_guid = e.guid
				JOIN {$dbprefix}metastrings msn ON msn.id = md.name_id
				JOIN {$dbprefix}metastrings msv ON msv.id = md.value_id
				SET e.subtype = $subtypeIdRule
				WHERE e.subtype = $subtypeIdAnnotation AND msn.string = 'handler' AND msv.string = 'badge_rule'	";

	update_data($query);
}

function upgrade_1394887562() {

	$dbprefix = elgg_get_config('dbprefix');

	$subtypeIdAnnotation = get_subtype_id('object', 'hjannotation');
	if (!$subtypeIdAnnotation) {
		return true;
	}

	// Convert badge rules to their own subtype
	add_subtype('object', 'gm_score_history');
	$subtypeIdRule = get_subtype_id('object', 'gm_score_history');

	$query = "	UPDATE {$dbprefix}entities e
				JOIN {$dbprefix}metadata md ON md.entity_guid = e.guid
				JOIN {$dbprefix}metastrings msn ON msn.id = md.name_id
				JOIN {$dbprefix}metastrings msv ON msv.id = md.value_id
				SET e.subtype = $subtypeIdRule
				WHERE e.subtype = $subtypeIdAnnotation AND msn.string = 'annotation_name' AND msv.string = 'gm_score_history'";

	update_data($query);
}