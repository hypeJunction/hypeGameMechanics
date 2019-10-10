<?php

namespace hypeJunction\GameMechanics;

use Elgg\Includer;
use Elgg\PluginBootstrap;

class Bootstrap extends PluginBootstrap {
	public function load() {
		Includer::requireFileOnce($this->plugin->getPath() . 'lib/deprecated.php');
	}

	public function boot() {

	}

	public function init() {
		/* Rules and points */
		elgg_register_plugin_hook_handler('get_rules', 'gm_score', [Policy::class, 'setupRules']);
		elgg_register_event_handler('all', 'object', [Policy::class, 'applyEventRules'], 999);
		elgg_register_event_handler('all', 'group', [Policy::class, 'applyEventRules'], 999);
		elgg_register_event_handler('all', 'user', [Policy::class, 'applyEventRules'], 999);
		elgg_register_event_handler('all', 'annotation', [Policy::class, 'applyEventRules'], 999);
		elgg_register_event_handler('all', 'metadata', [Policy::class, 'applyEventRules'], 999);
		elgg_register_event_handler('all', 'relationship', [Policy::class, 'applyEventRules'], 999);

		elgg_register_plugin_hook_handler('permissions_check:annotate', 'all', [Permissions::class, 'canAwardPoints']);
		elgg_register_plugin_hook_handler('permissions_check:comment', 'object', [Permissions::class, 'canComment']);

		/* Menus */
		elgg_register_plugin_hook_handler('register', 'menu:entity', [Menus::class, 'setupEntityMenu']);
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', [Menus::class, 'setupOwnerBlockMenu']);
		elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'setupUserHoverMenu']);
		elgg_register_plugin_hook_handler('register', 'menu:page', [Menus::class, 'setupPageMenu']);
		elgg_register_plugin_hook_handler('register', 'menu:site', [Menus::class, 'setupSiteMenu']);

		/* Views */
		elgg_extend_view('elgg.css', 'framework/mechanics/stylesheet.css');
		elgg_extend_view('admin.css', 'framework/mechanics/stylesheet.css');


		elgg_register_widget_type('hjmechanics', elgg_echo('mechanics:widget:badges'), elgg_echo('mechanics:widget:badges:description'));

		elgg_extend_view('framework/mechanics/sidebar', 'framework/mechanics/history/filter');
		elgg_extend_view('framework/mechanics/sidebar', 'framework/mechanics/leaderboard/filter');

		elgg_register_ajax_view('resources/points/award');
	}

	public function ready() {

	}

	public function shutdown() {

	}

	public function activate() {

	}

	public function deactivate() {

	}

	public function upgrade() {
		
	}
}