<?php

elgg_register_event_handler('all', 'object', 'hj_mechanics_score_event_handler');
elgg_register_event_handler('all', 'group', 'hj_mechanics_score_event_handler');
elgg_register_event_handler('all', 'user', 'hj_mechanics_score_event_handler');
elgg_register_event_handler('all', 'annotation', 'hj_mechanics_score_event_handler');
elgg_register_event_handler('all', 'metadata', 'hj_mechanics_score_event_handler');
elgg_register_event_handler('all', 'relationship', 'hj_mechanics_score_event_handler');
elgg_register_event_handler('all', 'friend', 'hj_mechanics_score_event_handler');
elgg_register_event_handler('all', 'member', 'hj_mechanics_score_event_handler');

// Handler handling function
function hj_mechanics_score_event_handler($event, $type, $entity) {

//	if (elgg_is_admin_logged_in()) {
//		return true;
//	}
	// Let's determine if a rule for this handler has been defined
	if (is_array($entity)) {
		$ent = elgg_extract('entity', $entity, false);
		if (!$ent) {
			$ent = elgg_extract('user', $entity, false);
		}
		if (!$ent) {
			$ent = elgg_extract('group', $entity, false);
		}
		$entity = $ent;
	}

	if ($entity->relationship) {
		$entity = get_relationship($entity->id);
	}

	if (!$entity) {
		return true;
	}

	$return = true;

	$entity_type = $entity->getType();
	if (!$entity_subtype = $entity->getSubtype()) {
		$entity_subtype = 'default';
	}

	$rules = hj_mechanics_get_scoring_rules();
	$rules = $rules["event"]["$event:$type:$entity_type:$entity_subtype"];

	if ($rules) {
		foreach ($rules as $rule) {
			$score = elgg_get_plugin_setting($rule['unique_name'], 'hypeGameMechanics');

			if (!$score || (int) $score == 0 || empty($score)) {
				continue;
			}

			$meets_conditions = true;

			// Conditions defined by the rule definition
			if ($conditions = $rule['conditions']) {
				if (!is_array($conditions)) {
					$conditions = array($conditions);
				}
				foreach ($conditions as $condition) {
					$condition_meta = $condition['metadata_name'];
					$condition_value = $condition['metadata_value'];
					if ($entity->$condition_meta != $condition_value) {
						$meets_conditions = false;
					}
				}
			}

			// Let's see if we have user guid stored as metadata for reverse scoring, e.g. in a relationship
			$user_meta = $rule['user'];
			if ($user_meta) {
				$ent = get_entity($entity->$user_meta);
				if (elgg_instanceof($ent, 'user')) {
					$user = $ent;
				} else {
					$ent_owner = get_entity($ent->owner_guid);
					if (elgg_instanceof($ent_owner, 'user')) {
						$user = $ent_owner;
					}
				}
			}

			if (!$user) {
				$user = elgg_get_logged_in_user_entity();
			}

			$yesterday = time() - 24 * 60 * 60;

			$access_status = access_get_show_hidden_status();
			access_show_hidden_entities(true);

			// Conditions imposed by max values
			if (!$daily_max = (int) elgg_get_plugin_setting('daily_max', 'hypeGameMechanics')) {
				$daily_max = 10000;
			}
			$daily_total = hj_mechanics_get_user_score($user, $yesterday, time());
			$alltime_total = hj_mechanics_get_user_score($user);

			$allow_negative = elgg_get_plugin_setting('allow_negative_total', 'hypeGameMechanics');

			if (!$daily_action_max = (int) elgg_get_plugin_setting('daily_action_max', 'hypeGameMechanics')) {
				$daily_action_max = 10000;
			}

			if (!$alltime_action_max = (int) elgg_get_plugin_setting('alltime_action_max', 'hypeGameMechanics')) {
				$alltime_action_max = 1000000;
			}
			if (!$daily_recur_max = (int) elgg_get_plugin_setting('daily_recur_max', 'hypeGameMechanics')) {
				$daily_recur_max = 100;
			}

			if (!$alltime_recur_max = (int) elgg_get_plugin_setting('alltime_recur_max', 'hypeGameMechanics')) {
				$alltime_recur_max = 10000;
			}

			$daily_action_total = hj_mechanics_get_user_action_total($user, $rule['unique_name'], $yesterday, time());
			$alltime_action_total = hj_mechanics_get_user_action_total($user, $rule['unique_name']);
			$daily_recur_total = hj_mechanics_get_user_recur_total($user, $rule['unique_name'], $yesterday, time());
			$alltime_recur_total = hj_mechanics_get_user_recur_total($user, $rule['unique_name']);

			if (!$meets_conditions) {
				continue;
			}

			if ($alltime_total + $score < 0) {
				if ($allow_negative != 'allow') {
					register_error(elgg_echo('hj:mechanics:negativereached'));
					$return = false;
					continue;
				}
			}
			if ($daily_total + $score <= $daily_max // Did user reach a daily max?
					&& $daily_action_total + $score <= $daily_action_max // Did user reach a daily max for this action?
					&& $daily_recur_total + 1 <= $daily_recur_max // Did user reach a daily limit for recurrences on this action?
					&& $alltime_action_total + $score <= $alltime_action_max // Dis user reach an all time max for this action?
					&& $alltime_recur_total + 1 <= $alltime_recur_max // Did user reach an all time limit for recurrences on this action?
					&& (!$rule['reverse'] || $user->guid !== elgg_get_logged_in_user_guid())
			) {

				$rule_rel = elgg_echo("mechanics:{$rule['unique_name']}");

				$reason = elgg_echo('hj:mechanics:score:earned:reason', array(strtolower($rule_rel)));
				$id = create_annotation($user->guid, "gm_score", $score, '', $user->guid, ACCESS_PUBLIC);
				if ($id) {
					$history = new hjAnnotation();
					$history->owner_guid = $user->guid;
					$history->container_guid = $user->guid;
					$history->access_id = ACCESS_PRIVATE;
					$history->annotation_name = 'gm_score_history';
					$history->annotation_value = $score;
					$history->rule = $rule['unique_name'];
					$history->annotation_id = $id;
					$history->save();
				}

				if ($id && $user->guid == elgg_get_logged_in_user_guid()) {
					if ($score > 0) {
						system_message(elgg_echo('hj:mechanics:score:earned:for', array($score, $reason)));
					} else {
						system_message(elgg_echo('hj:mechanics:score:lost:for', array($score, $reason)));
					}
				}
			}

			access_show_hidden_entities($access_status);
		}
	}
	return $return;
}

