<?php

namespace hypeJunction\GameMechanics;

/**
 * Get rule definitions
 * @return array
 */
function get_scoring_rules($type = '') {
	$rules = elgg_trigger_plugin_hook('get_rules', 'gm_score', null, array());

	if ($type && array_key_exists($type, $rules)) {
		return $rules[$type];
	} else {
		return $rules;
	}
}

/**
 * Get user score total in a given time frame
 *
 * @param ElggUser $user
 * @param int $time_lower	Lower time constraint
 * @param int $time_upper	Upper time constraint
 * @return int
 */
function get_user_score($user = null, $time_lower = null, $time_upper = null) {

	if (!elgg_instanceof($user, 'user')) {
		return 0;
	}

	$options = array(
		'types' => 'object',
		'subtypes' => 'gm_score_history',
		'container_guids' => $user->guid,
		'metadata_names' => 'annotation_value',
		'metadata_calculation' => 'sum',
		'metadata_created_time_lower' => $time_lower,
		'metadata_created_time_upper' => $time_upper,
	);

	return (int) elgg_get_metadata($options);
}

/**
 * Get total score for a specified action rule
 *
 * @param ElggUser $user
 * @param string $rule			Rule name
 * @param int $time_lower		Lower time constraint
 * @param int $time_upper		Upper time constraint
 * @return int
 */
function get_user_action_total($user, $rule, $time_lower = null, $time_upper = null) {

	if (empty($rule) || !elgg_instanceof($user, 'user')) {
		return 0;
	}

	$dbprefix = elgg_get_config('dbprefix');
	$msn_id = add_metastring('rule');
	$msv_id = add_metastring($rule);

	$options = array(
		'type' => 'object',
		'subtype' => 'gm_score_history',
		'container_guid' => $user->guid,
		'metadata_names' => 'annotation_value',
		'metadata_calculation' => 'sum',
		'metadata_created_time_lower' => $time_lower,
		'metadata_created_time_upper' => $time_upper,
		'joins' => array(
			"JOIN {$dbprefix}metadata rulemd ON n_table.entity_guid = rulemd.entity_guid"
		),
		'wheres' => array(
			"(rulemd.name_id = $msn_id AND rulemd.value_id = $msv_id)"
		),
	);

	return (int) elgg_get_metadata($options);
}

/**
 * Get the number of recurrences when user was awarded points for a given rule action
 *
 * @param ElggUser $user
 * @param string $rule			Rule name
 * @param int $time_lower		Lower time constraint
 * @param int $time_upper		Upper time constraint
 * @return int
 */
function get_user_recur_total($user, $rule, $time_lower = null, $time_upper = null) {

	if (empty($rule) || !elgg_instanceof($user, 'user')) {
		return 0;
	}

	$options = array(
		'types' => 'object',
		'subtypes' => 'gm_score_history',
		'container_guids' => $user->guid,
		'created_time_lower' => $time_lower,
		'created_time_upper' => $time_upper,
		'metadata_name_value_pairs' => array(
			array('name' => 'rule', 'value' => $rule)
		),
		'count' => true,
	);

	return elgg_get_entities_from_metadata($options);
}


/**
 * Get total score that was collected on an object by a given user with a given rule in given time frame
 *
 * @param object $object
 * @param ElggUser $user
 * @param string $rule
 * @param int $time_lower
 * @param int $time_upper
 * @return int
 */
function get_object_total($object, $user = null, $rule = null, $time_lower = null, $time_upper = null) {

	if (!is_object($object)) {
		return 0;
	}

	$dbprefix = elgg_get_config('dbprefix');
	$object_id = (isset($object->guid)) ? $object->guid : $object->id;
	$object_type = $object->getType();

	$msn_id = add_metastring('object_ref');
	$msv_id = add_metastring("$object_type:$object_id");

	$options = array(
		'type' => 'object',
		'subtype' => 'gm_score_history',
		'container_guid' => $user->guid,
		'metadata_names' => 'annotation_value',
		'metadata_calculation' => 'sum',
		'metadata_created_time_lower' => $time_lower,
		'metadata_created_time_upper' => $time_upper,
		'joins' => array(
			"JOIN {$dbprefix}metadata objmd ON n_table.entity_guid = objmd.entity_guid"
		),
		'wheres' => array(
			"(objmd.name_id = $msn_id AND objmd.value_id = $msv_id)"
		),
	);

	if (!empty($rule)) {
		$msn_id = add_metastring('rule');
		$msv_id = add_metastring($rule);
		$options['joins'][] = "JOIN {$dbprefix}metadata rulemd ON n_table.entity_guid = rulemd.entity_guid";
		$options['wheres'][] = "(rulemd.name_id = $msn_id AND rulemd.value_id = $msv_id)";
	}
	
	return (int) elgg_get_metadata($options);
}


function get_badge_types() {

	$return = array(
		'status' => elgg_echo('badge_type:value:status'),
		'experience' => elgg_echo('badge_type:value:experience'),
		'purchase' => elgg_echo('badge_type:value:purchase'),
		'surprise' => elgg_echo('badge_type:value:surprise')
	);

	$return = elgg_trigger_plugin_hook('mechanics:badge_types', 'object', null, $return);

	return $return;
}

function prepare_badge_relationship_tags() {

	$options = array(
		'types' => 'object',
		'subtypes' => 'hjbadge',
		'relationship_name' => 'badge_required',
		'limit' => 0
	);

	return $options;
}

function check_user_eligibility_for_badge(ElggObject $badge, ElggUser $user) {

	if (check_entity_relationship($user->guid, 'claimed', $badge->guid)) {
		return true;
	}

	$rules = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'badge_rule',
		'container_guid' => $badge->guid,
		'limit' => 10,
	));

	if ($rules) {
		foreach ($rules as $rule) {
			$complete = elgg_get_entities_from_metadata(array(
				'type' => 'object',
				'subtype' => 'gm_score_history',
				'container_guid' => $user->guid,
				'count' => true,
				'metadata_name_value_pairs' => array(
					array('name' => 'rule', 'value' => $rule->annotation_value)
				)
			));

			$progress = round(($complete / $rule->recurse) * 100);

			if ($progress < 100) {
				return false;
			}
		}
	}


	$points_required = (int) $badge->points_required;
	if ($user) {
		$user_points = get_user_score($user);
	} else {
		$user_points = 0;
	}

	if ($points_required > 0) {

		$progress = round(($user_points / $points_required) * 100);
		if ($progress < 100) {
			return false;
		}
	}

	$badges_required = elgg_get_entities_from_relationship(array(
		'relationship' => 'badge_required',
		'relationship_guid' => $badge->guid,
		'inverse_relationship' => true
	));

	if ($badges_required) {
		foreach ($badges_required as $badge) {
			if (!check_entity_relationship($user->guid, 'awarded', $badge->guid)) {
				return false;
			}
		}
	}

	$points_cost = (int) $badge->points_cost;
	if ($points_cost) {
		if ($user_points - $points_cost < $points_required) {
			return false;
		}
	}

	return true;
}
