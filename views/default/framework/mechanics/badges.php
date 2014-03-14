<?php

$limit = get_input('limit', 0);

$badges = hj_framework_get_entities_by_priority('object', 'hjbadge', null, null, $limit, $offset);

foreach ($badges as $badge) {
	$badges_by_type[$badge->badge_type][] = $badge;
}

if (elgg_is_admin_logged_in()) {
	$form = hj_framework_get_data_pattern('object', 'hjbadge');
	$params = array(
		'form_guid' => $form->guid,
		'fbox_x' => 900,
		'full_view' => false,
		'target' => 'hj-mechanics-badges-new'
	);
	$params = hj_framework_extract_params_from_params($params);
	$params = hj_framework_json_query($params);

	$html .= elgg_view('output/url', array(
		'text' => elgg_echo('mechanics:badge:create'),
		'href' => 'action/framework/entities/edit',
		'data-options' => htmlentities($params, ENT_QUOTES, 'UTF-8'),
		'is_action' => true,
		'class' => 'hj-ajaxed-view',
		'rel' => 'fancybox',
			));
	$html .= elgg_view_entity_list(array(), array(
		'list_id' => 'hj-mechanics-badges-new',
		'list_type' => 'gallery',
		'gallery_class' => 'hj-badge-gallery',
		'item_class' => 'hj-badge-item'
			));
	$page .= elgg_view_module('badges', elgg_echo('mechanics:badges:new'), $html);
}
$badges = array();

foreach ($badges_by_type as $type => $badges) {
	if (elgg_is_admin_logged_in() || $type !== 'surprise') {
		$html = elgg_view_entity_list($badges, array(
			'full_view' => false,
			'list_type' => 'gallery',
			'gallery_class' => 'hj-badge-gallery',
			'item_class' => 'hj-badge-item'
				));
	}

	$page .= elgg_view_module('badges', elgg_echo('badge_type:value:' . $type), $html);
}

echo $page;