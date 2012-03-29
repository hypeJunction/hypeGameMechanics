<?php

function hj_mechanics_setup() {
	if (elgg_is_logged_in()) {
		hj_mechanics_setup_badge_form();
		//hj_mechanics_setup_gift_form();
		//hj_mechanics_setup_footprint_form();
		elgg_set_plugin_setting('hj:mechanics:setup', true);

		return true;
	}
	return false;
}


function hj_mechanics_setup_badge_form() {
	$form = new hjForm();
	$form->title = 'hypeGameMechanics:badge:create';
	$form->label = 'Create a Badge';
	$form->description = '';
	$form->subject_entity_subtype = 'hjbadge';
	$form->notify_admins = false;
	$form->add_to_river = true;
	$form->comments_on = false;
	$form->ajaxify = true;

	if ($form->save()) {
		$form->addField(array(
			'title' => 'Icon',
			'name' => 'icon',
			'input_type' => 'entity_icon',
		));

		$form->addField(array(
			'title' => 'Badge Name',
			'name' => 'title',
			'mandatory' => true
		));

		$form->addField(array(
			'title' => 'Description',
			'name' => 'description',
			'input_type' => 'longtext',
			'class' => 'elgg-input-longtext',
			'mandatory' => true
		));

		$form->addField(array(
			'title' => 'Badge Type',
			'name' => 'badge_type',
			'input_type' => 'dropdown',
			'options_values' => 'hj_mechanics_get_badge_types();',
			'mandatory' => true
		));
		
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
			'options' => 'hj_mechanics_prepare_badge_relationship_tags();'
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
}