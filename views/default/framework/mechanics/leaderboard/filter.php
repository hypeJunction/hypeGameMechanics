<?php

$limit_label = '<label>' . elgg_echo('mechanics:leaderboard:limit') . '</label>';
$limit_input = elgg_view('input/dropdown', array(
	'name' => 'limit',
	'value' => get_input('limit', 10),
	'options' => array(5, 10, 25, 50, 100)
		));

$period_label = '<label>' . elgg_echo('mechanics:leaderboard:period') . '</label>';
$period_input = elgg_view('input/dropdown', array(
	'name' => 'period',
	'value' => get_input('period', 'all'),
	'options_values' => array(
		'all' => elgg_echo('mechanics:period:all'),
		'year' => elgg_echo('mechanics:period:year'),
		'month' => elgg_echo('mechanics:period:month'),
		'week' => elgg_echo('mechanics:period:week'),
		'day' => elgg_echo('mechanics:period:day'),
	)
		));

$submit = elgg_view('input/submit', array(
	'text' => elgg_echo('filter')
));

$form_body = <<<HTML
<div class="clearfix">
	<span class="float pam">$limit_label</span>
	<span class="float pam">$limit_input</span>
	<span class="float pam">$period_label</span>
	<span class="float pam">$period_input</span>
	<span class="float pam">$submit</span>
</div>
HTML;

echo elgg_view('input/form', array(
	'body' => $form_body,
	'action' => 'points/leaderboard',
	'method' => 'GET'
));




