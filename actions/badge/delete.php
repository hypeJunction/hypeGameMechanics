<?php

namespace hypeJunction\GameMechanics;

$guid = get_input('guid');
$entity = get_entity($guid);

if (elgg_instanceof($entity, 'object', HYPEGAMEMECHANICS_BADGE_SUBTYPE) && $entity->delete()) {
	system_message(elgg_echo('mechanics:badge:delete:success'));
} else {
	register_error(elgg_echo('mechanics:badge:delete:error'));
}

forward(REFERER);
