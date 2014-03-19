<?php

namespace hypeJunction\GameMechanics;

$nt = elgg_get_plugin_setting('allow_negative_total');
if ($nt == 'allow') {
	elgg_set_plugin_setting('allow_negative_total', true);
} else {
	elgg_set_plugin_setting('allow_negative_total', false);
}