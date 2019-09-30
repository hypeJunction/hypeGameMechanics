<?php

return [
	'bootstrap' => \hypeJunction\GameMechanics\Bootstrap::class,

	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'hjbadge',
			'class' => \hypeJunction\GameMechanics\Badge::class,
		],
		[
			'type' => 'object',
			'subtype' => 'badge_rule',
			'class' => \hypeJunction\GameMechanics\BadgeRule::class,
		],
		[
			'type' => 'object',
			'subtype' => 'gm_score_history',
			'class' => \hypeJunction\GameMechanics\Score::class,
		],
	],

	'routes' => [
		'points' => [
			'path' => '/points',
			'resource' => 'points/leaderboard',
		],

		'points:leaderboard' => [
			'path' => '/points/leaderboard',
			'resource' => 'points/leaderboard',
		],

		'collection:object:hjbadge:default' => [
			'path' => '/points/all',
			'resource' => 'points/badges',
		],

		'collection:object:hjbadge:all' => [
			'path' => '/points/badges',
			'resource' => 'points/badges',
		],

		'add:object:hjbadge' => [
			'path' => '/points/badge/add',
			'resource' => 'points/badge/add',
			'middleware' => [
				\Elgg\Router\Middleware\AdminGatekeeper::class,
			],
		],

		'edit:object:hjbadge' => [
			'path' => '/points/badge/edit/{guid}',
			'resource' => 'points/badge/edit',
			'middleware' => [
				\Elgg\Router\Middleware\AdminGatekeeper::class,
			],
		],

		'view:object:hjbadge' => [
			'path' => '/points/badge/view/{guid}/{title?}',
			'resource' => 'points/badge/view',
		],

		'award:object:gm_score_history' => [
			'path' => '/points/award/{guid}',
			'resource' => 'points/award',
			'middleware' => [
				\Elgg\Router\Middleware\AdminGatekeeper::class,
			]
		],

		'collection:object:gm_score_history:owner' => [
			'path' => '/points/owner/{username}',
			'resource' => 'points/owner',
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
			],
		],

		'collection:object:gm_score_history:history' => [
			'path' => '/points/history/{username}',
			'resource' => 'points/history',
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
			],
		],
	],

	'actions' => [
		'badge/claim' => [],
		'badge/edit' => [
			'access' => 'admin',
		],
		'badge/delete' => [
			'access' => 'admin',
		],
		'badge/order' => [
			'access' => 'admin',
		],
		'points/award' => [],
		'points/reset' => [
			'access' => 'admin',
		],
	],

	'hooks' => [
		'entity:icon:file' => [
			'object' => [
				\hypeJunction\GameMechanics\SetIconFile::class,
			],
		]
	]
];