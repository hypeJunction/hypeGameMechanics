<?php

namespace hypeJunction\GameMechanics;

$subtypes = array(
	'hjbadge',
	'hjbadgerule',
	'gm_score_history',
);

foreach ($subtypes as $subtype) {
	if (get_subtype_id('object', $subtype)) {
		update_subtype('object', $subtype);
	} else {
		add_subtype('object', $subtype, $class);
	}
}