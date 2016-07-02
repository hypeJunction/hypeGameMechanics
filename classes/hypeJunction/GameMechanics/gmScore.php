<?php

namespace hypeJunction\GameMechanics;

use ElggObject;

/**
 * Badge objecct class
 */
class gmScore extends ElggObject {

	const SUBTYPE = 'gm_score_history';

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
