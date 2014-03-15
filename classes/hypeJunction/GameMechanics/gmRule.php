<?php

namespace hypeJunction\GameMechanics;

use ElggObject;
use ElggUser;
use stdClass;

class gmRule {

	/**
	 * Unique name of the rule
	 * @var string
	 */
	protected $name;

	/**
	 * Elgg event that invoked the rule
	 * @var string "$event::$type"
	 */
	protected $event;

	/**
	 * Object of the rule
	 * @var object ElggEntity|ElggRelationship|ElggAnnotation|ElggMetadata
	 */
	protected $object;

	/**
	 * Description of the rule
	 * @var array
	 */
	protected $options;

	/**
	 * Number of positive or negative points if all conditions for this rule are met
	 * @var integer 
	 */
	protected $score;

	/**
	 * Error messages
	 * @var array
	 */
	protected $errors;

	/**
	 * Messages
	 * @var array
	 */
	protected $messages;

	/**
	 * Log
	 * @var array
	 */
	protected $log;

	/**
	 * Flag to terminate events early
	 * @var type
	 */
	protected $terminate;

	/**
	 * Subject of the rule (logged in user)
	 * @var ElggUser
	 */
	protected static $subject;

	/**
	 * Cache of current totals for the user
	 * @var object
	 */
	protected static $totals;

	/**
	 * Plugin settings cache
	 * @var object
	 */
	protected static $settings;

	/**
	 * Cache to prevent nested actions from creating multiple scores
	 * @var array
	 */
	protected static $eventThrottle;

	/**
	 * Create a new instance
	 * @param type $name	Unique name of the rule
	 * @param type $user	Subject
	 */
	function __construct($name = '', $user = null) {
		if (!$user) {
			$user = elgg_get_logged_in_user_entity();
		}

		self::getSettings();
		self::setSubject($user);

		if ($name) {
			$this->setName($name);
		}
	}

	/**
	 * Set the rule name
	 * @param string $name Unique name identifying the action
	 * @return string
	 */
	public function setName($name) {
		$this->name = $name;
		return $this->getName();
	}

	public function getName() {
		return $this->name;
	}

	public function getScore() {
		if (!$this->getName()) {
			return 0;
		}
		if (!isset($this->score)) {
			$this->score = elgg_get_plugin_setting($this->getName(), PLUGIN_ID);
		}
		return $this->score;
	}

	public function setObject($entity) {
		$this->object = $entity;
		return $this->getObject();
	}

	public function getObject() {
		return $this->object;
	}

	public function setEvent($event = '') {
		$this->event = $event;
		return $this->getEvent();
	}

	public function getEvent() {
		return $this->event;
	}

	/**
	 * Set rule options
	 *
	 * @param array $options
	 * @uses $options['title']		Friendly title
	 * @uses $options['events']		Elgg events this rule applies
	 * @uses $options['attributes']	Attributes and metadata to validate
	 * @uses $options['settings']	Settings to override global throttling settings
	 * @uses $options['callbacks']	Custom callback functions to validate the applicability of the rule
	 * @return array
	 */
	public function setOptions($options) {
		$this->options = $options;
		return $this->options;
	}

	public function getOptions($key = '') {
		return ($key) ? elgg_extract($key, $this->options, array()) : $this->options;
	}

	/**
	 * Check applicability of the rule conditions to the and distribute points as appropriate
	 * @return void
	 */
	public function applyRule() {

		$name = $this->getName();
		$event = $this->event;

		$score = $this->getScore();
		$user = $this->getSubject();
		$object = $this->getObject();

		$object_type = $object->getType();
		$object_id = (isset($object->guid)) ? $object->guid : $object->id;

		$this->setLog("Apply rule '$name' on '$event' to $object_type with id $object_id");

		$hash = md5("$name:$object_type:$object_id");

		$events = $this->getOptions('events');
		if (!is_array($events)) {
			$events = (array) $events;
		}

		// Check if current event applies to the rule
		if (!$name || !in_array($event, $events)) {
			$this->setLog("Event $event is not in the scope of this rule; quitting");
			return;
		}

		if (!isset(self::$eventThrottle)) {
			self::$eventThrottle = array();
		}

		if (in_array($hash, self::$eventThrottle)) {
			$this->setLog("Rule has already been applied; quitting");
			return;
		}
		self::$eventThrottle[] = $hash;

		// Check throttling conditions
		if (!$this->validateThrottlingConditions()) {
			$this->setLog("Rule has been throttled; quitting");
			return;
		}

		// Validate object attributes and metadata
		if (!$this->validateAttributes()) {
			$this->setLog("Attributes can't validate; quitting");
			return;
		}

		// Validate custom conditions by calling callback functions
		if (!$this->validateCallbackConditions()) {
			$this->setLog("Callback validation failed; quitting");
			return;
		}

		// Validate that the score is not negative, or that we can proceed
		if (!$this->validateNegativeScore()) {
			$this->setLog("Negative score not allowed; quitting");
			return;
		}

		// Add points and create a historical reference
		$id = create_annotation($user->guid, "gm_score", $score, '', $user->guid, ACCESS_PUBLIC);
		if ($id) {
			$history = new ElggObject();
			$history->subtype = 'gm_score_history';
			$history->owner_guid = $user->guid;
			$history->container_guid = $user->guid;
			$history->access_id = ACCESS_PRIVATE;
			$history->annotation_name = 'gm_score_history';
			$history->annotation_value = $score;
			$history->annotation_id = $id;

			$history->rule = $name;

			$history->event = $this->event;
			$history->object_type = $object->getType();
			$history->object_id = (isset($object->guid)) ? $object->guid : $object->id;
			$history->object_ref = "{$history->object_type}:{$history->object_id}";

			$history->save();

			$this->setLog("$score points applied");
		}

		if ($id && $user->guid == elgg_get_logged_in_user_guid()) {
			$rule_rel = elgg_echo("mechanics:{$name}");
			$reason = elgg_echo('mechanics:score:earned:reason', array(strtolower($rule_rel)));
			if ($score > 0) {
				$this->setMessage(elgg_echo('mechanics:score:earned:for', array($score, $reason)));
			} else {
				$this->setMessage(elgg_echo('mechanics:score:lost:for', array($score, $reason)));
			}
		}
	}

