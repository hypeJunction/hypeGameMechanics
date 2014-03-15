<?php

elgg_load_css('hj.mechanics.base');

$entity = elgg_extract('entity', $vars, false);
$full = elgg_extract('full_view', $vars, false);
$icon_size = elgg_extract('icon_size', $vars, 'medium');

if ($full) {

	if (elgg_is_admin_logged_in()) {
		$params = hj_framework_extract_params_from_entity($entity);
		$menu = elgg_view_menu('hjentityhead', array(
			'entity' => $entity,
			'sort_by' => 'priority',
			'class' => 'elgg-menu-hz',
			'params' => $params
				));
	}
	$title = $entity->title;
	$icon = elgg_view_entity_icon($entity, 'large', array('img_class' => 'elgg-photo'));
	$description = $entity->description;
	$type = elgg_echo('badge_type:value:' . $entity->badge_type);

	$content = '<div class="hj-mechanics-badge-profile">' . $icon . '<div class="hj-mechanics-badge-description">' . $description . '</div><div class="hj-mechanics-badge-type">' . $type . '</div></div>';
	$col1 = elgg_view_module('aside', $title . $menu, $content);


	elgg_push_context('points');
	$rules = elgg_view('framework/mechanics/rules', $vars);
	elgg_pop_context();

	$col2 = elgg_view_module('aside', elgg_echo('mechanics:badge:requirements'), $rules);

	$other_users = elgg_get_entities_from_relationship(array(
		'relationship' => 'claimed',
		'relationship_guid' => $entity->guid,
		'inverse_relationship' => true,
		'limit' => 20
			));

	if ($other_users) {
		$other_users = elgg_view_entity_list($other_users, array(
			'list_type' => 'gallery',
			'size' => 'small'
				));

		$col2 .= elgg_view_module('aside', elgg_echo('mechanics:badge:usersclaimed'), $other_users);
	}

	$html = elgg_view_layout('hj/dynamic', array(
		'grid' => array(3, 9),
		'content' => array($col1, $col2)
			));

	echo $html;
} else {
	if (elgg_in_context('points')) {
		if (check_entity_relationship(elgg_get_logged_in_user_guid(), 'claimed', $entity->guid)) {
			$img_class = "elgg-photo hj-badge-claimed";
		} elseif (check_user_eligibility_for_badge($entity, elgg_get_logged_in_user_entity())) {
			$img_class = "elgg-photo hj-badge-eligible";
		} else {
			$img_class = "elgg-photo hj-badge-unclaimed";
		}
	} else {
		$img_class = "elgg-photo";
	}
	$icon = elgg_view_entity_icon($entity, $icon_size, array('img_class' => $img_class, 'href' => false, 'title' => $entity->title));

	if ($icon_size != 'tiny') {
		$title = $entity->title;
	}
	$params = array('params' => array(
			'entity_guid' => $entity->guid,
			'full_view' => true,
			'fbox_x' => 950,
			'target' => ''
			));

	$html = elgg_view('output/url', array(
		'text' => '<span>' . $icon . '<br />' . $title . '</span>',
		'href' => "action/framework/entities/view?e=$entity->guid",
		'is_action' => true,
		'data-options' => htmlentities(json_encode($params), ENT_QUOTES, 'UTF-8'),
		'rel' => 'fancybox',
		'class' => 'hj-ajaxed-view'
			));

	echo $html;
}
