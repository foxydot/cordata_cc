<?php
/*
Template Name: Author Profile Page
*/

remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
add_action( 'genesis_before_loop', 'msdlab_do_author_title_description', 15 );

add_filter( 'msdlab_author_description_output', 'wpautop' );
/**
 * Add custom headline and description to author archive pages.
 *
 * If we're not on an author archive page, or not on page 1, then nothing extra is displayed.
 *
 * If there's a custom headline to display, it is marked up as a level 1 heading.
 *
 * If there's a description (intro text) to display, it is run through `wpautop()` before being added to a div.
 *
 * @since 1.4.0
 *
 * @return null Return early if not author archive or not page one.
 */
function msdlab_do_author_title_description() {

    if ( ! is_author() )
        return;

    if ( get_query_var( 'paged' ) >= 2 )
        return;

    $avatar     = get_the_author_meta( 'profile_img', (int) get_query_var( 'author' ) );
    $name       = get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) );
    $first_name = get_the_author_meta( 'first_name', (int) get_query_var( 'author' ) );
    $headline   = get_the_author_meta( 'headline', (int) get_query_var( 'author' ) );
    $position   = get_the_author_meta( 'position', (int) get_query_var( 'author' ) );
    $company    = get_the_author_meta( 'company', (int) get_query_var( 'author' ) );
    $url        = get_the_author_meta( 'url', (int) get_query_var( 'author' ) );
    $intro_text = get_the_author_meta( 'intro_text', (int) get_query_var( 'author' ) );
    $bio        = get_the_author_meta( 'description', (int) get_query_var( 'author' ) );
    
    
    $avatar_id  = $avatar ? get_attachment_id_from_src($avatar) : FALSE;
    $image      = wp_get_attachment_image_src( $avatar_id, 'author' );
    $avatar     = $avatar_id ? '<img src="'.$image[0].'" class="alignleft pull-left" />' : '';
    $name       = $name ? sprintf( '<h1 class="archive-title author-name">%s</h1>', strip_tags( $name ) ) : '';
    $first_name = $first_name ? sprintf( '<h3 class="author-bio-header">About %s</h1>', strip_tags( $first_name ) ) : '';
    $headline   = $headline ? sprintf( '<h1 class="archive-title author-name">%s</h1>', strip_tags( $headline ) ) : '';
    $position   = $position ? sprintf( '<span class="author-position">%s</span>', strip_tags( $position ) ) : FALSE;
    $company    = $company ? sprintf( '<span class="author-company">%s</span>', strip_tags( $company ) ) : FALSE;
    if($position && $company){
        $position   = sprintf( '<h3 class="author-job">%s, %s</h3>', $position, $company );
    } elseif ($position || $company){
        $position   = sprintf( '<h3 class="author-job">%s%s</h3>', $position, $company );
    } else {
        $position   = '';
    }
    $url        = sprintf( '<div class="author-url"><a href="%s" target="_blank">%s</a></div>', $url, $url );
    $intro_text = $intro_text ? '<div class="author-intro-text">' . apply_filters( 'genesis_author_intro_text_output', $intro_text ) . '</div>' : FALSE;
    $bio        = $bio ? '<div class="author-bio-text">' . apply_filters( 'msdlab_author_description_output', $bio ) . '</div>' : FALSE;
    if($intro_text || $bio){
        $bio   = sprintf( '<div class="author-bio-area">%s%s%s</h3>', $first_name, $intro_text, $bio );
    } else {
        $bio   = '';
    }

    print $avatar.$name.$headline.$position.$url.$bio;

}

remove_all_actions('genesis_loop');
genesis();