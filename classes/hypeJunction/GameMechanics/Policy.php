<?php

namespace hypeJunction\GameMechanics;

use Elgg\Database\QueryBuilder;
use Elgg\Hook;
use ElggData;
use ElggEntity;
use ElggUser;

class Policy {

	/**
	 * Get rule definitions
	 *
	 * @param string $type Rule type, e.g. events
	 *
	 * @return array
	 */
	public static function getRules($type = null) {
		$rules = elgg_trigger_plugin_hook('get_rules', 'gm_score', null, []);

		if ($type && array_key_exists($type, $rules)) {
			return $rules[$type];
		} else {
			return $rules;
		}
	}

	/**
	 * Get user score total in a given time frame
	 *
	 * @param ElggUser $user       User entity
	 * @param int      $time_lower Lower time constraint
	 * @param int      $time_upper Upper time constraint
	 *
	 * @return int
	 */
	public static function getUserScore($user = null, $time_lower = null, $time_upper = null) {
		if (!$user instanceof ElggUser) {
			return 0;
		}

		$options = [
			'types' => 'object',
			'subtypes' => Score::SUBTYPE,
			'container_guids' => $user->guid,
			'metadata_names' => 'annotation_value',
			'metadata_calculation' => 'sum',
			'metadata_created_time_lower' => $time_lower,
			'metadata_created_time_upper' => $time_upper,
		];

		return (int) elgg_get_metadata($options);
	}

	/**
	 * Get a list of users ordered by their total score
	 *
	 * @param int $time_lower Lower time constraint
	 * @param int $time_upper Upper time constraint
	 *
	 * @return ElggEntity[]
	 */
	public static function getLeaderboard($time_lower = null, $time_upper = null, $limit = 10, $offset = 0) {
		$options = [
			'types' => 'user',
			'annotation_names' => 'gm_score',
			'annotation_created_time_lower' => $time_lower,
			'annotation_created_time_upper' => $time_upper,
			'limit' => $limit,
			'offset' => $offset,
		];

		return elgg_get_entities($options) ? : [];
	}

	/**
	 * Get total score for a specified action rule
	 *
	 * @param ElggUser $user       User entity
	 * @param string   $rule       Rule name
	 * @param int      $time_lower Lower time constraint
	 * @param int      $time_upper Upper time constraint
	 *
	 * @return int
	 */
	public static function getUserActionTotal($user, $rule, $time_lower = null, $time_upper = null) {

		if (empty($rule) || !$user instanceof ElggUser) {
			return 0;
		}

		$options = [
			'type' => 'object',
			'subtype' => Score::SUBTYPE,
			'container_guid' => $user->guid,
			'metadata_names' => 'annotation_value',
			'metadata_calculation' => 'sum',
			'metadata_created_time_lower' => $time_lower,
			'metadata_created_time_upper' => $time_upper,
			'wheres' => [
				function (QueryBuilder $qb) use ($rule) {
					$qb->joinMetadataTable('e', 'guid', 'rule', 'inner', 'rulemd');

					return $qb->compare('rulemd.value', '=', $rule, ELGG_VALUE_STRING);
				},
			],
		];

		return (int) elgg_get_metadata($options);
	}

	/**
	 * Get the number of recurrences when user was awarded points for a given rule action on an object
	 *
	 * @param ElggUser $user       User entity
	 * @param string   $rule       Rule name
	 * @param int      $time_lower Lower time constraint
	 * @param int      $time_upper Upper time constraint
	 *
	 * @return int
	 */
	public static function getUserRecurTotal($user, $rule, $time_lower = null, $time_upper = null) {
		if (empty($rule) || !$user instanceof ElggUser) {
			return 0;
		}

		$options = [
			'types' => 'object',
			'subtypes' => Score::SUBTYPE,
			'container_guids' => $user->guid,
			'created_time_lower' => $time_lower,
			'created_time_upper' => $time_upper,
			'metadata_name_value_pairs' => [
				[
					'name' => 'rule',
					'value' => $rule,
				]
			],
			'count' => true,
		];

		return elgg_get_entities($options);
	}

