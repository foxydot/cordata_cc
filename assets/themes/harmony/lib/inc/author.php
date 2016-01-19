<?php

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

add_shortcode('post_author_thumbnail','msdlab_post_author_thumbnail');
add_shortcode('post_author_position','msdlab_post_author_position');
add_shortcode('post_author_title','msdlab_post_author_position');
add_shortcode('list_authors','msdlab_list_post_authors');


function msdlab_post_author_thumbnail($atts){
    $avatar     = get_the_author_meta( 'profile_img', (int) get_query_var( 'author' ) );
    $avatar_id  = $avatar ? get_attachment_id_from_src($avatar) : FALSE;
    $image      = wp_get_attachment_image_src( $avatar_id, 'tiny-post-thumb' );
    $avatar     = $avatar_id ? '<img src="'.$image[0].'" class="alignleft pull-left" />' : '';

    return $avatar;
}

function msdlab_post_author_position($atts){
    $position   = get_the_author_meta( 'position', (int) get_query_var( 'author' ) );
    $company    = get_the_author_meta( 'company', (int) get_query_var( 'author' ) );
    $position   = $position ? sprintf( '<span class="author-position">%s</span>', strip_tags( $position ) ) : FALSE;
    $company    = $company ? sprintf( '<span class="author-company">%s</span>', strip_tags( $company ) ) : FALSE;
    if($position && $company){
        $position   = sprintf( ', <span class="author-job">%s, %s</span>', $position, $company );
    } elseif ($position || $company){
        $position   = sprintf( ', <span class="author-job">%s%s</span>', $position, $company );
    } else {
        $position   = '';
    }
    return $position;
}

function msdlab_list_post_authors($atts){
    $atts = shortcode_atts( array(
        'exclude' => array(),
        'hide_empty' => true,
        'style' => 'list',
    ), $atts );
    $atts['exclude'] = array_merge($atts['exclude'],array('msd_lab','abby','cole'));
    foreach($atts['exclude'] AS $name){
        $user = get_user_by('login',$name);
        $exclude_ids[] = $user->ID;
    }
    $exclude = implode(',',$exclude_ids);
    $args = array(
    'hide_empty'    => $atts['hide_empty'],
    'echo'          => false,
    'style'         => $atts['style'],
    'exclude'       => $exclude
    );
    
    return msdlab_list_authors($args);
}


/**
 * List all the authors of the blog, with several options available.
 *
 * @link https://codex.wordpress.org/Template_Tags/wp_list_authors
 *
 * @since 1.2.0
 *
 * @param string|array $args {
 *     Optional. Array or string of default arguments.
 *
 *     @type string $orderby       How to sort the authors. Accepts 'nicename', 'email', 'url', 'registered',
 *                                 'user_nicename', 'user_email', 'user_url', 'user_registered', 'name',
 *                                 'display_name', 'post_count', 'ID', 'meta_value', 'user_login'. Default 'name'.
 *     @type string $order         Sorting direction for $orderby. Accepts 'ASC', 'DESC'. Default 'ASC'.
 *     @type int    $number        Maximum authors to return or display. Default empty (all authors).
 *     @type bool   $optioncount   Show the count in parenthesis next to the author's name. Default false.
 *     @type bool   $exclude_admin Whether to exclude the 'admin' account, if it exists. Default false.
 *     @type bool   $show_fullname Whether to show the author's full name. Default false.
 *     @type bool   $hide_empty    Whether to hide any authors with no posts. Default true.
 *     @type string $feed          If not empty, show a link to the author's feed and use this text as the alt
 *                                 parameter of the link. Default empty.
 *     @type string $feed_image    If not empty, show a link to the author's feed and use this image URL as
 *                                 clickable anchor. Default empty.
 *     @type string $feed_type     The feed type to link to, such as 'rss2'. Defaults to default feed type.
 *     @type bool   $echo          Whether to output the result or instead return it. Default true.
 *     @type string $style         If 'list', each author is wrapped in an `<li>` element, otherwise the authors
 *                                 will be separated by commas.
 *     @type bool   $html          Whether to list the items in HTML form or plaintext. Default true.
 *     @type string $exclude       An array, comma-, or space-separated list of author IDs to exclude. Default empty.
 *     @type string $exclude       An array, comma-, or space-separated list of author IDs to include. Default empty.
 * }
 * @return null|string The output, if echo is set to false. Otherwise null.
 */
