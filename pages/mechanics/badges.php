<?php


elgg_load_js('hj.framework.ajax');

elgg_load_js('hj.mechanics.base');
elgg_load_css('hj.mechanics.base');

elgg_load_js('hj.framework.relationshiptags');
elgg_load_css('hj.framework.jquitheme');

$page = elgg_view('hj/mechanics/badges');

$page = elgg_view_layout('one_sidebar', array(
    'content' => $page
));

echo elgg_view_page(elgg_echo('hj:mechanics:badges:site'), $page);
