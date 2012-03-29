<?php

$subtypes = array(
	'hjbadge' => 'hjBadge',
	'hjgift' => 'hjGift'
);

foreach ($subtypes as $subtype => $class) {
	if (get_subtype_id('object', $subtype)) {
		update_subtype('object', $subtype, $class);
	} else {
		add_subtype('object', $subtype, $class);
	}
}
