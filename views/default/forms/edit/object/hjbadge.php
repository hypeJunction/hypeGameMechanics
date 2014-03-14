<?php

$entity = elgg_extract('entity', $vars);

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:icon') . '</label>';
echo elgg_view('input/file', array(
	'name' => 'icon',
	'value' => ($entity->icontime)
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:title') . '</label>';
echo elgg_view('input/text', array(
	'name' => 'title',
	'value' => $entity->title
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:description') . '</label>';
echo elgg_view('input/longtext', array(
	'name' => 'description',
	'value' => $entity->description
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:badge_type') . '</label>';
echo elgg_view('input/dropdown', array(
	'name' => 'badge_type',
	'value' => $entity->badge_type,
	'options_values' => get_badge_types()
));
echo '</div>';

echo '<div>';
echo '<label>' . elgg_echo('label:hjbadge:rules') . '</label>';
echo elgg_view('input/mechanics/rules', array(
	'entity' => $entity
));
echo '</div>';



		$form->addField(array(
			'title' => 'Rule',
			'name' => 'rules',
			'input_type' => 'mechanics_rule'
		));

		$form->addField(array(
			'title' => 'Points Required',
			'name' => 'points_required'
		));

		$form->addField(array(
			'title' => 'Cost in Points',
			'name' => 'points_cost'
		));

		$form->addField(array(
			'title' => 'Badges Required',
			'name' => 'badges_required',
			'input_type' => 'relationship_tags',
			'options' => 'prepare_badge_relationship_tags();'
		));

		$form->addField(array(
			'title' => 'Access Level',
			'input_type' => 'access',
			'mandatory' => true,
			'name' => 'access_id'
		));

		return true;
	}
	return false;