<?php

/* hypeGameMechanics
 *
 * Provides a game mechanics functionality for Elgg
 *
 * @package hypeJunction
 * @subpackage hypeGameMechanics
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyrigh (c) 2011, Ismayil Khayredinov
 */

elgg_register_event_handler('init', 'system', 'hj_mechanics_init', 510);

function hj_mechanics_init() {

	$plugin = 'hypeGameMechanics';

	if (!elgg_is_active_plugin('hypeFramework')) {
		register_error(elgg_echo('hj:framework:disabled', array($plugin, $plugin)));
		disable_plugin($plugin);
	}

	$shortcuts = hj_framework_path_shortcuts($plugin);

	// Helper Classes
	elgg_register_classes($shortcuts['classes']);

	elgg_register_library('hj:mechanics:setup', $shortcuts['lib'] . 'mechanics/setup.php');

	elgg_register_admin_menu_item('administer', 'mechanics', 'hj', 400);

	//Check if the initial setup has been performed, if not porform it
	if (!elgg_get_plugin_setting('hj:mechanics:setup')) {
		elgg_load_library('hj:mechanics:setup');
		if (hj_mechanics_setup())
			system_message('hypeGameMechanics was successfully configured');
	}

	// Register Libraries
	elgg_register_library('hj:mechanics:base', $shortcuts['lib'] . 'mechanics/base.php');
	elgg_load_library('hj:mechanics:base');

	elgg_register_library('hj:mechanics:handlers', $shortcuts['lib'] . 'mechanics/handlers.php');
	elgg_load_library('hj:mechanics:handlers');

	elgg_register_library('hj:mechanics:rules', $shortcuts['lib'] . 'mechanics/rules.php');
	elgg_load_library('hj:mechanics:rules');

	elgg_register_action('badge/claim', $shortcuts['actions'] . 'hj/badge/claim.php');
	
	elgg_register_plugin_hook_handler('hj:mechanics:scoring:rules', 'all', 'hj_mechanics_setup_default_scoring_rules');
	elgg_register_plugin_hook_handler('hj:framework:field:process', 'all', 'hj_mechanics_rule_input_process');

	elgg_extend_view('profile/details', 'hj/mechanics/user_badges');
	elgg_extend_view('icon/user/default', 'hj/mechanics/user_score');

	$js = elgg_get_simplecache_url('js', 'hj/mechanics/base');
	elgg_register_js('hj.mechanics.base', $js);
	
	$css = elgg_get_simplecache_url('css', 'hj/mechanics/base');
	elgg_register_css('hj.mechanics.base', $css);

	elgg_register_page_handler('points', 'hj_mechanics_page_handler');

	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'hj_mechanics_owner_block_menu');
}

function hj_mechanics_rule_input_process($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params, false);
	$field = elgg_extract('field', $params, false);
	if (!$entity || !$field) {
		return true;
	}

	switch ($field->input_type) {
		case 'mechanics_rule' :
			$field_name = $field->name;

			$rules_input = get_input('rules');
			$recurse_input = get_input('recurse');
			
			$rules = elgg_get_entities_from_metadata(array(
				'type' => 'object',
				'subtype' => 'hjannotation',
				'container_guid' => $entity->guid,
				'limit' => 0,
				'metadata_name_value_pairs' => array(
					array('name' => 'annotation_name', 'value' => 'badge_rule'),
				)
					));

			$guid = array();
			if (is_array($rules)) {
				foreach ($rules as $rule) {
					if (in_array($rule->annotation_value, $rules_input)) {
						$guid[$rule->annotation_value] = $rule->guid;
					}
				}
			}

			foreach ($rules_input as $key => $rule) {
				$recurse = $recurse_input[$key];
				if (!empty($recurse) && (int)$recurse > 0) {
					$badge_rule = new hjAnnotation($guid[$rule]);
					$badge_rule->container_guid = $entity->guid;
					$badge_rule->access_id = ACCESS_PUBLIC;
					$badge_rule->annotation_name = 'badge_rule';
					$badge_rule->annotation_value = $rule;
					$badge_rule->recurse = (int)$recurse;
					$saved = $badge_rule->save();
				} elseif ($guid[$rule]) {
					$badge_rule = get_entity($guid[$rule]);
					if (elgg_instanceof($badge_rule)) {
						$result = $badge_rule->delete();
					}
				}
			}

			break;
	}

	return true;
}

function hj_mechanics_page_handler($page) {
	$plugin = 'hypeGameMechanics';
	$shortcuts = hj_framework_path_shortcuts($plugin);
	$pages = $shortcuts['pages'] . 'mechanics/';


	switch ($page[0]) {
		case 'badges' :
			if ($page[1] == 'user') {
				set_input('username', $page[2]);
				include "{$pages}badges_owner.php";
			} else {
				set_input('e', $page[2]);
				include "{$pages}badges.php";
			}
			break;

		case 'history' :
			$user = elgg_get_logged_in_user_entity();
			set_input('username', $user->username);
			include "{$pages}history.php";
			break;

		case 'gifts' :
			return false;
			break;

		case 'footprints' :
			return false;
			break;

		default :
			return false;
			break;
	}
	return true;
}

function hj_mechanics_owner_block_menu($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity, 'user') || $entity->guid !== elgg_get_logged_in_user_guid()) {
		return $return;
	}

	$points = array(
		'name' => 'points',
		'text' => elgg_echo('hj:mechanics:points'),
		'href' => "points/history"
	);
	$return[] = ElggMenuItem::factory($points);

	$badges = array(
		'name' => 'badges',
		'text' => elgg_echo('hj:mechanics:badges'),
		'href' => "points/badges"
	);
	$return[] = ElggMenuItem::factory($badges);

	return $return;

}