<?php

elgg_push_breadcrumb(elgg_echo('mechanics:points'), elgg_generate_url('points'));
elgg_push_breadcrumb(elgg_echo('mechanics:badges:site'), elgg_generate_url('collection:object:hjbadge:default'));

$title = elgg_echo('mechanics:badges:add');

$filter = false;

$sidebar = elgg_view('framework/mechanics/sidebar');

$content = elgg_view('framework/mechanics/badge/edit');

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
