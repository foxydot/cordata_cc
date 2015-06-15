<?php
require_once('genesis_tweak_functions.php');

//add_action('pre_get_posts','msdlab_alter_loop_params');
/*** GENERAL ***/
add_theme_support( 'html5' );//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
add_theme_support( 'genesis-responsive-viewport' );//* Add viewport meta tag for mobile browsers
add_theme_support( 'custom-background' );//* Add support for custom background
//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
'header',
'nav',
'subnav',
'footer',
'site-inner'
) );

/*** HEADER ***/
add_action('wp_head','msdlab_add_apple_touch_icons');
add_action('wp_head','msdlab_maybe_wrap_inner');
//add_action('wp_head','msdlab_make_it_homepage');
add_filter( 'genesis_search_text', 'msdlab_search_text' ); //customizes the serach bar placeholder
add_filter('genesis_search_button_text', 'msdlab_search_button'); //customize the search form to add fontawesome search button.
add_action('genesis_after_header','msdlab_pre_header',4);

/**
 * Move secodary nav into pre-header
 */
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action('msdlab_pre_header','msdlab_pre_header_sidebar');
add_action( 'msdlab_pre_header', 'genesis_do_subnav' );

remove_action('genesis_header','genesis_do_header' );
add_action('genesis_header','msdlab_do_header' );

add_action('genesis_header','msdlab_header_right' );

/*** SIDEBARS ***/
add_action('after_setup_theme','msdlab_add_extra_theme_sidebars', 4); //creates widget areas for a hero and flexible widget area
add_filter('widget_text', 'do_shortcode');//shortcodes in widgets

/*** CONTENT ***/
add_filter('genesis_breadcrumb_args', 'msdlab_breadcrumb_args'); //customize the breadcrumb output
remove_action('genesis_before_loop', 'genesis_do_breadcrumbs'); //move the breadcrumbs 
add_action('genesis_before_loop', 'msdlab_do_category_header'); //move the breadcrumbs 
add_filter( 'genesis_post_info', 'msdlab_post_info_filter' );

add_action('genesis_after_header', 'genesis_do_breadcrumbs'); //to outside of the loop area
add_action('genesis_before_entry','msd_post_image');//add the image above the entry

add_filter('excerpt_more', 'sp_read_more_link');
add_filter( 'the_content_more_link', 'sp_read_more_link' );

//remove_action( 'genesis_before_post_content', 'genesis_post_info', 12 ); //remove the info (date, posted by,etc.)
remove_action( 'genesis_after_post_content', 'genesis_post_meta' ); //remove the meta (filed under, tags, etc.)
//remove_action( 'genesis_entry_header', 'genesis_post_info', 12 ); //remove the info (date, posted by,etc.)
remove_action( 'genesis_entry_footer', 'genesis_post_meta'); //remove the meta (filed under, tags, etc.)

add_action( 'genesis_before_post', 'msdlab_post_image', 8 ); //add feature image across top of content on *pages*.

remove_action( 'genesis_after_endwhile', 'genesis_prev_next_post_nav' );
add_action( 'genesis_after_endwhile', 'msdlab_prev_next_post_nav' );
/*** FOOTER ***/
add_theme_support( 'genesis-footer-widgets', 1 ); //adds automatic footer widgets

add_action('genesis_after_loop','msdlab_do_footer_widget', 1);

remove_action('genesis_footer','genesis_do_footer'); //replace the footer
add_action('genesis_footer','msdlab_do_social_footer');//with a msdsocial support one


/*** SITEMAP ***/
add_action('after_404','msdlab_sitemap');

/*** Blog Header ***/
add_action('wp_head', 'msdlab_custom_hooks_management');