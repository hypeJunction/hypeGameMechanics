<?php

elgg_load_js('mechanics:base');

$entity = elgg_extract('entity', $vars, false);

if ($entity) {
	$rule_values = elgg_get_metadata(array(
		'entity_guid' => $entity->guid,
		'metadata_names' => 'rules',
		'limit' => 0
			));
	foreach ($rule_values as $key => $rule) {
		$recurse = elgg_get_metadata(array(
			'entity_guid' => $entity->guid,
			'metadata_names' => "rule:$rule",
			'limit' => 0
				));
		$req[] = array('rule' => $rule, 'recurse' => $recurse[0]);
	}
}

echo '<ul class="hj-mechanics-badge-rules-list>';
foreach ($req as $rule) {
	echo '<li class="hj-mechanic-badge-rule>';
	echo '<span class="hj-mechanic-badge-rule-name">' . $rule['rule'] . '</span>';
	echo '<span class="hj-mechanic-badge-rule-value">' . $rule['recurse'] . '</span>';
	echo '</li>';
}
echo '</ul>';
