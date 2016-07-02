<?php

namespace hypeJunction\GameMechanics;

use ElggFile;
use ElggObject;

/**
 * Handle pages
 *
 * @param array $page		URL segments
 * @param string $handler	Pagehandler name
 * @return boolean
 */
function page_handler($page, $handler) {

	elgg_load_js('lightbox');
	elgg_load_css('lightbox');

	elgg_push_breadcrumb(elgg_echo('mechanics:points'), 'points');

	switch ($page[0]) {

		case 'leaderboard' :
		default :

			$title = elgg_echo('mechanics:leaderboard');

			$filter = elgg_view('framework/mechanics/filter', array(
				'filter_context' => 'leaderboard'
			));

			$sidebar = elgg_view('framework/mechanics/sidebar', array(
				'filter_context' => 'leaderboard'
			));

			$content = elgg_view('framework/mechanics/leaderboard/list');
			break;

		case 'all' :
		case 'badges' :

			if (elgg_is_admin_logged_in()) {
				elgg_register_menu_item('title', array(
					'name' => 'add_badge',
					'text' => elgg_echo('mechanics:badges:add'),
					'href' => "$handler/badge/edit",
					'class' => 'elgg-button elgg-button-action',
				));
			}
			$title = elgg_echo('mechanics:badges:site');

			$filter = elgg_view('framework/mechanics/filter', array(
				'filter_context' => 'badges'
			));
			$sidebar = elgg_view('framework/mechanics/sidebar', array(
				'filter_context' => 'badges'
			));
			$content = elgg_view('framework/mechanics/badges');
			break;

		case 'badge' :

			elgg_push_breadcrumb(elgg_echo('mechanics:badges:site'), "$handler/badges");

			switch ($page[1]) {

				case 'edit' :

					admin_gatekeeper();

					$entity = get_entity($page[2]);

					$title = ($entity) ? elgg_echo('mechanics:badges:edit', array($entity->title)) : elgg_echo('mechanics:badges:add');

					$filter = false;
					$sidebar = elgg_view('framework/mechanics/sidebar', array(
						'entity' => $entity
					));
					$content = elgg_view('framework/mechanics/badge/edit', array(
						'entity' => $entity
					));
					break;

				case 'view' :
					$entity = get_entity($page[2]);

					if (!elgg_instanceof($entity, 'object', HYPEGAMEMECHANICS_BADGE_SUBTYPE)) {
						return false;
					}

					$title = $entity->title;

					$filter = false;
					$sidebar = elgg_view('framework/mechanics/sidebar', array(
						'entity' => $entity
					));
					$content = elgg_view('framework/mechanics/badge/view', array(
						'entity' => $entity
					));
					break;
			}
			break;

		case 'award' :

			$entity = get_entity($page[1]);

			if (!elgg_instanceof($entity, 'user') || !$entity->canAnnotate(0, 'gm_score_award')) {
				return false;
			}

			elgg_set_page_owner_guid($entity->guid);
			
			$title = elgg_echo('mechanics:admin:award_to', array($entity->name));

			$filter = false;
			$sidebar = elgg_view('framework/mechanics/sidebar', array(
				'entity' => $entity
			));
			$content = elgg_view('framework/mechanics/points/award', array(
				'entity' => $entity
			));
			break;

		case 'owner' :

			gatekeeper();

			$user = get_user_by_username($page[1]);
			if (elgg_instanceof($user, 'user')) {
				elgg_set_page_owner_guid($user->guid);
			} else {
				$user = elgg_get_logged_in_user_entity();
				forward("$handler/owner/$user->username");
			}

			if (!$user || !$user->canEdit()) {
				return false;
			}

			if ($user->guid == elgg_get_logged_in_user_guid()) {
				$title = elgg_echo('mechanics:badges:mine');
				$filter = elgg_view('framework/mechanics/filter', array(
					'filter_context' => 'owner'
				));
			} else {
				$title = elgg_echo('machanics:badges:owner', array($user->name));
				$filter = elgg_view('framework/mechanics/filter', array(
					'filter_context' => false
				));
			}

			$sidebar = elgg_view('framework/mechanics/sidebar', array(
				'filter_context' => 'owner',
			));

			$content = elgg_view('framework/mechanics/user_badges', array(
				'user' => $user,
				'icon_size' => 'medium'
			));
			break;

		case 'history' :

			gatekeeper();

			$user = get_user_by_username($page[1]);
			if (elgg_instanceof($user, 'user')) {
				elgg_set_page_owner_guid($user->guid);
			} else {
				$user = elgg_get_logged_in_user_entity();
				forward("$handler/owner/$user->username");
			}

			if (!$user || !$user->canEdit()) {
				return false;
			}

			if ($user->guid == elgg_get_logged_in_user_guid()) {
				$title = elgg_echo('mechanics:points:history');
			} else {
				$title = elgg_echo('mechanics:points:history:owner', array($user->name));
			}

			$filter = elgg_view('framework/mechanics/filter', array(
				'filter_context' => 'history'
			));

			$sidebar = elgg_view('framework/mechanics/sidebar', array(
				'filter_context' => 'history',
			));

			$content .= elgg_view('framework/mechanics/history/list', array(
				'user' => $user
			));
			break;

		case 'icon' :
			$entity = get_entity($page[1]);

			if (!elgg_instanceof($entity, 'object', HYPEGAMEMECHANICS_BADGE_SUBTYPE)) {
				return false;
			}

			$size = $page[2];

			if (!array_key_exists($size, elgg_get_config('icon_sizes'))) {
				$size = 'medium';
			}

			$filename = "icons/" . $entity->guid . $size . ".jpg";

			$filehandler = new ElggFile();
			$filehandler->owner_guid = $entity->owner_guid;
			$filehandler->setFilename($filename);

			if ($filehandler->open("read")) {
				$contents = $filehandler->read($filehandler->size());
			}

			header("Content-type: image/jpeg");
			header('Expires: ' . date('r', time() + 864000));
			header("Pragma: public");
			header("Cache-Control: public");
			header("Content-Length: " . strlen($contents));
			echo $contents;
			exit;
			break;
	}

	if (empty($content)) {
		return false;
	}

	if (elgg_is_xhr()) {
		echo $content;
	} else {
		elgg_push_breadcrumb($title);

		$layout = elgg_view_layout('content', array(
			'title' => $title,
			'content' => $content,
			'filter' => $filter,
			'sidebar' => $sidebar,
		));
		echo elgg_view_page($title, $layout);
	}

	return true;
}
