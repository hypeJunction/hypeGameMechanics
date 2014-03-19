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
			'parent_name' => 'appearance',
			'text' => elgg_echo('mechanics:badges:site'),
			'href' => PAGEHANDLER . '/badges',
			'priority' => 500,
			'contexts' => array('admin'),
			'section' => 'configure'
		));
	}
}

/**
 * Check if the event qualifies for points and award them to the user
 *
 * @param string $event
 * @param string $type
 * @param mixed $object
 * @return boolean
 */
function apply_event_rules($event, $type, $object) {

	// Subject
	$user = elgg_get_logged_in_user_entity();
	if (!$user) {
		return true;
	}

	if ($user->isAdmin()) {
		return true;
	}
	
	// Object
	if (is_object($object)) {
		$entity = $object;
	} else if (is_array($object)) {
		$entity = elgg_extract('entity', $object, null);
		if (!$entity) {
			$entity = elgg_extract('user', $object, null);
		}
		if (!$entity) {
			$entity = elgg_extract('group', $object, null);
		}
	}

	if (!is_object($entity)) {
		// Terminate early, nothing to act upon
		return true;
	}

	// Get rules associated with events
	$rules = get_scoring_rules('events');

	// Apply rules
	foreach ($rules as $rule_name => $rule_options) {

		$rule_options['name'] = $rule_name;
		$gmRule = gmRule::applyRule($entity, $rule_options, "$event::$type");

		$errors = $gmRule->getErrors();
		if ($errors) {
			foreach ($errors as $error) {
				register_error($error);
			}
		}

		$messages = $gmRule->getMessages();
		if ($messages) {
			foreach ($messages as $message) {
				system_message($message);
			}
		}

		//error_log(print_r($gmRule->getLog(), true));

		if ($gmRule->terminateEvent()) {
			return false;
		}
	}

	return true;
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
