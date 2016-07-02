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

require_once __DIR__ . '/autoloader.php';

const PLUGIN_ID = 'hypeGameMechanics';
const PAGEHANDLER = 'points';

define('HYPEGAMEMECHANICS_RELEASE', 1395099219);

define('HYPEGAMEMECHANICS_BADGE_SUBTYPE', gmBadge::SUBTYPE);
define('HYPEGAMEMECHANICS_BADGERULE_SUBTYPE', gmBadgeRule::SUBTYPE);
define('HYPEGAMEMECHANICS_SCORE_SUBTYPE', gmScore::SUBTYPE);

define('HYPEGAMEMECHANICS_DEPENDENCY_REL', 'badge_required');
define('HYPEGAMEMECHANICS_CLAIMED_REL', 'claimed');

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');
elgg_register_event_handler('upgrade', 'system', __NAMESPACE__ . '\\upgrade');

/**
 * Initialize
 * @return void
 */
function init() {

	/**
	 * Events
	 */
	$handler = __NAMESPACE__ . '\\apply_event_rules';
	elgg_register_event_handler('all', 'object', $handler, 999);
	elgg_register_event_handler('all', 'group', $handler, 999);
	elgg_register_event_handler('all', 'user', $handler, 999);
	elgg_register_event_handler('all', 'annotation', $handler, 999);
	elgg_register_event_handler('all', 'metadata', $handler, 999);
	elgg_register_event_handler('all', 'relationship', $handler, 999);

	/**
	 * JS and CSS
	 */
	elgg_extend_view('js/elgg', 'js/framework/mechanics/mechanics');
	elgg_extend_view('js/admin', 'js/framework/mechanics/mechanics');

	elgg_extend_view('css/elgg', 'css/framework/mechanics/mechanics');
	elgg_extend_view('css/admin', 'css/framework/mechanics/mechanics');

	/**
	 * Actions
	 */
	elgg_register_action('badge/claim', __DIR__ . '/actions/badge/claim.php');
	elgg_register_action('badge/edit', __DIR__ . '/actions/badge/edit.php', 'admin');
	elgg_register_action('badge/delete', __DIR__ . '/actions/badge/delete.php', 'admin');
	elgg_register_action('badge/order', __DIR__ . '/actions/badge/order.php', 'admin');

	elgg_register_action('points/award', __DIR__ . '/actions/points/award.php');
	elgg_register_action('points/reset', __DIR__ . '/actions/points/reset.php', 'admin');

	/**
	 * URL and page handlers
	 */
	elgg_register_page_handler(PAGEHANDLER, __NAMESPACE__ . '\\page_handler');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', __NAMESPACE__ . '\\badge_icon_url_handler');
	elgg_register_plugin_hook_handler('entity:url', 'object', __NAMESPACE__ . '\\badge_url_handler');
	
	/**
	 * Rules
	 */
	elgg_register_plugin_hook_handler('get_rules', 'gm_score', __NAMESPACE__ . '\\setup_scoring_rules');

	/**
	 * Menus
	 */
	elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\\entity_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', __NAMESPACE__ . '\\owner_block_menu_setup');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\user_hover_menu_setup');

	/**
	 * Permissions
	 */
	elgg_register_plugin_hook_handler('permissions_check:annotate', 'user', __NAMESPACE__ . '\\permissions_check_gm_score_award');

	/**
	 * Views
	 */
	elgg_register_widget_type('hjmechanics', elgg_echo('mechanics:widget:badges'), elgg_echo('mechanics:widget:badges:description'));

	elgg_extend_view('framework/mechanics/sidebar', 'framework/mechanics/history/filter');
	elgg_extend_view('framework/mechanics/sidebar', 'framework/mechanics/leaderboard/filter');

	// Load fonts
	elgg_extend_view('page/elements/head', 'framework/fonts/font-awesome');
	elgg_extend_view('page/elements/head', 'framework/fonts/open-sans');

	elgg_register_menu_item('site', array(
		'name' => 'leaderboard',
		'text' => elgg_echo('mechanics:leaderboard'),
		'href' => 'points/leaderboard'
	));

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
