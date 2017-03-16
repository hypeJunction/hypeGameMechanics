<?php

namespace hypeJunction\GameMechanics;

use ElggFile;
use ElggObject;

elgg_make_sticky_form('badge/edit');

$guid = get_input('guid');
$title = get_input('title', '');
$access_id = get_input('access_id', ACCESS_PUBLIC);
$description = get_input('description', '');
$badge_type = get_input('badge_type', '');
$rules = get_input('rules', array());
$dependencies = get_input('dependencies', array());
$points_required = get_input('points_required', 0);
$points_cost = get_input('points_cost', 0);

if (!$title) {
	register_error(elgg_echo('mechanics:badge:edit:error_empty_title'));
	forward(REFERER);
}

$icon_uploaded = (!empty($_FILES['icon']['name']) && $_FILES['icon']['error'] == UPLOAD_ERR_OK && substr_count($_FILES['icon']['type'], 'image/'));

$entity = get_entity($guid);
$site = elgg_get_site_entity();


if (!elgg_instanceof($entity)) {
	$new = true;

	// Badge icon must be provided for new badges
	if (!$icon_uploaded) {
		register_error(elgg_echo('mechanics:badge:edit:error_upload'));
		forward(REFERER);
	}

	$entity = new ElggObject();
	$entity->subtype = HYPEGAMEMECHANICS_BADGE_SUBTYPE;
	$entity->owner_guid = $site->guid;
	$entity->container_guid = $site->guid;

	$entity->priority = '';
}

$entity->title = $title;
$entity->description = $description;
$entity->access_id = $access_id;

$entity->badge_type = $badge_type;
$entity->points_required = $points_required;
$entity->points_cost = $points_cost;

if ($entity->save()) {

	for ($i = 0; $i < 10; $i++) {

		$guid = (int) $rules['guid'][$i];
		$name = $rules['name'][$i];
		$recurse = (int) $rules['recurse'][$i];

		if ($name && $recurse) {
			$badge_rule = new ElggObject($guid);
			$badge_rule->subtype = HYPEGAMEMECHANICS_BADGERULE_SUBTYPE;
			$badge_rule->owner_guid = $entity->owner_guid;
			$badge_rule->container_guid = $entity->guid;
			$badge_rule->access_id = $entity->access_id;
			$badge_rule->annotation_name = 'badge_rule';
			$badge_rule->annotation_value = $name;
			$badge_rule->recurse = (int) $recurse;
			$badge_rule->save();
		} else if ($guid) {
			$redundant = get_entity($guid);
			$redundant->delete();
		}
	}

	$current_dependency_guids = array();
	$current_dependencies = get_badge_dependencies($entity->guid);
	if ($current_dependencies) {
		foreach ($current_dependencies as $cd) {
			$current_dependency_guids[] = $cd->guid;
		}
	}

	if (is_array($dependencies)) {
		$future_dependency_guids = array_filter($dependencies);
	} else {
		$future_dependency_guids = array();
	}

	$to_remove = array_diff($current_dependency_guids, $future_dependency_guids);
	$to_add = array_diff($future_dependency_guids, $current_dependency_guids);

	foreach ($to_remove as $dep_guid) {
		remove_entity_relationship($dep_guid, HYPEGAMEMECHANICS_DEPENDENCY_REL, $entity->guid);
	}

	foreach ($to_add as $dep_guid) {
		add_entity_relationship($dep_guid, HYPEGAMEMECHANICS_DEPENDENCY_REL, $entity->guid);
	}

	if ($icon_uploaded) {
		$entity->icontime = time();
		$icon_sizes = elgg_get_config('icon_sizes');
		foreach ($icon_sizes as $size => $dimensions) {
			$icon = new ElggFile();
			$icon->owner_guid = $entity->owner_guid;
			$icon->setFilename("icons/{$entity->guid}{$size}.jpg");
			$contents = get_resized_image_from_existing_file($_FILES['icon']['tmp_name'], $dimensions['w'], $dimensions['h'], $dimensions['square'], 0, 0, 0, 0, $dimensions['upscale']);
			if ($contents) {
				$icon->open('write');
				$icon->write($contents);
				$icon->close();
			}
		}
	}

	elgg_clear_sticky_form('badge/edit');
	if ($new) {
		system_message(elgg_echo('mechanics:badge:create:success'));
	} else {
		system_message(elgg_echo('mechanics:badge:edit:success'));
	}
	forward(PAGEHANDLER . '/badges');
} else {
	register_error(elgg_echo('mechanics:badge:edit:error'));
	forward(REFERER);
}
