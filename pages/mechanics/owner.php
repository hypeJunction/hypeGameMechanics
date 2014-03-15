<?php

elgg_load_js('hj.mechanics.base');
elgg_load_css('hj.mechanics.base');

elgg_load_js('hj.framework.relationshiptags');
elgg_load_css('hj.framework.jquitheme');

$user = elgg_get_page_owner_entity();

if (!$user) {
	forward(REFERER);
}

if ($user->guid == elgg_get_logged_in_user_guid()) {
	$title = elgg_echo('mechanics:badges:mine');
	$filter = elgg_view('framework/mechanics/filter', array(
		'filter_context' => 'owner'
			));
} else {
	$title = elgg_echo('machanics:badges:owner', array($user->name));
	$filter = elgg_view('framework/mechanics/filter', array(
		'filter_context' => false
			));
}

elgg_push_breadcrumb($title);

$content = elgg_view('framework/mechanics/user_badges', array(
	'user' => $user,
	'icon_size' => 'medium'
));

$layout = elgg_view_layout('content', array(
	'content' => $content,
	'filter' => $filter,
	'title' => $title
		));

echo elgg_view_page($title, $layout);
