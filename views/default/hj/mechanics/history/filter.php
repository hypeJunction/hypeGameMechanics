<?php

$limit_label = '<label>' . elgg_echo('hj:mechanics:leaderboard:limit') . '</label>';
$limit_input = elgg_view('input/dropdown', array(
	'name' => 'limit',
	'value' => get_input('limit', 10),
	'options' => array(5, 10, 25, 50, 100)
		));

$period_label = '<label>' . elgg_echo('hj:mechanics:leaderboard:period') . '</label>';
$period_input = elgg_view('input/dropdown', array(
	'name' => 'period',
	'value' => get_input('period', 'all'),
	'options_values' => array(
		'all' => elgg_echo('hj:mechanics:period:all'),
		'year' => elgg_echo('hj:mechanics:period:year'),
		'month' => elgg_echo('hj:mechanics:period:month'),
		'week' => elgg_echo('hj:mechanics:period:week'),
		'day' => elgg_echo('hj:mechanics:period:day'),
	)
		));

$submit = elgg_view('input/submit', array(
	'text' => elgg_echo('filter')
));

$form_body = <<<HTML
<div class="clearfix">
	<span class="hj-left hj-padding-ten">$limit_label</span>
	<span class="hj-left hj-padding-ten">$limit_input</span>
	<span class="hj-left hj-padding-ten">$period_label</span>
	<span class="hj-left hj-padding-ten">$period_input</span>
	<span class="hj-left hj-padding-ten">$submit</span>
</div>
HTML;

$user = elgg_get_page_owner_entity();
echo elgg_view('input/form', array(
	'body' => $form_body,
	'action' => "points/history/$user->username",
	'method' => 'GET'
));