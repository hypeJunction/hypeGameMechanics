<?php

namespace hypeJunction\GameMechanics;

$entity = elgg_extract('entity', $vars);

$score = $entity->annotation_value;
if ((int)$score < 0) {
	$score_str = "<span class=\"gm-score-negative\">$score</span>";
} else {
	$score_str = "<span class=\"gm-score-positive\">+$score</span>";
}

$rule = $entity->rule;
$rule_str = elgg_echo('mechanics:' . $rule);
$time_str = elgg_view_friendly_time($entity->time_created);

echo elgg_view_image_block($score_str, $rule_str, array(
	'image_alt' => $time_str,
	'class' => 'gm-score-line-item',
));

