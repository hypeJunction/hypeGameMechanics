<?php

namespace hypeJunction\GameMechanics;

elgg_make_sticky_form('points/award');

$guid = get_input('guid');
$entity = get_entity($guid);

if (!elgg_instanceof($entity, 'user') || !$entity->canAnnotate(0, 'gm_score_award')) {
	register_error(elgg_echo('mechanics:admin:award:error_permissions'));
	forward(REFERER);
}

$amount = (int) get_input('amount', 0);
$note = get_input('note', '');

if (!$amount) {
	register_error(elgg_echo('mechanics:admin:award:error_amount'));
	forward(REFERER);
}

if (gmReward::awardPoints($amount, $note, $entity->guid)) {
	if ($amount > 0) {
		system_message(elgg_echo('mechanics:admin:award:success'));
	} else {
		system_message(elgg_echo('mechanics:admin:penalty:success'));
	}

	$admin = elgg_get_logged_in_user_entity();
	$admin_link = elgg_view('output/url', array(
		'text' => $admin->name,
		'href' => $admin->getURL()
	));

	if (!$note) {
		$note = elgg_echo('mechanics:no_note');
	}

	$balance = elgg_view('output/url', array(
		'href' => PAGEHANDLER . '/history/' . $user->username
	));

	if ($amount > 0) {
		$subject = elgg_echo('mechanics:create:award');
		$message = elgg_echo('mechanics:create:award:message', array(
			$admin_link, $amount, $note, $balance
		));
	} else {
		$subject = elgg_echo('mechanics:create:penalty');
		$message = elgg_echo('mechanics:create:penalty:message', array(
			$admin_link, $amount, $note, $balance
		));
	}
	notify_user($entity->guid, $admin->guid, $subject, $message);
	elgg_clear_sticky_form('points/award');
} else {
	register_error(elgg_echo('mechanics:admin:award:error'));
}

forward(REFERER);
