<?php

$nt = elgg_get_plugin_setting('allow_negative_total', 'hypeGameMechanics');
if (!isset($nt)) {
	elgg_set_plugin_setting('allow_negative_total', false, 'hypeGameMechanics');
}