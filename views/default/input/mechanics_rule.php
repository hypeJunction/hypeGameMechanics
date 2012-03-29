<?php

elgg_load_js('hj:mechanics:base');

$entity = elgg_extract('entity', $vars, false);

if ($entity) {
	$rules = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'hjannotation',
		'container_guid' => $entity->guid,
		'limit' => 10,
		'metadata_name_value_pairs' => array(
			array('name' => 'annotation_name', 'value' => 'badge_rule'),
		),
			));
}

$options_values = array();
$options_values[''] = '';

$rules_list = hj_mechanics_get_scoring_rules_list();
foreach ($rules_list as $name => $str) {
	$score = elgg_get_plugin_setting($name, 'hypeGameMechanics');

	if ($score && (int) $score !== 0 && !empty($score)) {
		$options_values[$name] = $str;
	}
}

for ($i = 0; $i <= 9; $i++) {

	$field .= '<div><label>' . elgg_echo('hj:mechanics:badges:rule') . '</label><br/>';
	$field .= elgg_view('input/dropdown', array(
		'name' => "rules[$i]",
		'options_values' => $options_values,
		'value' => $rules[$i]->annotation_value
			));
	$field .= '</div>';

	$field .= '<div><label>' . elgg_echo('hj:mechanics:badges:recurse') . '</label><br/>';
	$field .= elgg_view('input/text', array(
		'name' => "recurse[$i]",
		'value' => $rules[$i]->recurse
			));
	$field .= '</div>';
	$field .= '<hr />';
}

$legend = elgg_echo('hj:mechanics:badges:rules');
$html = <<<HTML
	<fieldset>
		<legend>$legend</legend>
			$field
	</fieldset>
HTML;

echo $html;