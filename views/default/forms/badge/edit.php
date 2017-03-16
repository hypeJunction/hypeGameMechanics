<?php

namespace hypeJunction\GameMechanics;

$entity = elgg_extract('entity', $vars);

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:icon') . '</label>';
echo elgg_view('input/file', array(
	'name' => 'icon',
	'value' => (isset($entity->icontime)),
	'required' => (!isset($entity->icontime)),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:title') . '</label>';
echo elgg_view('input/text', array(
	'name' => 'title',
	'required' => true,
	'value' => elgg_extract('title', $vars, $entity->title)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:description') . '</label>';
echo elgg_view('input/longtext', array(
	'name' => 'description',
	'value' => elgg_extract('description', $vars, $entity->description)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:badge_type') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'badge_type',
	'value' => elgg_extract('badge_type', $vars, $entity->badge_type),
	'options_values' => get_badge_types(),
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:rules') . '</label>';
$rules = ($entity) ? get_badge_rules($entity->guid) : null;
echo elgg_view('input/mechanics/rules', array(
	'value' => elgg_extract('rules', $vars, $rules)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:points_required') . '</label>';
echo elgg_view('input/text', array(
	'name' => 'points_required',
	'value' => (int) elgg_extract('points_required', $vars, $entity->points_required)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:points_cost') . '</label>';
echo elgg_view('input/text', array(
	'name' => 'points_cost',
	'value' => (int) elgg_extract('points_cost', $vars, $entity->points_cost)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:badges_required') . '</label>';
$dependecies = ($entity) ? get_badge_dependencies($entity->guid) : null;
echo elgg_view('input/mechanics/dependencies', array(
	'entity' => $entity,
	'value' => elgg_extract('dependencies', $vars, $dependecies)
));
echo '</div>';

echo '<div class="elgg-foot">';
echo elgg_view('input/hidden', array(
	'name' => 'guid',
	'value' => $entity->guid
));
echo elgg_view('input/hidden', array(
	'name' => 'access_id',
	'value' => ($entity) ? $entity->access_id : ACCESS_PUBLIC
));

echo elgg_view('input/submit', array(
	'value' => elgg_echo('save')
));
echo '</div>';
