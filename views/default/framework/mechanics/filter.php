<?php

namespace hypeJunction\GameMechanics;

$filter_context = elgg_extract('filter_context', $vars, 'leaderboard');

$tabs = array(
	'leaderboard' => array(
		'text' => elgg_echo('mechanics:leaderboard'),
		'href' => "points/leaderboard",
		'selected' => ($filter_context == 'leaderboard'),
		'priority' => 100,
	),
	'badges' => array(
		'text' => elgg_echo('mechanics:badges:site'),
		'href' => "points/badges",
		'selected' => ($filter_context == 'badges'),
		'priority' => 200,
	),
);


if (elgg_is_logged_in()) {
	$user = elgg_get_logged_in_user_entity();
	$tabs['owner'] = array(
		'text' => elgg_echo('mechanics:badges:mine'),
		'href' => "points/owner/$user->username",
		'selected' => ($filter_context == 'owner'),
		'priority' => 300
	);
	$tabs['history'] = array(
		'text' => elgg_echo('mechanics:history'),
		'href' => "points/history/$user->username",
		'selected' => ($filter_context == 'history'),
		'priority' => 400,
	);
}

foreach ($tabs as $name => $tab) {
	$tab['name'] = $name;
	elgg_register_menu_item('filter', $tab);
}

echo elgg_view_menu('filter', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz'
));



