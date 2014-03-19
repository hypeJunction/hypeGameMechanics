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
		'subtypes' => HYPEGAMEMECHANICS_SCORE_SUBTYPE,
		'container_guids' => $user->guid,
		'metadata_names' => 'annotation_value',
		'metadata_calculation' => 'sum',
		'metadata_created_time_lower' => $time_lower,
		'metadata_created_time_upper' => $time_upper,
	);

	return (int) elgg_get_metadata($options);
}

/**
 * Get a list of users ordered by their total score
 * 
 * @param int $time_lower
 * @param int $time_upper
 */
function get_leaderboard($time_lower = null, $time_upper = null, $limit = 10, $offset = 0) {

	$options = array(
		'types' => 'user',
		'annotation_names' => 'gm_score',
		'annotation_created_time_lower' => $time_lower,
		'annotation_created_time_upper' => $time_upper,
		'limit' => $limit,
		'offset' => $offset,
	);

	return elgg_get_entities_from_annotation_calculation($options);
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
		'subtype' => HYPEGAMEMECHANICS_SCORE_SUBTYPE,
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
 * Get the number of recurrences when user was awarded points for a given rule action on an object
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
		'subtypes' => HYPEGAMEMECHANICS_SCORE_SUBTYPE,
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

	$object_id = (isset($object->guid)) ? $object->guid : $object->id;
	$object_type = $object->getType();

	$dbprefix = elgg_get_config('dbprefix');

	$msn_id = add_metastring('object_ref');
	$msv_id = add_metastring("$object_type:$object_id");

	$options = array(
		'type' => 'object',
		'subtype' => HYPEGAMEMECHANICS_SCORE_SUBTYPE,
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

/**
 * Get the number of recurrences when user was awarded points for a given rule action on an object
 *
 * @param object $object
 * @param ElggUser $user
 * @param string $rule			Rule name
 * @param int $time_lower		Lower time constraint
 * @param int $time_upper		Upper time constraint
 * @return int
 */
function get_object_recur_total($object, $user = null, $rule = null, $time_lower = null, $time_upper = null) {

	if (!is_object($object)) {
		return 0;
	}

	$object_id = (isset($object->guid)) ? $object->guid : $object->id;
	$object_type = $object->getType();

	$options = array(
		'types' => 'object',
		'subtypes' => HYPEGAMEMECHANICS_SCORE_SUBTYPE,
		'container_guids' => $user->guid,
		'created_time_lower' => $time_lower,
		'created_time_upper' => $time_upper,
		'metadata_name_value_pairs' => array(
			array('name' => 'rule', 'value' => $rule),
			array('name' => 'object_ref', 'value' => "$object_type:$object_id")
		),
		'count' => true,
	);

	return elgg_get_entities_from_metadata($options);
}

/**
 * Reward user with applicable badges
 *
 * @param ElggUser $user
 * @return boolean
 */
function reward_user($user = null) {

	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}

	$gmReward = gmReward::rewardUser($user);

	$errors = $gmReward->getErrors();
	if ($errors) {
		foreach ($errors as $error) {
			register_error($error);
		}
	}

	$messages = $gmReward->getMessages();
	if ($messages) {
		foreach ($messages as $message) {
			system_message($message);
		}
	}

	$badges = $gmReward->getNewUserBadges();
	if (count($badges)) {
		foreach ($badges as $badge) {
			system_message(elgg_echo('mechanics:badge:claim:success', array($badge->title)));
			add_to_river('framework/mechanics/river/claim', 'claim', $user->guid, $badge->guid);
		}
	}

	error_log(print_r($gmReward->getLog(), true));

	return true;
}

/**
 * Get site badges
 * @param array $options
 * @return array|false
 */
function get_badges($options = array(), $getter = 'elgg_get_entities_from_metadata') {

	$defaults = array(
		'types' => 'object',
		'subtypes' => HYPEGAMEMECHANICS_BADGE_SUBTYPE,
		'order_by_metadata' => array(
			'name' => 'priority',
			'direction' => 'ASC',
			'as' => 'integer'
		),
	);

	$options = array_merge($defaults, $options);

	if (is_callable($getter)) {
		return $getter($options);
	}

	return elgg_get_entities($options);
}

/**
 * Get badges of a given type
 * 
 * @param string $type
 * @param array $options
 * @param string $getter
 * @return array|false
 */
function get_badges_by_type($type = '', $options = array(), $getter = 'elgg_get_entities_from_metadata') {

	$options['metadata_name_value_pairs'] = array(
		'name' => 'badge_type',
		'value' => $type,
	);

	return get_badges($options, $getter);
}

/**
 * Get types of badges
 * @return array
 */
function get_badge_types() {

	$return = array(
		'status' => elgg_echo('badge_type:value:status'),
		'experience' => elgg_echo('badge_type:value:experience'),
		//'purchase' => elgg_echo('badge_type:value:purchase'),
		'surprise' => elgg_echo('badge_type:value:surprise')
	);

	$return = elgg_trigger_plugin_hook('mechanics:badge_types', 'object', null, $return);

	return $return;
}

/**
 * Get badges that are required to uncover this badge
 * @param int $badge_guid
 * @return array|false
 */
function get_badge_dependencies($badge_guid) {

	return elgg_get_entities_from_relationship(array(
		'types' => 'object',
		'subtypes' => HYPEGAMEMECHANICS_BADGE_SUBTYPE,
		'relationship' => HYPEGAMEMECHANICS_DEPENDENCY_REL,
		'relationship_guid' => $badge_guid,
		'inverse_relationship' => true
	));
}

/**
 * Get badge rules
 * @param int $badge_guid
 * @return array|false
 */
function get_badge_rules($badge_guid) {

	return elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => HYPEGAMEMECHANICS_BADGERULE_SUBTYPE,
		'container_guid' => $badge_guid,
		'limit' => 10,
	));
}
