<?php

elgg_push_breadcrumb(elgg_echo('mechanics:points'), elgg_generate_url('points'));

if (elgg_is_admin_logged_in()) {
	elgg_register_menu_item('title', [
		'name' => 'add_badge',
		'text' => elgg_echo('mechanics:badges:add'),
		'href' => "$handler/badge/edit",
		'class' => 'elgg-button elgg-button-action',
	]);
}
$title = elgg_echo('mechanics:badges:site');

$filter = elgg_view('framework/mechanics/filter', [
	'filter_context' => 'badges'
]);

$sidebar = elgg_view('framework/mechanics/sidebar', [
	'filter_context' => 'badges'
]);

$content = elgg_view('framework/mechanics/badges');

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
