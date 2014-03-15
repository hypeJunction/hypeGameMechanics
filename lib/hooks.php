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
 */
function setup_scoring_rules($hook, $type, $return, $params) {

	$rules['events'] = array(

		// Adding a blog post
		'create:object:blog' => array(
			'title' => elgg_echo('mechanics:create:object:blog'),
			'events' => array(
				'create::object',
				'update::object',
				'publish::object'
			),
			'attributes' => array(
				'type' => 'object',
				'subtype' => 'blog',
				//'access_id' => array(ACCESS_PUBLIC, ACCESS_LOGGED_IN),
			),

			// override global settings
			'settings' => array(
				
				'daily_max' => 0,
				'daily_action_max' => 0,
				'alltime_action_max' => 0,

				'daily_recur_max' => 0,
				'alltime_recur_max' => 0,

				'daily_object_max' => 5,
				'alltime_object_max' => 0,
				'action_object_max' => 0,

				'allow_negative_total' => true,
			)
		),

//		'create:object:bookmarks' => 'Adding a bookmark',
//		'create:object:page' => 'Adding a page',
//		'create:object:page_top' => 'Adding a top level page',
//		'create:object:file' => 'Uploading a file',
//		'create:object:thewire' => 'Adding a wire post',
//		'create:object:groupforumtopic' => 'Adding a group forum topic',
//		'create:group:default' => 'Creating a group',
//		'create:annotation:comment' => 'Adding a comment',
//		'create:annotation:comment:reverse' => 'Receiving a comment',
//		'create:annotation:group_topic_post' => 'Adding a post to group forum',
//		'create:annotation:group_topic_post:reverse' => 'Receiving an answer to your group forum topic',
//		'create:annotation:likes' => 'Liking',
//		'create:annotation:likes:reverse' => 'Having your item liked',
//		'create:annotation:starrating' => 'Rating',
//		'create:annotation:starrating:reverse' => 'Receiving a rating on your item',
//		'update:object:blog' => 'Editing a blog post',
//		'update:object:bookmarks' => 'Updating a bookmark',
//		'update:object:page' => 'Editing a page',
//		'update:object:page_top' => 'Editing a top level page',
//		'update:object:file' => 'Updating a file',
//		'update:object:thewire' => 'Editing a wire post',
//		'update:object:groupforumtopic' => 'Editing a group forum topic',
//		'update:group:default' => 'Updating a group',
//		'update:annotation:comment' => 'Updating a comment',
//		'update:annotation:group_topic_post' => 'Editing a group forum post',
//		'update:annotation:likes' => 'Editing a like',
//		'update:annotation:starrating' => 'Editing the rating',
//		'delete:object:blog' => 'Deleting a blog post',
//		'delete:object:bookmarks' => 'Deleting a bookmark',
//		'delete:object:page' => 'Deleting a page',
//		'delete:object:page_top' => 'Deleting a top level page',
//		'delete:object:file' => 'Deleting a file',
//		'delete:object:thewire' => 'Deleting a wire post',
//		'delete:object:groupforumtopic' => 'Deleting a group forum topic',
//		'delete:group:default' => 'Removing a group',
//		'delete:annotation:comment' => 'Deleting a comment',
//		'delete:annotation:group_topic_post' => 'Deleting a group forum post',
//		'delete:annotation:likes' => 'Unliking',
//		'delete:annotation:starrating' => 'Removing the rating',
//		'login:user:default' => 'Logging in',
//		'profileupdate:user:default' => 'Updating profile',
//		'profileiconupdate:user:default' => 'Updating avatar',
//		'join:group:user' => 'Joining a group',
//		'leave:group:user' => 'Leaving a group',
//		'create:relationship:friend' => 'Adding a friend',
//		'create:relationship:friend:reverse' => 'Becoming a friend',
//		'delete:relationship:friend' => 'Removing a friend',
//		'create:object:hjannotation:generic_comment' => 'Adding a comment',
//		'create:object:hjannotation:generic_comment:reverse' => 'Receiving a comment',
//		'create:object:hjannotation:likes' => 'Liking',
//		'create:object:hjannotation:likes:reverse' => 'Having your item liked',
//		'create:object:hjannotation:group_topic_post' => 'Replying to a group forum topic',
//		'create:object:hjannotation:group_topic_post:reverse' => 'mechanics:create:object:hjannotation:group_topic_post:reverse',
//		'create:object:hjannotation:hjforumpost' => 'Posting a reply to a forum topic',
//		'create:object:hjannotation:hjforumpost:reverse' => 'Receiving a response to a forum topic',
//		'update:object:hjannotation:generic_comment' => 'Editing a comment',
//		'update:object:hjannotation:likes' => 'Editing a like',
//		'update:object:hjannotation:group_topic_post' => 'Updating a group forum topic',
//		'update:object:hjannotation:hjforumpost' => 'Editing a post in a forum topic',
//		'delete:object:hjannotation:generic_comment' => 'Removing a comment',
//		'delete:object:hjannotation:likes' => 'Removing a like',
//		'delete:object:hjannotation:group_topic_post' => 'Deleting a reply to a group forum topic',
//		'delete:object:hjannotation:hjforumpost' => 'Deleting a post from a forum topic',
	);

	if (is_array($return)) {
		return array_merge_recursive($return, $rules);
	} else {
		return $rules;
	}
}

