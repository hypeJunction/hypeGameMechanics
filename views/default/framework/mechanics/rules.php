<?php

$entity = elgg_extract('entity', $vars);
$user = elgg_get_logged_in_user_entity();

if (check_entity_relationship($user->guid, 'claimed', $entity->guid)) {
	echo '<div class="hj-mechanics-uncover-badge">' . elgg_echo('mechanics:alreadyclaimed') . '</div>';
	$has = true;
}

$rules = elgg_get_entities_from_metadata(array(
	'type' => 'object',
	'subtype' => 'hjbadgerule',
	'container_guid' => $entity->guid,
	'limit' => 10,
		));

$eligibility = true;

if ($rules) {
	foreach ($rules as $rule) {
		$rule_name = elgg_echo("mechanics:$rule->annotation_value");
		$rule_recurse = $rule->recurse;

		if ($user) {
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
		} else {
			$progress = 0;
		}

		if ($progress < 100) {
			$eligibility = false;
		}
		$html = elgg_view_layout('hj/dynamic', array(
			'grid' => array(10, 2),
			'content' => array($rule_name, "$complete / $rule_recurse")
				));

		echo $html;

		echo elgg_view('output/progressbar', array(
			'value' => $progress,
			'total' => $rule->recurse
		));
	}
}


$points_required = (int) $entity->points_required;
if ($user) {
	$user_points = get_user_score($user);
} else {
	$user_points = 0;
}

if ($points_required > 0) {

	echo elgg_view_layout('hj/dynamic', array(
		'grid' => array(10, 2),
		'content' => array(elgg_echo('mechanics:pointsrequired'), "$user_points / $points_required")
	));
	$progress = round(($user_points / $points_required) * 100);
	echo elgg_view('output/progressbar', array(
		'value' => $progress,
		'total' => $points_required
	));
	if ($progress < 100) {
		$eligibility = false;
	}
}

$badges_required = elgg_get_entities_from_relationship(array(
	'relationship' => 'badge_required',
	'relationship_guid' => $entity->guid,
	'inverse_relationship' => true
		));

if ($badges_required) {
	echo elgg_echo('mechanics:badgesrequired') . '<br />';
	echo '<ul class="elgg-gallery hj-badge-gallery">';
	foreach ($badges_required as $badge) {
		if (check_entity_relationship($user->guid, 'claimed', $badge->guid)) {
			$class = "hj-check";
		} else {
			$eligibility = false;
		}

		echo "<li class=\"elgg-item hj-badge-item $class\">";
		echo elgg_view_entity($badge, array('full_view' => false));
		echo '</li>';
	}
	echo '</ul>';
}

$points_cost = (int) $entity->points_cost;
if ($points_cost) {
	echo elgg_echo('mechanics:badge:pointscost', array($points_cost));
	if ($user_points - $points_cost < $points_required) {
		$eligibility = false;
	}
}

if ($eligibility && !$has) {
	echo '<div class="hj-mechanics-uncover-badge">';
	echo elgg_echo('mechanics:badge:congratulations') . '<br />';
	echo elgg_view('output/url', array(
		'text' => elgg_view('input/button', array('value' => elgg_echo('mechanics:badge:claim'), 'class' => 'elgg-button-action')),
		'href' => "action/badge/claim?e=$entity->guid",
		'is_action' => true,
		'encode_text' => false
	));
	echo '</div>';
}