function msdlab_list_authors( $args = '' ) {
    global $wpdb;

    $defaults = array(
        'orderby' => 'name', 'order' => 'ASC', 'number' => '',
        'optioncount' => false, 'exclude_admin' => true,
        'show_fullname' => false, 'hide_empty' => true,
        'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
        'style' => 'list', 'html' => true, 'exclude' => '', 'include' => '',
        'avatar' => true, 'position' => true, 'company' => true, 'bio' => 'excerpt'
    );

    $args = wp_parse_args( $args, $defaults );

    $return = '';

    $query_args = wp_array_slice_assoc( $args, array( 'orderby', 'order', 'number', 'exclude', 'include' ) );
    $query_args['fields'] = 'ids';
    $authors = get_users( $query_args );

    $author_count = array();
    foreach ( (array) $wpdb->get_results( "SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author" ) as $row ) {
        $author_count[$row->post_author] = $row->count;
    }
    foreach ( $authors as $author_id ) {
        $author     = get_userdata( $author_id );
        $headline   = get_the_author_meta( 'headline', $author_id );
        $url        = get_the_author_meta( 'url', $author_id );
        $intro_text = get_the_author_meta( 'intro_text', $author_id );
        $bio        = get_the_author_meta( 'description', $author_id );

        if ( $args['exclude_admin'] && 'admin' == $author->display_name ) {
            continue;
        }

        $posts = isset( $author_count[$author->ID] ) ? $author_count[$author->ID] : 0;

        if ( ! $posts && $args['hide_empty'] ) {
            continue;
        }
        
        if ( $args['show_fullname'] && $author->first_name && $author->last_name ) {
            $name = "$author->first_name $author->last_name";
        } else {
            $name = $author->display_name;
        }

        if ( ! $args['html'] ) {
            $return .= $name . ', ';

            continue; // No need to go further to process HTML.
        }

        if ( 'list' == $args['style'] ) {
            $return .= '<li>';
        }
        
        if ( $args['avatar'] ){
            $avatar     = get_the_author_meta( 'profile_img', $author_id );
            $avatar_id  = $avatar ? get_attachment_id_from_src($avatar) : FALSE;
            $image      = wp_get_attachment_image_src( $avatar_id, 'tiny-post-thumb' );
            $avatar     = $avatar_id ? '<img src="'.$image[0].'" class="alignleft pull-left" />' : '';
            $return .= $avatar;
        }
        
        $link = '<a href="' . get_author_posts_url( $author->ID, $author->user_nicename ) . '" title="' . esc_attr( sprintf(__("Posts by %s"), $author->display_name) ) . '">' . $name . '</a>';

        if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
            $link .= ' ';
            if ( empty( $args['feed_image'] ) ) {
                $link .= '(';
            }

            $link .= '<a href="' . get_author_feed_link( $author->ID, $args['feed_type'] ) . '"';

            $alt = '';
            if ( ! empty( $args['feed'] ) ) {
                $alt = ' alt="' . esc_attr( $args['feed'] ) . '"';
                $name = $args['feed'];
            }

            $link .= '>';

            if ( ! empty( $args['feed_image'] ) ) {
                $link .= '<img src="' . esc_url( $args['feed_image'] ) . '" style="border: none;"' . $alt . ' />';
            } else {
                $link .= $name;
            }

            $link .= '</a>';

            if ( empty( $args['feed_image'] ) ) {
                $link .= ')';
            }
        }

        if ( $args['optioncount'] ) {
            $link .= ' ('. $posts . ')';
        }

        $return .= $link;
        
        if($args['position'] || $args['company']){
            $position   = $args['position']?get_the_author_meta( 'position', $author_id ):false;
            $company    = $args['company']?get_the_author_meta( 'company', $author_id ):false;
            if($position && $company){
                $return .= sprintf( '<div class="author-job">%s, %s</div>', $position, $company );
            } elseif ($position || $company){
                $return .= sprintf( '<div class="author-job">%s%s</div>', $position, $company );
            }
        }
        
        $return .= ( 'list' == $args['style'] ) ? '</li>' : ', ';
    }

    $return = rtrim( $return, ', ' );
    
    if ( 'list' == $args['style'] ) {
            $return = sprintf('<ul class="author-list">%s</ul>', $return);
        }

    if ( ! $args['echo'] ) {
        return $return;
    }
    echo $return;
}

