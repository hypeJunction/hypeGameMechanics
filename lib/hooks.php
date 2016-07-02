<?php

namespace hypeJunction\GameMechanics;

use ElggMenuItem;

/**
 * Setup scoring rules
 *
 * @param string $hook		Equals 'get_rules'
 * @param string $type		Equals 'gm_score'
 * @param array $return		An array of rules
 * @param array $params
 * @return array
 *
 */
function setup_scoring_rules($hook, $type, $return, $params) {

	$rules['events'] = array(
		/**
		 * Rule: publish a blog post
		 */
		'create:object:blog' => array(
			'title' => elgg_echo('mechanics:create:object:blog'),
			'events' => array(
				'publish::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'blog',
			),
			// override global settings
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: add a bookmark
		 */
		'create:object:bookmarks' => array(
			'title' => elgg_echo('mechanics:create:object:bookmarks'),
			'events' => array(
				'create::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'bookmarks',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: add a page
		 */
		'create:object:page' => array(
			'title' => elgg_echo('mechanics:create:object:page'),
			'events' => array(
				'create::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'page',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: add a top-level page
		 */
		'create:object:page_top' => array(
			'title' => elgg_echo('mechanics:create:object:page_top'),
			'events' => array(
				'create::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'page_top',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: add a file
		 */
		'create:object:file' => array(
			'title' => elgg_echo('mechanics:create:object:file'),
			'events' => array(
				'create::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'file',
			//'simletype' => array('image', 'document'),
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: add a wire post
		 */
		'create:object:thewire' => array(
			'title' => elgg_echo('mechanics:create:object:thewire'),
			'events' => array(
				'create::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'thewire',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: add a group discussion topic
		 */
		'create:object:groupforumtopic' => array(
			'title' => elgg_echo('mechanics:create:object:groupforumtopic'),
			'events' => array(
				'create::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'groupforumtopic',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: create a group
		 */
		'create:group:default' => array(
			'title' => elgg_echo('mechanics:create:group:default'),
			'events' => array(
				'create::group'
			),
			'attributes' => array(
				'type' => 'group',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: add a comment (annotation)
		 */
		'create:annotation:comment' => array(
			'title' => elgg_echo('mechanics:create:annotation:comment'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'generic_comment',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: receive a comment (annotation)
		 */
		'create:annotation:comment:reverse' => array(
			'title' => elgg_echo('mechanics:create:annotation:comment:reverse'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'subject_guid_attr' => 'entity_guid', // entity owner will be identified automatically
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'generic_comment',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: add a reply to a discussion (annotation)
		 */
		'create:annotation:group_topic_post' => array(
			'title' => elgg_echo('mechanics:create:annotation:group_topic_post'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'group_topic_post',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: receiving a reply to a discussion (annotation)
		 */
		'create:annotation:group_topic_post:reverse' => array(
			'title' => elgg_echo('mechanics:create:annotation:group_topic_post:reverse'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'subject_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'group_topic_post',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: liking something (annotation)
		 */
		'create:annotation:likes' => array(
			'title' => elgg_echo('mechanics:create:annotation:likes'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'likes',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: receiving a like
		 */
		'create:annotation:likes:reverse' => array(
			'title' => elgg_echo('mechanics:create:annotation:likes:reverse'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'subject_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'likes',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: adding a star rating (annotation)
		 */
		'create:annotation:starrating' => array(
			'title' => elgg_echo('mechanics:create:annotation:starrating'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'starrating',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: receiving a starrating
		 */
		'create:annotation:starrating:reverse' => array(
			'title' => elgg_echo('mechanics:create:annotation:starrating:reverse'),
			'events' => array(
				'create::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'subject_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'starrating',
			),
			'settings' => array(
				'object_recur_max' => 1,
			)
		),
		/**
		 * Rule: updating a blog post
		 */
		'update:object:blog' => array(
			'title' => elgg_echo('mechanics:update:object:blog'),
			'events' => array(
				'update::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'blog',
			),
			// override global settings
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: updating a bookmark
		 */
		'update:object:bookmarks' => array(
			'title' => elgg_echo('mechanics:update:object:bookmarks'),
			'events' => array(
				'update::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'bookmarks',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: updating a page
		 */
		'update:object:page' => array(
			'title' => elgg_echo('mechanics:update:object:page'),
			'events' => array(
				'update::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'page',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: updating a top-level page
		 */
		'update:object:page_top' => array(
			'title' => elgg_echo('mechanics:update:object:page_top'),
			'events' => array(
				'update::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'page_top',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: updating a file
		 */
		'update:object:file' => array(
			'title' => elgg_echo('mechanics:update:object:file'),
			'events' => array(
				'update::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'file',
			//'simletype' => array('image', 'document'),
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: updating a wire post
		 */
		'update:object:thewire' => array(
			'title' => elgg_echo('mechanics:update:object:thewire'),
			'events' => array(
				'update::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'thewire',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: updating a group discussion topic
		 */
		'update:object:groupforumtopic' => array(
			'title' => elgg_echo('mechanics:update:object:groupforumtopic'),
			'events' => array(
				'update::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'groupforumtopic',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: update a group
		 */
		'update:group:default' => array(
			'title' => elgg_echo('mechanics:update:group:default'),
			'events' => array(
				'update::group'
			),
			'attributes' => array(
				'type' => 'group',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: update a comment (annotation)
		 */
		'update:annotation:comment' => array(
			'title' => elgg_echo('mechanics:update:annotation:comment'),
			'events' => array(
				'update::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'generic_comment',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: update a reply to a discussion (annotation)
		 */
		'update:annotation:group_topic_post' => array(
			'title' => elgg_echo('mechanics:update:annotation:group_topic_post'),
			'events' => array(
				'update::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'group_topic_post',
			),
			'settings' => array(
				'object_recur_max' => 0,
			)
		),
		/**
		 * Rule: updating a star rating (annotation)
		 */
		'update:annotation:starrating' => array(
			'title' => elgg_echo('mechanics:update:annotation:starrating'),
			'events' => array(
				'update::annotation'
			),
			'object_guid_attr' => 'entity_guid',
			'attributes' => array(
				'type' => 'annotation',
				'name' => 'starrating',
			),
			'settings' => array(
			)
		),
		/**
		 * Rule: logging in
		 */
		'login:user:default' => array(
			'title' => elgg_echo('mechanics:login:user:default'),
			'events' => array(
				'login::user'
			),
			'attributes' => array(
			),
			'settings' => array(
				'daily_recur_max' => 1,
			)
		),
		/**
		 * Rule: updating profile
		 */
		'profileupdate:user:default' => array(
			'title' => elgg_echo('mechanics:profileupdate:user:default'),
			'events' => array(
				'profileupdate::user'
			),
			'attributes' => array(
			),
			'settings' => array(
			)
		),
		/**
		 * Rule: updating profile avatar
		 */
		'profileiconupdate:user:default' => array(
			'title' => elgg_echo('mechanics:profileiconupdate:user:default'),
			'events' => array(
				'profileiconupdate::user'
			),
			'attributes' => array(
			),
			'settings' => array(
			)
		),
		/**
		 * Rule: joining a group
		 */
		'join:group:user' => array(
			'title' => elgg_echo('mechanics:join:group:user'),
			'events' => array(
				'join::group'
			),
			'attributes' => array(
			),
			'settings' => array(
				'object_recur_max' => 1
			)
		),
		/**
		 * Rule: leaving a group
		 */
		'leave:group:user' => array(
			'title' => elgg_echo('mechanics:leave:group:user'),
			'events' => array(
				'leave::group'
			),
			'attributes' => array(
			),
			'settings' => array(
				'object_recur_max' => 1
			)
		),
		/**
		 * Rule: friending someone
		 */
		'create:relationship:friend' => array(
			'title' => elgg_echo('mechanics:create:relationship:friend'),
			'events' => array(
				'create::relationship'
			),
			'object_guid_attr' => 'guid_two',
			'subject_guid_attr' => 'guid_one',
			'attributes' => array(
				'relationship' => 'friend',
			),
			'settings' => array(
				'object_recur_max' => 1
			)
		),
		/**
		 * Rule: being friended by someone
		 */
		'create:relationship:friend:reverse' => array(
			'title' => elgg_echo('mechanics:create:relationship:friend:reverse'),
			'events' => array(
				'create::relationship'
			),
			'object_guid_attr' => 'guid_one',
			'subject_guid_attr' => 'guid_two',
			'attributes' => array(
				'relationship' => 'friend',
			),
			'settings' => array(
				'object_recur_max' => 1
			)
		),
		/**
		 * Rule: removing a friend
		 */
		'delete:relationship:friend' => array(
			'title' => elgg_echo('mechanics:create:relationship:friend'),
			'events' => array(
				'delete::relationship'
			),
			'object_guid_attr' => 'guid_two',
			'subject_guid_attr' => 'guid_one',
			'attributes' => array(
				'relationship' => 'friend',
			),
			'settings' => array(
				'object_recur_max' => 1
			)
		),
	);

	if (is_array($return)) {
		return array_merge_recursive($return, $rules);
	} else {
		return $rules;
	}
}

/**
 * Setup entity menu
 *
 * @param string $hook		Equals 'register'
 * @param string $type		Equals 'menu:entity'
 * @param array $return		Current menu
 * @param array $params		Additional params
 * @return array			Updated menu
 */
function entity_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity, 'object', HYPEGAMEMECHANICS_BADGE_SUBTYPE)) {
		return $return;
	}

	$return = array();

	if ($entity->canEdit()) {
		$options = array(
			'name' => 'edit',
			'text' => elgg_echo('edit'),
			'title' => elgg_echo('edit:this'),
			'href' => PAGEHANDLER . "/badge/edit/{$entity->guid}",
			'priority' => 200,
		);
		$return[] = ElggMenuItem::factory($options);

		$options = array(
			'name' => 'delete',
			'text' => elgg_view_icon('delete'),
			'title' => elgg_echo('delete:this'),
			'href' => "action/badge/delete?guid={$entity->guid}",
			'confirm' => elgg_echo('deleteconfirm'),
			'priority' => 300,
		);
		$return[] = ElggMenuItem::factory($options);
	}

	if (!gmReward::isClaimed($entity->guid) && gmReward::isEligible($entity->guid)) {
		$options = array(
			'name' => 'claim',
			'text' => elgg_echo('mechanics:claim'),
			'href' => "action/badge/claim?guid={$entity->guid}",
			'is_action' => true,
			'confirm' => ($entity->points_cost > 0) ? elgg_echo('mechanics:claim:confirm', array($entity->points_cost)) : false,
			'priority' => 400,
		);
		$return[] = ElggMenuItem::factory($options);
	}

	return $return;
}

/**
 * Setup owner block menu
 *
 * @param string $hook		Equals 'register'
 * @param string $type		Equals 'menu:owner_block'
 * @param array $return		Current menu
 * @param array $params		Additional params
 * @return array			Updated menu
 */
function owner_block_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);

	if (!elgg_instanceof($entity, 'user')) {
		return $return;
	}

	$return[] = ElggMenuItem::factory(array(
				'name' => 'badges',
				'text' => elgg_echo('mechanics:badges'),
				'href' => PAGEHANDLER . "/owner/$entity->username"
	));

	return $return;
}

/**
 * Setup user hover menu
 *
 * @param string $hook		Equals 'register'
 * @param string $type		Equals 'menu:user_hover'
 * @param array $return		Current menu
 * @param array $params		Additional params
 * @return array			Updated menu
 */
function user_hover_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	
	if (elgg_is_admin_logged_in()) {

		$reset = array(
			'name' => 'gm_reset',
			'text' => elgg_echo('mechanics:admin:reset'),
			'href' => "action/points/reset?user_guid=$entity->guid",
			'is_action' => true,
			'rel' => 'confirm',
			'section' => 'admin'
		);
		$return[] = ElggMenuItem::factory($reset);
	}

	if ($entity->canAnnotate(0, 'gm_score_award')) {

		elgg_load_js('jquery.form');
		
		$award = array(
			'name' => 'gm_score_award',
			'text' => elgg_echo('mechanics:admin:award'),
			'href' => PAGEHANDLER . "/award/$entity->guid",
			'class' => 'elgg-lightbox',
			//'section' => 'actions'
		);
		$return[] = ElggMenuItem::factory($award);
	}

	return $return;
}

/**
 * Override badge url handler
 *
 * @param string $hook		Equals 'entity:icon:url'
 * @param string $type		Equals 'object'
 * @param string $return	Default icon url
 * @param array $params		Additional params
 * @return string			Updated icon url
 */
function badge_icon_url_handler($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	$size = elgg_extract('size', $params, 'medium');

	if (elgg_instanceof($entity, 'object', HYPEGAMEMECHANICS_BADGE_SUBTYPE)) {
		return PAGEHANDLER . '/icon/' . $entity->guid . '/' . $size;
	}

	return $return;
}

/**
 * Check if current user can award points to the user
 * Currently, only admins can award points
 *
 * @param string $hook		Equals 'permissions_check:annotate'
 * @param string $type		Equals 'user'
 * @param boolean $return	Current permission
 * @param array $params		Additional params
 * @return boolean			Updated permission
 */
function permissions_check_gm_score_award($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params);
	$user = elgg_extract('user', $params);
	$annotation_name = elgg_extract('annotation_name', $params);

	if ($annotation_name !== 'gm_score_award') {
		return $return;
	}

	if (elgg_instanceof($entity, 'user') && $entity->isAdmin()) {
		// Do not allow awards on admins
		return false;
	}
	
	if (elgg_instanceof($entity, 'user') && elgg_instanceof($user, 'user') && $user->isAdmin()) {
		return true;
	}

	return $return;
}