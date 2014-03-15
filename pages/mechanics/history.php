<?php

$user = elgg_get_page_owner_entity();

if (!$user) {
	forward(REFERER);
}

if ($user->guid == elgg_get_logged_in_user_guid()) {
	$title = elgg_echo('mechanics:points:history');
} else {
	$title = elgg_echo('mechanics:points:history:owner', array($user->name));
}
elgg_push_breadcrumb($title);

$filter = elgg_view('framework/mechanics/filter', array(
	'filter_context' => 'history'
));

$content = elgg_view('framework/mechanics/history/filter');
$content .= elgg_view('framework/mechanics/history/list', array(
	'user' => $user
));


$layout = elgg_view_layout('content', array(
    'content' => $content,
	'filter' => $filter,
	'title' => $title
));

echo elgg_view_page($title, $layout);

echo elgg_view_page(null, $html);


