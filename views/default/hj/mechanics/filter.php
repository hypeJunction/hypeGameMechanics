<?php

$user = elgg_get_logged_in_user_entity();
$filter_context = elgg_extract('filter_context', $vars, 'badges');

// generate a list of default tabs
$tabs = array(
	'badges' => array(
		'text' => elgg_echo('hj:mechanics:badges:site'),
		'href' => "points/badges",
		'selected' => ($filter_context == 'badges'),
		'priority' => 100,
	),
	'leaderboard' => array(
		'text' => elgg_echo('hj:mechanics:leaderboard'),
		'href' => "points/leaderboard",
		'selected' => ($filter_context == 'leaderboard'),
		'priority' => 400,
	)
);


if (elgg_is_logged_in()) {
	$tabs['owner'] = array(
		'text' => elgg_echo('hj:mechanics:badges:mine'),
		'href' => "points/owner/$user->username",
		'selected' => ($filter_context == 'owner'),
		'priority' => 200
	);
	
	$tabs['history'] = array(
		'text' => elgg_echo('hj:mechanics:history'),
		'href' => "points/history/$user->username",
		'selected' => ($filter_context == 'history'),
		'priority' => 300,
	);
}

foreach ($tabs as $name => $tab) {
	$tab['name'] = $name;

	elgg_register_menu_item('filter', $tab);
}

echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));



