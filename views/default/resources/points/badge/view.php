<?php

elgg_push_breadcrumb(elgg_echo('mechanics:points'), elgg_generate_url('points'));
elgg_push_breadcrumb(elgg_echo('mechanics:badges:site'), elgg_generate_url('collection:object:hjbadge:default'));

$guid = elgg_extract('guid', $vars);
$entity = get_entity($guid);

if (!$entity instanceof \hypeJunction\GameMechanics\Badge) {
	throw new \Elgg\EntityNotFoundException();
}

$title = $entity->getDisplayName();

$filter = false;

$sidebar = elgg_view('framework/mechanics/sidebar', [
	'entity' => $entity
]);

$content = elgg_view('framework/mechanics/badge/view', [
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
