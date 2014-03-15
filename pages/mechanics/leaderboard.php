<?php

elgg_load_js('hj.mechanics.base');
elgg_load_css('hj.mechanics.base');

elgg_load_js('hj.framework.relationshiptags');
elgg_load_css('hj.framework.jquitheme');

$title = elgg_echo('mechanics:leaderboard');
elgg_push_breadcrumb($title);

$filter = elgg_view('framework/mechanics/filter', array(
	'filter_context' => 'leaderboard'
));

$content = elgg_view('framework/mechanics/leaderboard/filter');
$content .= elgg_view('framework/mechanics/leaderboard/list');

$layout = elgg_view_layout('content', array(
    'content' => $content,
	'filter' => $filter,
	'title' => $title
));

echo elgg_view_page($title, $layout);
