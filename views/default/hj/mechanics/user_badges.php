<?php
elgg_load_css('hj.mechanics.base');

$user = elgg_extract('user', $vars, false);

if (!$user) {
	$user = elgg_get_page_owner_entity();
}
$badges = elgg_get_entities_from_relationship(array(
	'relationship' => 'claimed',
	'relationship_guid' => $user->guid,
	'inverse_relationship' => false,
	'limit' => 0
		));

if ($badges) {
	//echo '<div class="hj-margin-ten hj-padding-ten hj-left">';
	//echo '<b>' . elgg_echo('hj:mechanics:badges') . '</b><br />';
	echo elgg_view_entity_list($badges, array(
		'full_view' => false,
		'list_type' => 'gallery',
		'icon_size' => elgg_extract('icon_size', 'small'),
		'gallery_class' => 'hj-badge-gallery',
		'item_class' => 'hj-badge-item'
	));
	//echo '</div>';
}