<?php

namespace hypeJunction\GameMechanics;

echo '<h3>' . elgg_echo('mechanics:settings:throttling') . '</h3>';
echo '<div class="elgg-text-help">' . elgg_echo('mechanics:settings:throttling:help') . '</div>';

$throttles = array(
	'daily_max',
	'daily_action_max',
	'alltime_action_max',
	'daily_recur_max',
	'alltime_recur_max',
	'object_recur_max',
	'daily_object_max',
	'alltime_object_max',
	'action_object_max',
);

echo '<div class="clearfix">';
foreach ($throttles as $throttle) {
	echo '<div class="elgg-col elgg-col-1of2">';
	echo '<div class="pam">';
	echo '<label>' . elgg_echo("mechanics:settings:$throttle") . '</label>';
	echo elgg_view('input/text', array(
		'value' => $vars['entity']->$throttle,
		'name' => "params[$throttle]"
	));
	echo '</div>';
	echo '</div>';
}
echo '<div class="elgg-col elgg-col-1of2">';
echo '<div class="pam">';
echo '<label>' . elgg_echo("mechanics:settings:allow_negative_total") . '</label>';
echo '<span class="elgg-text-help">' . elgg_echo('mechanics:settings:allow_negative_total:help') . '</span>';
echo elgg_view('input/dropdown', array(
	'value' => $vars['entity']->allow_negative_total,
	'name' => "params[allow_negative_total]",
	'options_values' => array(
		true => elgg_echo('option:yes'),
		false => elgg_echo('option:no')
	)
));
echo '</div>';
echo '</div>';
echo '</div>';

echo '<h3>' . elgg_echo('mechanics:settings:scoring_rules') . '</h3>';
echo '<div class="elgg-text-help">' . elgg_echo('mechanics:settings:scoring_rules:help') . '</div>';

$rules = get_scoring_rules('events');

echo '<div class="clearfix">';
foreach ($rules as $rule => $options) {
	echo '<div class="elgg-col elgg-col-1of2">';
	echo '<div class="pam">';
	echo '<label>' . $options['title'] . '</label><br />';
	echo elgg_view('input/text', array(
		'value' => $vars['entity']->$rule,
		'name' => "params[$rule]",
		'maxlength' => '3'
	));
	echo '</div>';
	echo '</div>';
}
echo '</div>';
