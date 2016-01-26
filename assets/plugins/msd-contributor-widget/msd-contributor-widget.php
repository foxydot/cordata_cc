<?php
/*
Plugin Name: MSDLab Contributor Widget
Description: A simple widget to display a random contributor.
Author: MSDLab
Version: 1.0
*/

// Block direct requests
if ( !defined('ABSPATH') )
    die('-1');

// Load the widget on widgets_init
function msdlab_load_contributor_widget() {
    register_widget('MSDLab_Contributor_Widget');
}
add_action('widgets_init', 'msdlab_load_contributor_widget');

/**
 * Tribe_Image_Widget class
 **/
class MSDLab_Contributor_Widget extends WP_Widget {
    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array( 
            'class_name' => 'msdlab-contributor',
            'description' => 'Display a random Contributor (Expert)',
        );
        parent::__construct( 'msdlab-contributor', 'Contributor Widget', $widget_ops );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $expert = $this->msdlab_get_author();
        echo $before_widget;
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
        
        echo get_image_tag( $expert[avatar_id], $expert[author]->display_name, $expert[author]->display_name, "left", "thumbnail" );
        
        if ( !empty( $description ) ) {
            echo '<div class="'.$this->widget_options['classname'].'-description" >';
            echo '<a href="'.get_author_posts_url( $expert[author]->ID ).'">'.$expert[author]->display_name.'</a><br />';
            echo $expert[position].'<br />';
            echo $expert[company].'<br />';
            echo "</div>";
        }
        $linktext = $linktext != ''?$linktext:'More Experts';
        echo '<div class="link"><a class="readmore" href="/contributors">'.$linktext.' ></a><div class="clear"></div></div>';
        
        echo '<div class="clear"></div>';
        echo $after_widget;
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // outputs the options form on admin
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = strip_tags($instance['title']);
?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
            <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
         
        return $instance;
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
    function msdlab_get_author( $args = '' ) {
        global $wpdb;
    
        $defaults = array(
            'orderby' => 'name', 'order' => 'ASC', 'number' => '',
            'optioncount' => false, 'exclude_admin' => true,
            'show_fullname' => false, 'hide_empty' => true,
            'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
            'style' => 'list', 'html' => true, 'exclude' => '', 'include' => '',
            'avatar' => true, 'position' => true, 'company' => true, 'bio' => 'excerpt', 
            'role' => 'contributor'
        );
    
        $args = wp_parse_args( $args, $defaults );
    
        $return = '';
    
        $query_args = wp_array_slice_assoc( $args, array( 'orderby', 'order', 'number', 'exclude', 'include', 'role' ) );
        $query_args['fields'] = 'ids';
        $authors = get_users( $query_args );
    
        $author_count = array();
        foreach ( (array) $wpdb->get_results( "SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author" ) as $row ) {
            $author_count[$row->post_author] = $row->count;
        }
        foreach ( $authors as $author_id ) {
            $user[$author_id][author]     = get_userdata( $author_id );
            $user[$author_id][headline]   = get_the_author_meta( 'headline', $author_id );
            $user[$author_id][url]        = get_the_author_meta( 'url', $author_id );
            $user[$author_id][company]   = get_the_author_meta( 'company', $author_id );
            $user[$author_id][position]   = get_the_author_meta( 'position', $author_id );
            $user[$author_id][intro_text] = get_the_author_meta( 'intro_text', $author_id );
            $user[$author_id][bio]        = get_the_author_meta( 'description', $author_id );
    
            if ( $args['avatar'] ){
                $user[$author_id][avatar]     = get_the_author_meta( 'profile_img', $author_id );
                $user[$author_id][avatar_id]  = $user[$author_id][avatar] ? get_attachment_id_from_src($user[$author_id][avatar]) : FALSE;
                $user[$author_id][image]      = wp_get_attachment_image_src( $user[$author_id][avatar_id], 'thumbnail' );
            }
        }
        shuffle($user);
        return array_pop($user);   
    
    }
}