<?php

$user = elgg_extract('entity', $vars);
$size = elgg_extract('size', $vars);

if (!elgg_in_context('profile') || $size !== 'large') {
	return true;
}

$score = hj_mechanics_get_user_score($user);
$score_str = elgg_echo('hj:mechanics:currentscore', array($score));

if ($status = $user->gm_status) {
	$badge = get_entity($status);
	$status_icon = elgg_view_entity_icon($badge, 'tiny');
	$status_str = elgg_echo('hj:mechanics:currentstatus', array($badge->title));
}

echo elgg_view_image_block($status_icon, $score_str . '<br />' . $status_str);

