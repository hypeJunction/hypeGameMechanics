<?php

// Unregister entity subtypes
use hypeJunction\GameMechanics\gmBadge;
use hypeJunction\GameMechanics\gmBadgeRule;
use hypeJunction\GameMechanics\gmScore;

// Register subtype classes
$subtypes = array(
	gmBadge::SUBTYPE => gmBadge::class,
	gmBadgeRule::SUBTYPE => gmBadgeRule::class,
	gmScore::SUBTYPE => gmScore::class,
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}