	/**
	 * Get total score that was collected on an object by a given user with a given rule in given time frame
	 *
	 * @param ElggData $object     Object
	 * @param ElggUser $user       User entity
	 * @param string   $rule       Rule name
	 * @param int      $time_lower Lower time constraint
	 * @param int      $time_upper Upper time constraint
	 *
	 * @return int
	 */
	public static function getObjectTotal($object, $user = null, $rule = null, $time_lower = null, $time_upper = null) {
		if (!$object instanceof ElggData) {
			return 0;
		}

		$object_id = (isset($object->guid)) ? $object->guid : $object->id;
		$object_type = $object->getType();

		$options = [
			'type' => 'object',
			'subtype' => Score::SUBTYPE,
			'container_guid' => $user->guid,
			'metadata_names' => 'annotation_value',
			'metadata_calculation' => 'sum',
			'metadata_created_time_lower' => $time_lower,
			'metadata_created_time_upper' => $time_upper,
			'wheres' => [
				function (QueryBuilder $qb) use ($object_type, $object_id) {
					$qb->joinMetadataTable('e', 'guid', 'object_ref', 'inner', 'objmd');

					return $qb->compare('objmd.value', '=', "$object_type:$object_id", ELGG_VALUE_STRING);
				}
			],
		];

		if (!empty($rule)) {
			$options['wheres'][] = function (QueryBuilder $qb) use ($rule) {
				$qb->joinMetadataTable('e', 'guid', 'rule', 'inner', 'rulemd');

				return $qb->compare('rulemd.value', '=', $rule, ELGG_VALUE_STRING);
			};
		}

		return (int) elgg_get_metadata($options);
	}

	/**
	 * Get the number of recurrences when user was awarded points for a given rule action on an object
	 *
	 * @param ElggData $object     Object
	 * @param ElggUser $user       User entity
	 * @param string   $rule       Rule name
	 * @param int      $time_lower Lower time constraint
	 * @param int      $time_upper Upper time constraint
	 *
	 * @return int
	 */
	public static function getObjectRecurTotal($object, $user = null, $rule = null, $time_lower = null, $time_upper = null) {
		if (!$object instanceof ElggData) {
			return 0;
		}

		$object_id = (isset($object->guid)) ? $object->guid : $object->id;
		$object_type = $object->getType();

		$options = [
			'types' => 'object',
			'subtypes' => Score::SUBTYPE,
			'container_guids' => $user->guid,
			'created_time_lower' => $time_lower,
			'created_time_upper' => $time_upper,
			'metadata_name_value_pairs' => [
				['name' => 'rule', 'value' => $rule],
				['name' => 'object_ref', 'value' => "$object_type:$object_id"]
			],
			'count' => true,
		];

		return elgg_get_entities($options);
	}

	/**
	 * Reward user with applicable badges
	 *
	 * @param ElggUser $user User entity
	 *
	 * @return boolean
	 * @throws \DatabaseException
	 */
	public static function rewardUser($user = null) {

		if (!$user) {
			$user = elgg_get_logged_in_user_entity();
		}

		$gmReward = Reward::rewardUser($user);

		$errors = $gmReward->getErrors();
		if ($errors) {
			foreach ($errors as $error) {
				register_error($error);
			}
		}

		$messages = $gmReward->getMessages();
		if ($messages) {
			foreach ($messages as $message) {
				system_message($message);
			}
		}

		$badges = $gmReward->getNewUserBadges();

		if (count($badges)) {
			foreach ($badges as $badge) {
				if ($user->guid == elgg_get_logged_in_user_guid()) {
					system_message(elgg_echo('mechanics:badge:claim:success', [$badge->title]));
				} else {
					// @todo: send notification instead?
				}

				elgg_create_river_item([
					'view' => 'framework/mechanics/river/claim',
					'action_type' => 'claim',
					'subject_guid' => $user->guid,
					'object_guid' => $badge->guid,
				]);
			}
		}

		return true;
	}

