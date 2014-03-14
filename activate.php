<?php

$subtypes = array(
	'hjbadge' => 'hypeJunction\\GameMechanics\\hjBadge',
	'hjbadgerule' => 'hypeJunction\\GameMechanics\\hjBadgeRule',
);

foreach ($subtypes as $subtype => $class) {
	if (get_subtype_id('object', $subtype)) {
		update_subtype('object', $subtype, $class);
	} else {
		add_subtype('object', $subtype, $class);
	}
}