<?php

namespace hypeJunction\GameMechanics;

$badge_guid = get_input('guid');
$badge = get_entity($badge_guid);

if (!elgg_instanceof($badge, 'object', HYPEGAMEMECHANICS_BADGE_SUBTYPE)) {
	register_error(elgg_echo('mechanics:badge:claim:failure'));
	forward(REFERER);
}

$user = elgg_get_logged_in_user_entity();

if (gmReward::claimBadge($badge->guid, $user->guid)) {
	system_message(elgg_echo('mechanics:badge:claim:success', array($badge->title)));
	elgg_create_river_item(array(
		'view' => 'framework/mechanics/river/claim',
		'action_type' => 'claim',
		'subject_guid' => $user->guid,
		'object_guid' => $badge->guid,
	));
} else {
	register_error(elgg_echo('mechanics:badge:claim:failure'));
}

forward(REFERER);