//elgg_register_plugin_hook_handler('register', 'user', 'hj_mechanics_score_hook_handler', 999);
//
//function hj_mechanics_score_hook_handler($hook, $type, $return, $params) {
//
//	$rules = hj_mechanics_get_scoring_rules();
//
//	$rules = $rules["hook"]["$hook:$type"];
//
//	if ($rules && $return) {
//		foreach ($rules as $rule) {
//			$meets_conditions = true;
//			if ($conditions = $rule['conditions']) {
//				if (!is_array($conditions)) {
//					$conditions = array($conditions);
//				}
//				foreach ($conditions as $condition) {
//					$condition_param = $condition['param'];
//					$condition_meta = $condition['metadata_name'];
//					$condition_value = $condition['metadata_value'];
//					$entity = elgg_extract($condition_param, $params);
//					if ($entity->$condition_meta != $condition_value) {
//						$meets_conditions = false;
//					}
//				}
//			}
//			if ($meets_conditions) {
//				$score = elgg_get_plugin_setting($rule['unique_name'], 'hypeGameMechanics');
//				$user = elgg_get_logged_in_user_entity();
//				$id = create_annotation($user->guid, "game_mechanics_score_value", $score, '', $user->guid, ACCESS_PUBLIC);
//				create_annotation($user->guid, "game_mechanics_score_description_$id", elgg_echo("hj:mechanics:rule:{$rule['unique_name']}"), '', $user->guid, ACCESS_PUBLIC);
//			}
//		}
//	}
//
//	return $return;
//}