	public function validateAttributes() {

		$object = $this->getObject();
		$attributes = $this->getOptions('attributes');
		if (is_array($attributes)) {
			foreach ($attributes as $attribute => $expected_value) {
				switch ($attribute) {
					case 'type' :
						$value = $object->getType();
						break;

					case 'subtype' :
						$value = $object->getSubtype();
						break;

					default :
						$value = $object->$attribute;
						break;
				}

				if (is_array($expected_value)) {
					$result = in_array($value, $expected_value);
				} else {
					$result = ($value == $expected_value);
				}

				if ($result === false) {
					return false;
				}
			}
		}

		return true;
	}

	public function validateCallbackConditions() {

		$callbacks = $this->getOptions('callbacks');
		if (is_array($callbacks)) {
			foreach ($callbacks as $callback) {
				if (is_callable($callback)) {
					$result = call_user_func($callback, $this);
					if (!$result) {
						return false;
					}
				}
			}
		}

		return true;
	}

	public function validateValidators() {


		$user = $this->getSubject();
		$object = $this->getObject();

		$validators = $this->getOptions('validators');
		if (is_array($validators)) {
			foreach ($validators as $validator) {
				switch ($validator['type']) {

					default :
					case 'attribute' :
					case 'metadata' :

						switch ($validator['comparison']) {

							case 'et' :
							case 'equals' :
							case '=' :
								$name = $validator['name'];
								$value = $validator['value'];
								return ($object->$name == $value);
						}

						break;

					case 'annotation' :

						break;

					case 'relationship' :
						break;
				}
			}
		}
		return true;
	}

	public function validateThrottlingConditions() {

		$name = $this->getName();
		$score = $this->getScore();
		$totals = $this->calculateTotals();
		$action_totals = $totals->actions[$name];

		$daily_max = $this->getSetting('daily_max');
		if ($daily_max && $action_totals->daily_total + $score > $daily_max) {
			$this->setLog("Daily max exceeded");
			return false;
		}


		$daily_action_max = $this->getSetting('daily_action_max');
		if ($daily_action_max && $action_totals->daily_action_total + $score > $daily_action_max) {
			$this->setLog("Daily action max exceeded");
			return false;
		}

		$alltime_action_max = $this->getSetting('alltime_action_max');
		if ($alltime_action_max && $action_totals->alltime_action_total + $score > $alltime_action_max) {
			$this->setLog("All time max for this action exceeded");
			return false;
		}

		$daily_recur_max = $this->getSetting('daily_recur_max');
		if ($daily_recur_max && $action_totals->daily_recur_total + 1 > $daily_recur_max) {
			$this->setLog("Daily recurrences for this action exceeded");
			return false;
		}

		$alltime_recur_max = $this->getSetting('alltime_recur_max');
		if ($alltime_recur_max && $action_totals->alltime_recur_total + 1 > $alltime_recur_max) {
			$this->setLog("All time recurrences for this action exceeded");
			return false;
		}
		
		$action_object_max = $this->getSetting('action_object_max');
		if ($action_object_max && $action_totals->action_object_total + $score > $action_object_max) {
			$this->setLog("Action max for this object exceeded");
			return false;
		}

		$daily_object_max = $this->getSetting('daily_object_max');
		if ($daily_object_max && $action_totals->daily_object_total + $score > $daily_object_max) {
			$this->setLog("Daily max for this object exceeded");
			return false;
		}

		$alltime_object_max = $this->getSetting('alltime_object_max');
		if ($alltime_object_max && $action_totals->alltime_object_total + $score > $alltime_object_max) {
			$this->setLog("All time max for this object exceeded");
			return false;
		}

		return true;
	}

	public function validateNegativeScore() {

		$name = $this->getName();
		$score = $this->getScore();
		$settings = self::getSettings();
		$totals = self::calculateTotals($name);

		if ($totals->alltime_total + $score < 0) {
			if ($settings->allow_negative_total != 'allow') {
				$this->setError(elgg_echo('mechanics:negativereached'));
				$this->terminate = true; // Terminate and prevent event from completing
				return false;
			}
		}

		return true;
	}

