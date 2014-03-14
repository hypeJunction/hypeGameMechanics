<?php

namespace hypeJunction\GameMechanics;

function get_scoring_rules() {
	return elgg_trigger_plugin_hook('mechanics:scoring:rules', 'all', null, array());
}

function get_scoring_rules_list() {
	$rules = get_scoring_rules();

	foreach ($rules as $type => $rule_grouping) {
		foreach ($rule_grouping as $rule_defs) {
			foreach ($rule_defs as $rule) {
				$unique_name = $rule['unique_name'];
				$options[$unique_name] = elgg_echo("mechanics:$unique_name");
			}
		}
	}
	return $options;
}

function get_user_score($user, $time_lower = null, $time_upper = null) {
	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}
	$params = array(
		'annotation_names' => 'gm_score',
		'annotation_calculation' => 'sum',
		'annotation_created_time_lower' => $time_lower,
		'annotation_created_time_upper' => $time_upper,
		'guids' => $user->guid,
		'limit' => 0
	);

	if (!$score = elgg_get_annotations($params)) {
		$score = 0;
	}

	return $score;
}

function get_user_action_total($user, $rule, $time_lower = null, $time_upper = null) {

	if (!$rule) {
		return 0;
	}

	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}

	$params = array(
		'type' => 'object',
		'subtype' => 'hjannotation',
		'limit' => 0,
		'container_guid' => $user->guid,
		'metadata_name_value_pairs' => array(
			array('name' => 'annotation_name', 'value' => 'gm_score_history'),
			array('name' => 'rule', 'value' => $rule)
		)
	);

	$total = 0;

	$annotations = elgg_get_entities_from_metadata($params);

	if ($annotations) {
		foreach ($annotations as $annotation) {
			$count_params = array(
				'metadata_names' => "annotation_value",
				'metadata_calculation' => 'sum',
				'metadata_created_time_lower' => $time_lower,
				'metadata_created_time_upper' => $time_upper,
				'guids' => $annotation->guid,
				'limit' => 0
			);

			$total = $total + elgg_get_metadata($count_params);
		}
	}

	return $total;
}

function get_user_recur_total($user, $rule, $time_lower = null, $time_upper = null) {

	if (!$rule) {
		return 0;
	}

	if (!$user) {
		$user = elgg_get_logged_in_user_entity();
	}

	$params = array(
		'type' => 'object',
		'subtype' => 'hjannotation',
		'limit' => 0,
		'container_guid' => $user->guid,
		'metadata_name_value_pairs' => array(
			array('name' => 'annotation_name', 'value' => 'gm_score_history'),
			array('name' => 'rule', 'value' => $rule)
		)
	);

	$count = 0;

	$annotations = elgg_get_entities_from_metadata($params);

	if ($annotations) {
		foreach ($annotations as $annotation) {
			$count_params = array(
				'metadata_names' => "annotation_value",
				'metadata_created_time_lower' => $time_lower,
				'metadata_created_time_upper' => $time_upper,
				'guids' => $annotation->guid,
				'count' => true,
			);

			$count = $count + elgg_get_metadata($count_params);
		}
	}

	return $count;
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
		'subtype' => 'hjannotation',
		'container_guid' => $badge->guid,
		'limit' => 10,
		'metadata_name_value_pairs' => array(
			array('name' => 'annotation_name', 'value' => 'badge_rule'),
		)
			));

	if ($rules) {
		foreach ($rules as $rule) {
			$complete = elgg_get_entities_from_metadata(array(
				'type' => 'object',
				'subtype' => 'hjannotation',
				'container_guid' => $user->guid,
				'count' => true,
				'metadata_name_value_pairs' => array(
					array('name' => 'annotation_name', 'value' => 'gm_score_history'),
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

