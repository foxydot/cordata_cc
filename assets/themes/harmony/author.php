<?php
/*
Template Name: Author Profile Page
*/

remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
add_action( 'genesis_before_loop', 'msdlab_do_author_title_description', 15 );

add_filter( 'msdlab_author_description_output', 'wpautop' );

remove_all_actions('genesis_loop');
genesis();