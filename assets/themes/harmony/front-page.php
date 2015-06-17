<?php

    global $loop_counter;
    if(!isset($loop_counter)){$loop_counter=0;}
        add_action('genesis_after_entry','msdlab_add_loop_counter_to_html5_loop',1);
        remove_action( 'genesis_loop', 'genesis_do_loop' );
        add_action( 'genesis_loop', 'msdlab_grid_loop_helper' );
        add_action('genesis_before_entry', 'msdlab_switch_content');
        remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
        add_filter('genesis_grid_loop_post_class', 'msdlab_grid_add_bootstrap');
        
/**
 * Custom blog loop
 */

function msdlab_grid_loop_helper() {
    if ( function_exists( 'genesis_grid_loop' ) ) {//add the image above the entry
                        
        genesis_grid_loop( array(
        'features' => 1,
        'features_on_all'       => false,
        'feature_image_size'    => 0,
        'feature_image_class'   => 0,
        'feature_content_limit' => 0,
        'grid_image_size'       => 'post-image-small',
        'grid_image_class'      => 'alignnone post-image post-image-small',
        'grid_content_limit'    => 0,
        'more' => 0
        ) );
    } else {
        genesis_standard_loop();
    }
}

// Customize Grid Loop Content
function msdlab_switch_content() {
    remove_action('genesis_entry_header','msd_post_image', 20);
    remove_action('genesis_entry_content', 'genesis_grid_loop_content');
    add_action('genesis_entry_content', 'msdlab_grid_loop_content');
    add_action('genesis_after_entry', 'msdlab_grid_divider');
    add_action('genesis_entry_header', 'msdlab_grid_loop_image', 5);
    remove_action( 'genesis_entry_header', 'msdlab_do_post_subtitle', 6 );
    
    if(in_array( 'genesis-feature', get_post_class() )){
        add_action( 'genesis_entry_header','msdlab_grid_header',4);
    } else {
        remove_action( 'genesis_entry_header','msdlab_grid_header',4);
        remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
    }
}

function msdlab_grid_header(){
    print '<h2 class="entry-subtitle">Featured</h2>';
}

function msdlab_grid_loop_content() {

    global $_genesis_loop_args;

    if ( in_array( 'genesis-feature', get_post_class() ) ) {
        if ( $_genesis_loop_args['feature_image_size'] ) {
            printf( '<a href="%s" title="%s" class="featured_image_wrapper">%s</a>', get_permalink(), the_title_attribute('echo=0'), genesis_get_image( array( 'size' => $_genesis_loop_args['feature_image_size'], 'attr' => array( 'class' => esc_attr( $_genesis_loop_args['feature_image_class'] ) ) ) ) );
        }

        the_excerpt();             
    }
    else {

        //the_excerpt();
    }

}

function msdlab_grid_loop_image() {
    if ( in_array( 'genesis-feature', get_post_class() ) ) {
        msd_post_image();
    } elseif ( in_array( 'genesis-grid', get_post_class() ) ) {
        msd_post_image('post-image-small');
    }
}

function msdlab_add_loop_counter_to_html5_loop(){
    global $loop_counter;
    $loop_counter++;
}

function msdlab_grid_divider() {
    global $loop_counter, $paged;
    if($loop_counter == 1 && $paged == 0){print '<h2 class="entry-subtitle">Additional Resources</h2>
    <div class="row">';}
    if($loop_counter == 4 && $paged == 0){print '</div>';}
    
    
}
 function msdlab_grid_add_bootstrap($classes){
     if(in_array('genesis-grid',$classes)){
         $classes[] = 'col-md-4';
     }
     return $classes;
 }
function msdlab_get_comments_number(){
    $num_comments = get_comments_number();
    if ($num_comments == '1') $comments = $num_comments.' ' . __( 'comment', 'adaptation' );
    else $comments = $num_comments.' ' . __( 'comments', 'adaptation' );
    return '<a class="comments" href="'.get_permalink().'/#comments">'.$comments.'</a>';
}
genesis();
