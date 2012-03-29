<?php

$badge_guid = get_input('e');
$badge = get_entity($badge_guid);

$user = elgg_get_logged_in_user_entity();

if (hj_mechanics_check_user_eligibility_for_badge($badge, $user)) {
	if (add_entity_relationship($user->guid, 'claimed', $badge->guid)) {
		if ($cost = $badge->points_cost) {
			$id = create_annotation($user->guid, "gm_score", -$cost, '', $user->guid, ACCESS_PUBLIC);
			if ($id) {
				$history = new hjAnnotation();
				$history->owner_guid = $user->guid;
				$history->container_guid = $user->guid;
				$history->access_id = ACCESS_PRIVATE;
				$history->annotation_name = 'gm_score_history';
				$history->annotation_value = -$cost;
				$history->rule = 'badge:purchase';
				$history->annotation_id = $id;
				$history->save();
			}
		}
		if ($badge->badge_type == 'status') {
			$user->gm_status = $badge->guid;
		}
		system_message(elgg_echo('hj:mechanics:badge:claim:success'));
		add_to_river("river/object/hjformsubmission/create", "claim", $user->guid, $badge->guid);
		forward(REFERER);
	} else {
		register_error(elgg_echo('hj:mechanics:badge:claim:failure'));
	}
} else {
	register_error(elgg_echo('hj:mechanics:badge:ineligible'));
	forward(REFERER);
}
