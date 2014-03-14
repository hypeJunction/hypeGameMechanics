<?php

/**
 * Game Mechanics for Elgg
 *
 * @package hypeJunction
 * @subpackage GameMechanics
 *
 * @author Ismayil Khayredinov <ismayil.khayredinov@gmail.com>
 * @copyright Copyright (c) 2011-2014, Ismayil Khayredinov
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2
 */

namespace hypeJunction\GameMechanics;

const PLUGIN_ID = 'hypeGameMechanics';
const PAGEHANDLER = 'points';

define('HYPEGAMEMECHANICS_RELEASE', 1394806380);

elgg_register_class('hypeJunction\\GameMechanics\\hjBadge', __DIR__ . '/classes/hypeJunction/GameMechanics/hjBadge.php');
elgg_register_class('hypeJunction\\GameMechanics\\hjBadgeRule', __DIR__ . '/classes/hypeJunction/GameMechanics/hjBadgeRule.php');

// Load libraries
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/events.php';
require_once __DIR__ . '/lib/hooks.php';
require_once __DIR__ . '/lib/page_handlers.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');
elgg_register_event_handler('upgrade', 'system', __NAMESPACE__ . '\\upgrade');

function init() {

	/**
	 * JS and CSS
	 */
	$js = elgg_get_simplecache_url('js', 'framework/mechanics/base');
	elgg_register_js('mechanics.base', $js);

	$css = elgg_get_simplecache_url('css', 'framework/mechanics/base');
	elgg_register_css('mechanics.base', $css);

	/**
	 * Actions
	 */
	elgg_register_action('badge/claim', __DIR__ . '/actions/badge/claim.php');
	elgg_register_action('points/reset', __DIR__ . '/actions/points/reset.php', 'admin');

	/**
	 * URL and page handlers
	 */
	elgg_register_page_handler(PAGEHANDLER, __NAMESPACE__ . '\\page_handler');

	/**
	 * Rules
	 */
	elgg_register_plugin_hook_handler('mechanics:scoring:rules', 'all', __NAMESPACE__ . '\\default_scoring_rules_setup');

	/**
	 * Menus
	 */
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', __NAMESPACE__ . '\\owner_block_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\user_hover_menu_setup');


	/**
	 * Views
	 */
	//elgg_extend_view('profile/details', 'hj/mechanics/user_badges');
	elgg_extend_view('icon/user/default', 'hj/mechanics/user_score');
	elgg_register_widget_type('hjmechanics', elgg_echo('mechanics:widget:badges'), elgg_echo('mechanics:widget:badges:description'));
}

function rule_input_process($hook, $type, $return, $params) {
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
				if (!empty($recurse) && (int) $recurse > 0) {
					$badge_rule = new hjAnnotation($guid[$rule]);
					$badge_rule->container_guid = $entity->guid;
					$badge_rule->access_id = ACCESS_PUBLIC;
					$badge_rule->annotation_name = 'badge_rule';
					$badge_rule->annotation_value = $rule;
					$badge_rule->recurse = (int) $recurse;
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

