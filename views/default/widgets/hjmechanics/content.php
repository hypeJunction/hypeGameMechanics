<?php

$widget = $vars['entity'];
$user = $widget->getOwnerEntity();

echo elgg_view('hj/mechanics/user_badges', array(
	'user' => $user
));