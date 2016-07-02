<?php

use hypeJunction\GameMechanics\gmBadge;
use hypeJunction\GameMechanics\gmBadgeRule;
use hypeJunction\GameMechanics\gmScore;

require_once __DIR__ . '/autoloader.php';

// Register subtype classes
$subtypes = array(
	gmBadge::SUBTYPE => gmBadge::class,
	gmBadgeRule::SUBTYPE => gmBadgeRule::class,
	gmScore::SUBTYPE => gmScore::class,
);

foreach ($subtypes as $subtype => $class) {
	if (!update_subtype('object', $subtype, $class)) {
		add_subtype('object', $subtype, $class);
	}
}