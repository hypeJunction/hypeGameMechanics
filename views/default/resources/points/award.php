<?php

elgg_push_breadcrumb(elgg_echo('mechanics:points'), elgg_generate_url('points'));

$guid = elgg_extract('guid', $vars);
$entity = get_entity($guid);

if (!$entity instanceof ElggUser || !$entity->canAnnotate(0, 'gm_score_award')) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_set_page_owner_guid($entity->guid);

$title = elgg_echo('mechanics:admin:award_to', [$entity->getDisplayName()]);

$filter = false;

$sidebar = elgg_view('framework/mechanics/sidebar', [
	'entity' => $entity
]);

$content = elgg_view('framework/mechanics/points/award', [
	'entity' => $entity
]);

if (elgg_is_xhr()) {
	echo $content;

	return;
}

elgg_push_breadcrumb($title);

$layout = elgg_view_layout('default', [
	'title' => $title,
	'content' => $content,
	'filter' => $filter,
	'sidebar' => $sidebar,
]);

echo elgg_view_page($title, $layout);
