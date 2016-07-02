<?php

namespace hypeJunction\GameMechanics;

use ElggObject;

/**
 * Badge objecct class
 */
class gmBadge extends ElggObject {

	const SUBTYPE = 'hjbadge';

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
