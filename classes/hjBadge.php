<?php

class hjBadge extends ElggObject {

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = 'hjbadge';
	}

	public function __construct($guid = null) {
		parent::__construct($guid);
	}

}

?>
