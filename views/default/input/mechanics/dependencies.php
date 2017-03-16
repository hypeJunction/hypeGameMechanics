<?php

namespace hypeJunction\GameMechanics;

$dependencies = elgg_extract('value', $vars);
if (!is_array($dependencies)) {
	$dependencies = array();
}

$value = array();
foreach ($dependencies as $dependency) {
	if (elgg_instanceof($dependency)) {
		$value[] = $dependency->guid;
	} else if (is_numeric($dependency)) {
		$value[] = (int) $dependency;
	}
}

$badges = get_badges();
$entity = elgg_extract('entity', $vars);

if ($badges) {
	echo '<ul class"elgg-input-checkboxes">';
	foreach ($badges as $badge) {
		if ($badge->guid == $entity->guid) {
			continue;
		}
		$icon = elgg_view('output/img', array(
			'src' => $badge->getIconURL('small')
		));

		echo '<li>';
		echo '<label>' . elgg_view('input/checkbox', array(
					'name' => 'dependencies[]',
					'value' => $badge->guid,
					'checked' => (in_array($badge->guid, $value))
				)) . $icon . $badge->title . '</label>';
		echo '</li>';
	}
	echo '</ul>';
}