add_shortcode('author_posts','msdlab_get_the_author_posts');
function msdlab_get_the_author_posts($atts){
    $current_author = get_query_var('author');
    $author_posts=  get_posts( 'author='.$current_author );
    if($author_posts){
        foreach ($author_posts as $author_post){
         $this_post = '';
         if(has_post_thumbnail( $author_post->ID )){
             $size = has_image_size('wp_review_small')?'wp_review_small':'tiny-post-thumb';
             $this_post .= '<div class="wpt_thumbnail wpt_thumb_small"> 
                 <a href="'.get_permalink($author_post->ID).'" class="entry-title" title="'.$author_post->post_title.'">    
                 '.get_the_post_thumbnail( $author_post->ID, $size, array('class' => 'alignleft pull-left') ).'
                 </a>
                 </div>';
         }
         $this_post .= '<div class="entry-title"><a href="'.get_permalink($author_post->ID).'" class="entry-title" title="'.$author_post->post_title.'">'.$author_post->post_title.'</a></div>';
         $this_post .= '<div class="wpt-postmeta post-info">Posted '.msdlab_post_date($author_post->ID).'</div>
            <div class="clear"></div>  ';
         $this_post = sprintf('<li>%s</li>', $this_post);
         $list .= $this_post;
        }
        $list = sprintf('<div class="wpt_widget_content"><ul class="tab-content">%s</ul></div>',$list);
        return $list;    
    }
    return "There are no posts by this contributor.";
}

/**
 * Produces the date of post publication.
 *
 * Supported shortcode attributes are:
 *   after (output after link, default is empty string),
 *   before (output before link, default is empty string),
 *   format (date format, default is value in date_format option field),
 *   label (text following 'before' output, but before date).
 *
 * Output passes through 'genesis_post_date_shortcode' filter before returning.
 *
 * @since 1.1.0
 *
 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
 * @return string Shortcode output
 */
function msdlab_post_date( $post_id = false, $atts = array() ) {
    global $post;
    $post_id = $post_id?$post_id:$post->ID;
    $defaults = array(
        'after'  => '',
        'before' => '',
        'format' => get_option( 'date_format' ),
        'label'  => '',
    );

    $atts = shortcode_atts( $defaults, $atts, 'post_date' );

    $display = ( 'relative' === $atts['format'] ) ? genesis_human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'genesis' ) : get_the_time( $atts['format'], $post_id );

    if ( genesis_html5() )
        $output = sprintf( '<time %s>', genesis_attr( 'entry-time' ) ) . $atts['before'] . $atts['label'] . $display . $atts['after'] . '</time>';
    else
        $output = sprintf( '<span class="date published time" title="%5$s">%1$s%3$s%4$s%2$s</span> ', $atts['before'], $atts['after'], $atts['label'], $display, get_the_time( 'c' ) );

    return apply_filters( 'genesis_post_date_shortcode', $output, $atts );

}
