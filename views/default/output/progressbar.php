<?php

elgg_load_js('hj.mechanics.base');
elgg_load_css('hj.mechanics.base');
elgg_load_css('hj.framework.jquitheme');

$value = elgg_extract('value', $vars, 0);

echo "<div class=\"hj-mechanics-progressbar\" data=\"$value\"></div>";
