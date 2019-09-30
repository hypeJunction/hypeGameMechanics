<?php

$title = elgg_echo('mechanics:leaderboard');

$filter = elgg_view('framework/mechanics/filter', [
	'filter_context' => 'leaderboard'
]);

$sidebar = elgg_view('framework/mechanics/sidebar', [
	'filter_context' => 'leaderboard'
]);

$content = elgg_view('framework/mechanics/leaderboard/list');

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