	/**
	 * Get site badges
	 *
	 * @param array $options ege* option
	 * @param void  $ignore  Ignored
	 *
	 * @return array|false
	 */
	public static function getBadges($options = [], $ignore = null) {

		$defaults = [
			'types' => 'object',
			'subtypes' => Badge::SUBTYPE,
			'order_by_metadata' => [
				'name' => 'priority',
				'direction' => 'ASC',
				'as' => 'integer'
			],
		];

		$options = array_merge($defaults, $options);

		return elgg_get_entities($options);
	}

	/**
	 * Get badges of a given type
	 *
	 * @param string $type
	 * @param array  $options
	 * @param string $getter
	 *
	 * @return array|false
	 */
	public static function getBadgesByType($type = '', $options = [], $getter = 'elgg_get_entities_from_metadata') {
		$options['metadata_name_value_pairs'] = [
			'name' => 'badge_type',
			'value' => $type,
		];

		return get_badges($options, $getter);
	}

	/**
	 * Get types of badges
	 * @return array
	 */
	public static function getBadgeTypes() {
		$return = [
			'status' => elgg_echo('badge_type:value:status'),
			'experience' => elgg_echo('badge_type:value:experience'),
			//'purchase' => elgg_echo('badge_type:value:purchase'),
			'surprise' => elgg_echo('badge_type:value:surprise')
		];

		$return = elgg_trigger_plugin_hook('mechanics:badge_types', 'object', null, $return);

		return $return;
	}

	/**
	 * Get badges that are required to uncover this badge
	 *
	 * @param int $badge_guid GUID of the badge
	 *
	 * @return array|false
	 */
	public static function getBadgeDependencies($badge_guid) {
		return elgg_get_entities([
			'types' => 'object',
			'subtypes' => Badge::SUBTYPE,
			'relationship' => 'badge_required',
			'relationship_guid' => $badge_guid,
			'inverse_relationship' => true
		]);
	}

	/**
	 * Get badge rules
	 *
	 * @param int $badge_guid GUID of the badge
	 *
	 * @return array|false
	 */
	public static function getBadgeRules($badge_guid) {
		return elgg_get_entities([
			'type' => 'object',
			'subtype' => BadgeRule::SUBTYPE,
			'container_guid' => $badge_guid,
			'limit' => 10,
		]);
	}

