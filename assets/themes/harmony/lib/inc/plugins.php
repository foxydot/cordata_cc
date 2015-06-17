<?php
add_filter('gform_pre_render','walker_do_shortcode_gform_description', 10, 2);

function do_shortcode_gform_description(&$item, $key) {
    $item = do_shortcode($item);
}

function walker_do_shortcode_gform_description($form, $ajax) {
    $form['description'] = do_shortcode($form['description']);
    array_walk_recursive($form['fields'], 'do_shortcode_gform_description');
    
    return $form;
}