<?php

$entity = elgg_extract('entity', $vars);

$score = $entity->annotation_value;
$rule = $entity->rule;
$rule_str = elgg_echo('mechanics:' . $rule);
$time_str = elgg_view_friendly_time($entity->time_created);

echo elgg_view_layout('hj/dynamic', array(
	'grid' => array(6, 3, 3),
	'content' => array($rule_str, $score, $time_str)
));

