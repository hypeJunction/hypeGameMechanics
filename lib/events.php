<?php

namespace hypeJunction\GameMechanics;

/**
 * Add some menu items during page setup
 */
function pagesetup() {

	elgg_register_menu_item('site', array(
		'name' => 'leaderboard',
		'text' => elgg_echo('mechanics:leaderboard'),
		'href' => 'points/leaderboard'
	));

	if (elgg_is_admin_logged_in()) {
		elgg_register_menu_item('page', array(
			'name' => 'gamemechanics',
			'text' => elgg_echo('mechanics:setup'),
			'href' => PAGEHANDLER . '/badges',
			'priority' => 500,
			'contexts' => array('admin'),
			'section' => 'configure'
		));
	}
}

/**
 * Run upgrade scripts
 */
function upgrade() {

	if (!elgg_is_admin_logged_in()) {
		return true;
	}

	$release = HYPEGAMEMECHANICS_RELEASE;
	$old_release = elgg_get_plugin_setting('release', PLUGIN_ID);

	if ($release > $old_release) {

		include_once dirname(dirname(__FILE__)) . '/lib/upgrade.php';
		elgg_set_plugin_setting('release', $release, PLUGIN_ID);
	}

	return true;
}
