<?php

elgg_push_breadcrumb(elgg_echo('mechanics:points'), elgg_generate_url('points'));

$username = elgg_extract('username', $vars);
$user = get_user_by_username($username);

if (!$user instanceof ElggUser) {
	throw new \Elgg\EntityNotFoundException();
}

if (!$user->canEdit()) {
	throw new \Elgg\EntityPermissionsException();
}

if ($user->guid == elgg_get_logged_in_user_guid()) {
	$title = elgg_echo('mechanics:badges:mine');

	$filter = elgg_view('framework/mechanics/filter', [
		'filter_context' => 'owner'
	]);
} else {
	$title = elgg_echo('machanics:badges:owner', [$user->name]);

	$filter = elgg_view('framework/mechanics/filter', [
		'filter_context' => false
	]);
}

$sidebar = elgg_view('framework/mechanics/sidebar', [
	'filter_context' => 'owner',
]);

$content = elgg_view('framework/mechanics/user_badges', [
	'user' => $user,
	'icon_size' => 'medium'
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
