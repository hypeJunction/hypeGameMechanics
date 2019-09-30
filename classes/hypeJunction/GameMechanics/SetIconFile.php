<?php

namespace hypeJunction\GameMechanics;

use Elgg\Hook;

class SetIconFile {

	/**
	 * @todo This was a big mistake added somewhere in 2.x
	 *       Can't remove now, because it will break things
	 *       Need an upgrade script
	 *
	 * @param Hook $hook
	 *
	 * @return \ElggIcon
	 */
	public function __invoke(Hook $hook) {
		$entity = $hook->getEntityParam();
		$size = $hook->getParam('size', 'medium');

		$icon = $hook->getValue();
		/* @var \ElggIcon $icon */

		$icon->owner_guid = $entity->owner_guid;
		$icon->setFilename("icons/{$entity->guid}{$size}.jpg");

		return $icon;
	}
}