function default_scoring_rules_setup($hook, $type, $return, $params) {

	$events = array(
		'create',
		'update',
		'delete'
	);

	$registered_entities = elgg_get_config('registered_entities');

	$types['object'] = $registered_entities['object'];

	if (count($registered_entities['group'])) {
		$types['group'] = $registered_entities['group'];
	} else {
		$types['group'] = array(
			'default'
		);
	}

	$types['annotation'] = array(
		'comment',
		'group_topic_post',
		'likes',
		'starrating'
	);


	foreach ($events as $event) {
		foreach ($types as $type => $subtypes) {
			foreach ($subtypes as $subtype) {
				$return['event']["$event:$type:$type:$subtype"][] = array(
					'conditions' => array(),
					'unique_name' => "$event:$type:$subtype"
				);

				// Add reverse (get points when your entity was acted upon)
				if (($type == 'metadata' || $type == 'annotation' || $type == 'relationship') && $event == 'create') {
					// Metadata of an object that describes the relationship
					// We will see if owner of this entity is user, else we will assume the entity is user
					switch ($type) {
						case 'annotation' :
							$user_meta = 'entity_guid';
							$return['event']["$event:$type:$type:$subtype"][] = array(
								'conditions' => array(),
								'unique_name' => "$event:$type:$subtype:reverse",
								'reverse' => true,
								'user' => $user_meta
							);
							break;

						case 'relationship' :
							$user_meta = 'guid_two';
							$return['event']["$event:$subtype:$type:$subtype"][] = array(
								'conditions' => array(),
								'unique_name' => "$event:$type:$subtype:reverse",
								'reverse' => true,
								'user' => $user_meta
							);
							break;

						default :
							break;
					}
				}
			}
		}
	}


	/**
	 * User login and profile updates
	 */
	$return['event']["login:user:user:default"][] = array(
		'conditions' => array(),
		'unique_name' => "login:user:default"
	);

	$return['event']["profileupdate:user:user:default"][] = array(
		'conditions' => array(),
		'unique_name' => "profileupdate:user:default"
	);

	$return['event']["profileiconupdate:user:user:default"][] = array(
		'conditions' => array(),
		'unique_name' => "profileiconupdate:user:default"
	);


	/**
	 * Groups join / leave
	 */
	$return['event']["create:member:relationship:member"][] = array(
		'conditions' => array(),
		'user' => 'guid_one',
		'unique_name' => "join:group:user"
	);
	$return['event']["leave:group:user:default"][] = array(
		'conditions' => array(),
		'unique_name' => "leave:group:user"
	);

	$return['event']["create:friend:relationship:friend"][] = array(
		'conditions' => array(),
		'user' => 'guid_one',
		'unique_name' => "create:relationship:friend"
	);
	$return['event']["delete:friend:relationship:friend"][] = array(
		'conditions' => array(),
		'user' => 'guid_one',
		'unique_name' => "delete:relationship:friend"
	);

	$return['event']["create:friend:relationship:friend"][] = array(
		'conditions' => array(),
		'user' => 'guid_two',
		'reverse' => true,
		'unique_name' => "create:relationship:friend:reverse"
	);


	/**
	 * hjAnnotation
	 */
	$annotation_names = array('generic_comment', 'likes', 'group_topic_post', 'hjforumpost');

	foreach ($events as $event) {
		foreach ($annotation_names as $aname) {
			$return['event']["$event:object:object:hjannotation"][] = array(
				'conditions' => array(
					array(
						'metadata_name' => 'annotation_name',
						'metadata_value' => $aname
					)
				),
				'unique_name' => "$event:object:hjannotation:$aname"
			);

			if ($event == 'create') {
				$return['event']["$event:object:object:hjannotation"][] = array(
					'conditions' => array(
						array(
							'metadata_name' => 'annotation_name',
							'metadata_value' => $aname
						)
					),
					'reverse' => true,
					'user' => 'owner_guid',
					'unique_name' => "$event:object:hjannotation:$aname:reverse"
				);
			}
		}
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

	if (!elgg_is_admin_logged_in()) {
		return $return;
	}

	$entity = elgg_extract('entity', $params);

	$reset = array(
		'name' => 'gm_reset',
		'text' => elgg_echo('mechanics:admin:reset'),
		'href' => "action/points/reset?user_guid=$entity->guid",
		'is_action' => true,
		'rel' => 'confirm',
		'section' => 'admin'
	);
	$return[] = ElggMenuItem::factory($reset);

	return $return;
}
