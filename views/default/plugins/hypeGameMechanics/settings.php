<?php

namespace hypeJunction\GameMechanics;


echo '<h3>Scoring Rules - General</h3>';

echo '<div>';
echo '<label>Maximum Points per User per Day</label><br />';
echo elgg_view('input/text', array(
	'value' => $vars['entity']->daily_max,
	'name' => "params[daily_max]"
));
echo '</div>';

echo '<div>';
echo '<label>Maximum Score per Action per User per Day</label>';
echo elgg_view('input/text', array(
	'value' => $vars['entity']->daily_action_max,
	'name' => "params[daily_action_max]"
));
echo '</div>';

echo '<div>';
echo '<label>All-Time Maximum Score per Action per User</label>';
echo elgg_view('input/text', array(
	'value' => $vars['entity']->alltime_action_max,
	'name' => "params[alltime_action_max]"
));
echo '</div>';

echo '<div>';
echo '<label>Maximum number of times an Action Score can be credited per Day per User</label>';
echo elgg_view('input/text', array(
	'value' => $vars['entity']->daily_recur_max,
	'name' => "params[daily_recur_max]"
));
echo '</div>';

echo '<div>';
echo '<label>All-Time Maximum number of times an Action Score can be credited to User</label>';
echo elgg_view('input/text', array(
	'value' => $vars['entity']->alltime_recur_max,
	'name' => "params[alltime_recur_max]"
));
echo '</div>';

echo '<div>';
echo '<label>Allow negative totals</label>';
echo elgg_view('input/dropdown', array(
	'value' => $vars['entity']->allow_negative_total,
	'name' => "params[allow_negative_total]",
	'options_values' => array(
		'allow' => elgg_echo('mechanics:allownegativetotal'),
		'forbid' => elgg_echo('mechanics:forbidnegativetotal')
	)
));
echo '</div>';



echo '<h3>Scoring Rules - Actions/Points</h3>';

echo '<i>Please enter a number of score points (positive or negative) to be added/deducted per action</i>';

$rules = get_scoring_rules_list();

foreach ($rules as $rule => $name) {
	echo '<div>';
	echo '<label>' . $name . '</label><br />';
	echo elgg_view('input/text', array(
		'value' => $vars['entity']->get($rule),
		'name' => "params[$rule]",
		'maxlength' => '3'
			));
	echo '</div>';
}

//echo elgg_view('admin/hj/sections/extend', array('plugin' => 'mechanics'));
