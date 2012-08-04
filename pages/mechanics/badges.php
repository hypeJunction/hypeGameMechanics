<?php

elgg_load_js('hj.mechanics.base');
elgg_load_css('hj.mechanics.base');

elgg_load_js('hj.framework.relationshiptags');
elgg_load_css('hj.framework.jquitheme');

$title = elgg_echo('hj:mechanics:badges:site');
elgg_push_breadcrumb($title);

$filter = elgg_view('hj/mechanics/filter', array(
	'filter_context' => 'all'
));

$content = elgg_view('hj/mechanics/badges');

$layout = elgg_view_layout('content', array(
    'content' => $content,
	'filter' => $filter,
	'title' => $title
));

echo elgg_view_page($title, $layout);
