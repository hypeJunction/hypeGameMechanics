<?php

$widget = $vars['entity'];
$user = $widget->getOwnerEntity();

echo elgg_view('framework/mechanics/user_badges', array(
	'user' => $user
));