	/**
	 * Check if the event qualifies for points and award them to the user
	 *
	 * @param string $event  Event type
	 * @param string $type   'object'|'user'|'group'|'relationship'|'annotation'|'metadata'
	 * @param mixed  $object Event object
	 *
	 * @return boolean
	 */
	public static function applyEventRules($event, $type, $object) {

		// Object
		if (is_object($object)) {
			$entity = $object;
		} else if (is_array($object)) {
			$entity = elgg_extract('entity', $object, null);
			if (!$entity) {
				$entity = elgg_extract('user', $object, null);
			}
			if (!$entity) {
				$entity = elgg_extract('group', $object, null);
			}
		}

		if (!is_object($entity)) {
			// Terminate early, nothing to act upon
			return true;
		}

		// Get rules associated with events
		$rules = get_scoring_rules('events');

		$event_name = "$event::$type";

		// Apply rules
		foreach ($rules as $rule_name => $rule_options) {

			if (!in_array($event_name, (array) $rule_options['events'])) {
				continue;
			}

			$rule_options['name'] = $rule_name;
			$Rule = Rule::applyRule($entity, $rule_options, $event_name);

			$errors = $Rule->getErrors();
			if ($errors) {
				foreach ($errors as $error) {
					register_error($error);
				}
			}

			$messages = $Rule->getMessages();
			if ($messages) {
				foreach ($messages as $message) {
					system_message($message);
				}
			}

			if ($Rule->terminateEvent()) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Setup scoring rules
	 *
	 * @param Hook $hook Hook
	 *
	 * @return array
	 */
	public static function setupRules(Hook $hook) {
		$rules = $hook->getValue();

		$rules['events'] = [
			/**
			 * Rule: publish a blog post
			 */
			'create:object:blog' => [
				'title' => elgg_echo('mechanics:create:object:blog'),
				'events' => [
					'publish::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'blog',
				],
				// override global settings
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: add a bookmark
			 */
			'create:object:bookmarks' => [
				'title' => elgg_echo('mechanics:create:object:bookmarks'),
				'events' => [
					'create::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'bookmarks',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: add a page
			 */
			'create:object:page' => [
				'title' => elgg_echo('mechanics:create:object:page'),
				'events' => [
					'create::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'page',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: add a top-level page
			 */
			'create:object:page_top' => [
				'title' => elgg_echo('mechanics:create:object:page_top'),
				'events' => [
					'create::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'page_top',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: add a file
			 */
			'create:object:file' => [
				'title' => elgg_echo('mechanics:create:object:file'),
				'events' => [
					'create::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'file',
					//'simletype' => array('image', 'document'),
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: add a wire post
			 */
			'create:object:thewire' => [
				'title' => elgg_echo('mechanics:create:object:thewire'),
				'events' => [
					'create::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'thewire',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: add a group discussion topic
			 */
			'create:object:groupforumtopic' => [
				'title' => elgg_echo('mechanics:create:object:groupforumtopic'),
				'events' => [
					'create::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'groupforumtopic',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: create a group
			 */
			'create:group:default' => [
				'title' => elgg_echo('mechanics:create:group:default'),
				'events' => [
					'create::group'
				],
				'attributes' => [
					'type' => 'group',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: add a comment
			 */
			'create:annotation:comment' => [
				'title' => elgg_echo('mechanics:create:annotation:comment'),
				'events' => [
					'create::object'
				],
				'object_guid_attr' => 'container_guid',
				'attributes' => [
					'type' => 'object',
					'subtype' => 'comment',
				],
				'settings' => [
					'object_recur_max' => 0,
				],
				'callbacks' => [
					function (Rule $rule) {
						if ($rule->getObject()->owner_guid == $rule->getSubject()->guid) {
							return false;
						}

						return true;
					},
				],
			],
			/**
			 * Rule: receive a comment
			 */
			'create:annotation:comment:reverse' => [
				'title' => elgg_echo('mechanics:create:annotation:comment:reverse'),
				'events' => [
					'create::object'
				],
				'object_guid_attr' => 'container_guid',
				'subject_guid_attr' => 'container_guid', // entity owner will be identified automatically
				'attributes' => [
					'type' => 'object',
					'subtype' => 'comment',
				],
				'settings' => [
					'object_recur_max' => 0,
				],
				'callbacks' => [
					function (Rule $rule) {
						if ($rule->getObject()->owner_guid == $rule->getSubject()->guid) {
							return false;
						}

						return true;
					},
				],
			],
			/**
			 * Rule: add a reply to a discussion
			 */
			'create:annotation:group_topic_post' => [
				'title' => elgg_echo('mechanics:create:annotation:group_topic_post'),
				'events' => [
					'create::object'
				],
				'object_guid_attr' => 'container_guid',
				'attributes' => [
					'type' => 'object',
					'subtype' => 'discussion_reply',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: receiving a reply to a discussion
			 */
			'create:annotation:group_topic_post:reverse' => [
				'title' => elgg_echo('mechanics:create:annotation:group_topic_post:reverse'),
				'events' => [
					'create::object'
				],
				'object_guid_attr' => 'container_guid',
				'subject_guid_attr' => 'container_guid',
				'attributes' => [
					'type' => 'object',
					'subtype' => 'discussion_reply',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: liking something (annotation)
			 */
			'create:annotation:likes' => [
				'title' => elgg_echo('mechanics:create:annotation:likes'),
				'events' => [
					'create::annotation'
				],
				'object_guid_attr' => 'entity_guid',
				'attributes' => [
					'type' => 'annotation',
					'name' => 'likes',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: receiving a like
			 */
			'create:annotation:likes:reverse' => [
				'title' => elgg_echo('mechanics:create:annotation:likes:reverse'),
				'events' => [
					'create::annotation'
				],
				'object_guid_attr' => 'entity_guid',
				'subject_guid_attr' => 'entity_guid',
				'attributes' => [
					'type' => 'annotation',
					'name' => 'likes',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: adding a star rating (annotation)
			 */
			'create:annotation:starrating' => [
				'title' => elgg_echo('mechanics:create:annotation:starrating'),
				'events' => [
					'create::annotation'
				],
				'object_guid_attr' => 'entity_guid',
				'attributes' => [
					'type' => 'annotation',
					'name' => 'starrating',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: receiving a starrating
			 */
			'create:annotation:starrating:reverse' => [
				'title' => elgg_echo('mechanics:create:annotation:starrating:reverse'),
				'events' => [
					'create::annotation'
				],
				'object_guid_attr' => 'entity_guid',
				'subject_guid_attr' => 'entity_guid',
				'attributes' => [
					'type' => 'annotation',
					'name' => 'starrating',
				],
				'settings' => [
					'object_recur_max' => 1,
				]
			],
			/**
			 * Rule: updating a blog post
			 */
			'update:object:blog' => [
				'title' => elgg_echo('mechanics:update:object:blog'),
				'events' => [
					'update::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'blog',
				],
				// override global settings
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: updating a bookmark
			 */
			'update:object:bookmarks' => [
				'title' => elgg_echo('mechanics:update:object:bookmarks'),
				'events' => [
					'update::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'bookmarks',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: updating a page
			 */
			'update:object:page' => [
				'title' => elgg_echo('mechanics:update:object:page'),
				'events' => [
					'update::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'page',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: updating a top-level page
			 */
			'update:object:page_top' => [
				'title' => elgg_echo('mechanics:update:object:page_top'),
				'events' => [
					'update::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'page_top',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: updating a file
			 */
			'update:object:file' => [
				'title' => elgg_echo('mechanics:update:object:file'),
				'events' => [
					'update::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'file',
					//'simletype' => array('image', 'document'),
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: updating a wire post
			 */
			'update:object:thewire' => [
				'title' => elgg_echo('mechanics:update:object:thewire'),
				'events' => [
					'update::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'thewire',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: updating a group discussion topic
			 */
			'update:object:groupforumtopic' => [
				'title' => elgg_echo('mechanics:update:object:groupforumtopic'),
				'events' => [
					'update::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'groupforumtopic',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: update a group
			 */
			'update:group:default' => [
				'title' => elgg_echo('mechanics:update:group:default'),
				'events' => [
					'update::group'
				],
				'attributes' => [
					'type' => 'group',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: update a comment (annotation)
			 */
			'update:annotation:comment' => [
				'title' => elgg_echo('mechanics:update:annotation:comment'),
				'events' => [
					'update::annotation'
				],
				'object_guid_attr' => 'entity_guid',
				'attributes' => [
					'type' => 'annotation',
					'name' => 'generic_comment',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: update a reply to a discussion (annotation)
			 */
			'update:annotation:group_topic_post' => [
				'title' => elgg_echo('mechanics:update:annotation:group_topic_post'),
				'events' => [
					'update::annotation'
				],
				'object_guid_attr' => 'entity_guid',
				'attributes' => [
					'type' => 'annotation',
					'name' => 'group_topic_post',
				],
				'settings' => [
					'object_recur_max' => 0,
				]
			],
			/**
			 * Rule: updating a star rating (annotation)
			 */
			'update:annotation:starrating' => [
				'title' => elgg_echo('mechanics:update:annotation:starrating'),
				'events' => [
					'update::annotation'
				],
				'object_guid_attr' => 'entity_guid',
				'attributes' => [
					'type' => 'annotation',
					'name' => 'starrating',
				],
				'settings' => [
				]
			],
			/**
			 * Rule: logging in
			 */
			'login:user:default' => [
				'title' => elgg_echo('mechanics:login:user:default'),
				'events' => [
					'login::user'
				],
				'attributes' => [
				],
				'settings' => [
					'daily_recur_max' => 1,
				]
			],
			/**
			 * Rule: updating profile
			 */
			'profileupdate:user:default' => [
				'title' => elgg_echo('mechanics:profileupdate:user:default'),
				'events' => [
					'profileupdate::user'
				],
				'attributes' => [
				],
				'settings' => [
					'alltime_recur_max' => 1,
				]
			],
			/**
			 * Rule: completing profile
			 */
			'profilecomplete:user:default' => [
				'title' => elgg_echo('mechanics:profileupdate:user:default'),
				'events' => [
					'profileupdate::user'
				],
				'attributes' => [
				],
				'settings' => [
					'alltime_recur_max' => 1,
				],
				'callbacks' => [
					function (Rule $rule) {
						if (!elgg_is_active_plugin('profile_manager')) {
							return false;
						}

						$completeness = profile_manager_profile_completeness($rule->getSubject());
						if ($completeness['percentage_completeness'] >= 100) {
							return true;
						}

						return false;
					},
				],
			],
			/**
			 * Rule: updating profile avatar
			 */
			'profileiconupdate:user:default' => [
				'title' => elgg_echo('mechanics:profileiconupdate:user:default'),
				'events' => [
					'profileiconupdate::user'
				],
				'attributes' => [
				],
				'settings' => [
				]
			],
			/**
			 * Rule: joining a group
			 */
			'join:group:user' => [
				'title' => elgg_echo('mechanics:join:group:user'),
				'events' => [
					'join::group'
				],
				'attributes' => [
				],
				'settings' => [
					'object_recur_max' => 1
				]
			],
			/**
			 * Rule: leaving a group
			 */
			'leave:group:user' => [
				'title' => elgg_echo('mechanics:leave:group:user'),
				'events' => [
					'leave::group'
				],
				'attributes' => [
				],
				'settings' => [
					'object_recur_max' => 1
				]
			],
			/**
			 * Rule: friending someone
			 */
			'create:relationship:friend' => [
				'title' => elgg_echo('mechanics:create:relationship:friend'),
				'events' => [
					'create::relationship'
				],
				'object_guid_attr' => 'guid_two',
				'subject_guid_attr' => 'guid_one',
				'attributes' => [
					'relationship' => 'friend',
				],
				'settings' => [
					'object_recur_max' => 1
				]
			],
			/**
			 * Rule: being friended by someone
			 */
			'create:relationship:friend:reverse' => [
				'title' => elgg_echo('mechanics:create:relationship:friend:reverse'),
				'events' => [
					'create::relationship'
				],
				'object_guid_attr' => 'guid_one',
				'subject_guid_attr' => 'guid_two',
				'attributes' => [
					'relationship' => 'friend',
				],
				'settings' => [
					'object_recur_max' => 1
				]
			],
			/**
			 * Rule: removing a friend
			 */
			'delete:relationship:friend' => [
				'title' => elgg_echo('mechanics:create:relationship:friend'),
				'events' => [
					'delete::relationship'
				],
				'object_guid_attr' => 'guid_two',
				'subject_guid_attr' => 'guid_one',
				'attributes' => [
					'relationship' => 'friend',
				],
				'settings' => [
					'object_recur_max' => 1
				]
			],
			/**
			 * Rule: wall post
			 */
			'create:object:hjwall' => [
				'title' => elgg_echo('mechanics:create:object:hjwall'),
				'events' => [
					'create::object'
				],
				'attributes' => [
					'type' => 'object',
					'subtype' => 'hjwall',
				],
				// override global settings
				'settings' => [
					'object_recur_max' => 1,
				],
			],
			/**
			 * Rule: receive a wall post
			 */
			'create:object:hjwall:reverse' => [
				'title' => elgg_echo('mechanics:create:object:hjwall:reverse'),
				'events' => [
					'create::object'
				],
				'subject_guid_attr' => 'container_guid', // entity owner will be identified automatically
				'attributes' => [
					'type' => 'object',
					'subtype' => 'hjwall',
				],
				'settings' => [
					'object_recur_max' => 0,
				],
				'callbacks' => [
					function (Rule $rule) {
						if ($rule->getSubject()->guid == $rule->getObject()->container_guid) {
							return false;
						}

						return true;
					},
				],
			],
		];

		$value = $hook->getValue();

		if (is_array($value)) {
			return array_merge_recursive($value, $rules);
		}

		return $rules;
	}

}
