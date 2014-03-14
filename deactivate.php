<?php

// Unregister entity subtypes
$subtypes = array(
	'hjbadge',
	'hjbadgerule',
);

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}
