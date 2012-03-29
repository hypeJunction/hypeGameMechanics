<?php

$subtypes = array(
	'hjbadge' => 'hjBadge',
	'hjgift' => 'hjGift'
);

foreach ($subtypes as $subtype => $class) {
	update_subtype('object', $subtype);
}