	private function setError($error) {
		if (!isset($this->errors)) {
			$this->errors = array();
		}
		$this->errors[] = $error;
	}

	public function getErrors() {
		return (count($this->errors)) ? $this->errors : false;
	}

	private function setMessage($message) {
		if (!isset($this->messages)) {
			$this->messages = array();
		}
		$this->messages[] = $message;
	}

	public function getMessages() {
		return (count($this->messages)) ? $this->messages : false;
	}

	private function setLog($error) {
		if (!isset($this->log)) {
			$this->log = array();
		}
		$this->log[] = $error;
	}

	public function getLog() {
		return (count($this->log)) ? $this->log : false;
	}

	public function terminateEvent() {
		return ($this->terminate === true);
	}

	public static function getSubject() {
		return self::$subject;
	}

	protected static function setSubject($user = null) {
		if (isset(self::$subject)) {
			return self::$subject;
		}

		if (!elgg_instanceof($user)) {
			$user = elgg_get_logged_in_user_entity();
		}
		self::$subject = $user;
		return self::$subject;
	}

	private static function getSettings() {

		if (isset(self::$settings)) {
			return self::$settings;
		}

		$settings = new stdClass();

		// Total number of points a user can collect per day
		$settings->daily_max = (int) elgg_get_plugin_setting('daily_max', PLUGIN_ID);

		// Total number of points a user can collect per action per day
		$settings->daily_action_max = (int) elgg_get_plugin_setting('daily_action_max', PLUGIN_ID);

		// Total number of points a user can collect for a given action
		$settings->alltime_action_max = (int) elgg_get_plugin_setting('alltime_action_max', PLUGIN_ID);

		// A number of recurring times that points can be collected for an action per day
		$settings->daily_recur_max = (int) elgg_get_plugin_setting('daily_recur_max', PLUGIN_ID);

		// A number of recurring times that points can be collected for a given action
		$settings->alltime_recur_max = (int) elgg_get_plugin_setting('alltime_recur_max', PLUGIN_ID);

		// A cumulative number of points that can be collected on an object per day
		$settings->daily_object_max = (int) elgg_get_plugin_setting('daily_object_max', PLUGIN_ID);

		// A cumulative number of points that can be collected on an object
		$settings->alltime_object_max = (int) elgg_get_plugin_setting('alltime_object_max', PLUGIN_ID);

		// A number of points that can be collected on an object by a single action
		$settings->action_object_max = (int) elgg_get_plugin_setting('action_object_max', PLUGIN_ID);

		// Whether an action should be allowed to propagate if the number of points to become negative
		$settings->allow_negative_total = (int) elgg_get_plugin_setting('allow_negative_total', PLUGIN_ID);

		self::$settings = $settings;

		return self::$settings;
	}

	public function getSetting($setting_name) {

		$global = self::getSettings();
		$settings = $this->getOptions('settings');

		if (isset($settings[$setting_name])) {
			return $settings[$setting_name];
		}
		return $global->$setting_name;
	}

	private function calculateTotals() {

		$name = $this->getName();

		$totals = (isset(self::$totals)) ? self::$totals : new stdClass();

		if (!isset($totals->alltime_total)) {
			$totals->alltime_total = self::getUserTotal();
		}
		if (!isset($totals->daily_total)) {
			$end = time();
			$totals->daily_total = self::getUserTotal($end - 86400, $end);
		}

		if (!isset($totals->actions)) {
			$totals->actions = array();
		}

		if (!isset($totals->actions[$name])) {
			$action_totals = new stdClass();
			$end = time();

			$action_totals->daily_action_total = $this->getUserActionTotal($name, $end - 86400, $end);
			$action_totals->alltime_action_total = $this->getUserActionTotal($name);

			$action_totals->daily_recur_total = $this->getUserRecurTotal($name, $end - 86400, $end);
			$action_totals->alltime_recur_total = $this->getUserRecurTotal($name);

			$action_totals->action_object_total = $this->getObjectTotal($name);
			$action_totals->daily_object_total = $this->getObjectTotal(null, $end - 86400, $end);
			$action_totals->alltime_object_total = $this->getObjectTotal(null);

			$totals->actions[$name] = $action_totals;
		}

		self::$totals = $totals;
		return self::$totals;
	}

	public function getUserTotal($time_lower = null, $time_upper = null) {
		return get_user_score($this->getSubject(), $time_lower, $time_upper);
	}

	public function getUserActionTotal($name, $time_lower = null, $time_upper = null) {
		return get_user_action_total($this->getSubject(), $name, $time_lower, $time_upper);
	}

	public function getUserRecurTotal($name, $time_lower = null, $time_upper = null) {
		return get_user_recur_total($this->getSubject(), $name, $time_lower, $time_upper);
	}

	public function getObjectTotal($name = null, $time_lower = null, $time_upper = null) {
		return get_object_total($this->getObject(), $this->getSubject(), $name, $time_lower, $time_upper);
	}

}
