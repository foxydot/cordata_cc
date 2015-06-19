<?php

/*
Plugin Name: MSD Text Widget with Click
Plugin URI: http://msdlab.com/plugins/msd-txtwdgt-click
Description: Replacement text widget for clickable widget area.
Version: 0.4
Author: Catherine M OBrien Sandrick (CMOS)
Author URI: http://msdlab.com/biological-assets/catherine-obrien-sandrick/
License: GPL v2
*/

class MSD_Widget_Text extends WP_Widget {

	function __construct() {
		add_action('wp_print_styles', array($this,'add_css'));
		add_action('wp_print_scripts', array($this,'add_js'));
		$widget_ops = array('classname' => 'widget_text', 'description' => __('Arbitrary text or HTML with optional URL'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('text', __('Text'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters( 'widget_text', $instance['text'], $instance );
		$url = empty($instance['url']) ? FALSE : $instance['url'];
		$target = $instance['target'] ? ' target="_blank"':'';
		$linktext = apply_filters( 'widget_title', empty($instance['linktext']) ? 'Read More' : $instance['linktext'], $instance, $this->id_base);
		echo $before_widget; ?>
		<div class="textwidget">
			<?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
			echo $instance['filter'] ? wpautop($text) : $text; 
			if ( !empty( $url ) ) { echo '<a href="'.$url.'"'.$target.' class="readmore"><span>'.$linktext.'</span></a>'; } ?>
		</div>
		<?php
		
		print $url?'<a href="'.$url.'"'.$target.' class="msd-widget-text"></a>':'';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['target'] = isset($new_instance['target']);
		$instance['linktext'] = strip_tags($new_instance['linktext']);
		
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
		$url = strip_tags($instance['url']);
		$linktext = strip_tags($instance['linktext']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_attr($url); ?>" /></p>
		<p><input id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" type="checkbox" <?php checked(isset($instance['target']) ? $instance['target'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Open in new window'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('linktext'); ?>"><?php _e('Link Text:'); ?></label><input class="widefat" id="<?php echo $this->get_field_id('linktext'); ?>" name="<?php echo $this->get_field_name('linktext'); ?>" type="text" value="<?php echo esc_attr($linktext); ?>" /></p>
		
<?php
	}
	
	function init() {
		if ( !is_blog_installed() )
			return;
	
		unregister_widget('WP_Widget_Text');
		register_widget('MSD_Widget_Text');
	}
	
	function add_css(){
		if(!is_admin()){
			wp_enqueue_style('msd-widget-text',plugin_dir_url(__FILE__).'/css/msd-widget-text.css');
		}
	}
	function add_js(){
		if(!is_admin()){
			wp_enqueue_script('msd-widget-text',plugin_dir_url(__FILE__).'/js/msd-widget-text.js','jquery','0.4',TRUE);
		}
	}
}

	
	add_action('widgets_init',array('MSD_Widget_Text','init'),10);
?>