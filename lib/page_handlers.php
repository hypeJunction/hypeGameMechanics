<?php

namespace hypeJunction\GameMechanics;

/**
 * Handle pages
 *
 * @param array $page	URL segments
 * @return boolean
 */
function page_handler($page) {

	elgg_push_breadcrumb(elgg_echo('mechanics:points', 'points'));

	$pages = dirname(dirname(__FILE__)) . '/pages/mechanics/';
	switch ($page[0]) {

		case 'all' :
		case 'badges' :
			include "{$pages}badges.php";
			break;

		case 'owner' :
			if (isset($page[1])) {
				$user = get_user_by_username($page[1]);
				if (elgg_instanceof($user, 'user')) {
					elgg_set_page_owner_guid($user->guid);
				}
			}
			include "{$pages}owner.php";
			break;

		case 'history' :
			if (isset($page[1])) {
				$user = get_user_by_username($page[1]);
				if (elgg_instanceof($user, 'user')) {
					elgg_set_page_owner_guid($user->guid);
				} else if (elgg_is_logged_in ()) {
					$user = elgg_get_logged_in_user_entity();
					elgg_set_page_owner_guid($user->guid);
				} else {
					return false;
				}
			}
			include "{$pages}history.php";
			break;

		case 'leaderboard' :
		default :
			include "{$pages}leaderboard.php";
			break;

		case 'gifts' :
			return false;
			break;

		case 'footprints' :
			return false;
			break;

	}
	return true;
}