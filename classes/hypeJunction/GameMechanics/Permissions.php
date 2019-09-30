<?php

namespace hypeJunction\GameMechanics;

use Elgg\Hook;
use ElggUser;

class Permissions {

	/**
	 * Check if current user can award points to the user
	 * Currently, only admins can award points
	 *
	 * @param Hook $hook
	 *
	 * @return bool
	 */
	public static function canAwardPoints(Hook $hook) {
		$return = $hook->getValue();

		$entity = $hook->getEntityParam();
		$user = $hook->getUserParam();
		$annotation_name = $hook->getParam('annotation_name');

		if ($annotation_name !== 'gm_score_award') {
			return $return;
		}

		if (!$entity instanceof ElggUser) {
			// Only users can be awarded points
			return false;
		}

		if ($entity->isAdmin()) {
			// Do not allow awards on admins
			return false;
		}

		return $user instanceof ElggUser && $user->isAdmin();
	}

	/**
	 * Do not allow comments on badges
	 *
	 * @param Hook $hook
	 *
	 * @return bool
	 */
	public static function canComment(Hook $hook) {
		$entity = $hook->getEntityParam();

		if ($entity instanceof Badge || $entity instanceof BadgeRule || $entity instanceof Score) {
			return false;
		}
	}

}
