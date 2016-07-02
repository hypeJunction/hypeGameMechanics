<?php

namespace hypeJunction\GameMechanics;

use ElggObject;

/**
 * Badge objecct class
 */
class gmBadgeRule extends ElggObject {

	const SUBTYPE = 'badge_rule';

	/**
	 * Initialize attributes
	 * Set subtype
	 * 
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

}
