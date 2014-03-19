<?php

// Unregister entity subtypes
$subtypes = array(
	HYPEGAMEMECHANICS_BADGE_SUBTYPE,
	HYPEGAMEMECHANICS_BADGERULE_SUBTYPE,
);

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}
