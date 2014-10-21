<?php

/**
 * Plugin Name: Media Image Widget
 * Description: Provide a widget for images from media library
 * Version: 0.1
 * Author: Hong Anh
 * Author URI: http://vndis.com
 */
class Vndis_Image_Widget extends WP_Widget
{

    /**
     * Widget setup.
     */
    public function __construct()
    {
        /* Widget settings. */
        $widget_ops = array('classname' => 'vndis-image-widget', 'description' => __('List media image library.', 'vndis-image-widget'));

        /* Widget control settings. */
        $control_ops = array('width' => '100%', 'height' => '100%', 'id_base' => 'vndis-image-widget');

        /* Create the widget. */
        parent::__construct('vndis-image-widget', __('Vndis Media Widget', 'vndis-image-widget'), $widget_ops, $control_ops);

        /* Actions */
        add_action('init', array(&$this, 'addCss'));
        add_action('widgets_init', array(&$this, 'load_widget'));

    }

    /**
     * Display the widget on the screen.
     */
    public function widget($args, $instance)
    {
        wp_enqueue_style('vndis-image-widget');
        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title']);
        $count = $instance['count'];

        /* Before widget (defined by themes). */
        $show = $before_widget;

        /* Display the widget title if one was input (before and after defined by themes). */
        if ($title)
        {
            $show .= $before_title . $title . $after_title;
        }
        $show .= '<ul>';
        $show .= $this->get_media_library_images($count);
        $show .= '</ul>';
        /* After widget (defined by themes). */
        $show .= $after_widget;
        echo $show;
    }

    /**
     * Update the widget settings.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? strip_tags($new_instance['title']) : __('Media Widget', 'vndis-image-widget');
        $instance['count'] = isset($new_instance['count']) ? (int)strip_tags($new_instance['count']) : 9;
        return $instance;
    }

    public function form($instance)
    {

        /* Set up some default widget settings. */
        $defaults = array('title' => __('Media', 'vndis-image-widget'), 'count' => 9);
        $instance = wp_parse_args((array)$instance, $defaults);
        ?>
        <p>
            <label
                for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'vndis-image-widget'); ?></label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
                   value="<?php echo $instance['title']; ?>" style="width:100%;"/>
        </p>
        <p>
            <label
                for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of media items to list:', 'vndis-image-widget'); ?></label>
            <input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"
                   value="<?php echo $instance['count']; ?>" style="width:100%;"/>
        </p>
    <?php
    }

    private function get_media_library_images($number = 9)
    {
        $args = array('post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'inherit', 'posts_per_page' => $number, 'orderby' => 'rand');
        $query = new WP_Query($args);
        $show = '';
        foreach ($query->posts as $image)
        {
            $post_link = get_permalink($image->post_parent);
            if (empty($post_link))
                $post_link = '#';
            $show .= '<li><a href="' . $post_link . '"><img src="' . $image->guid . '" alt="' . $image->post_title . '" /></a></li>';
        }

        return $show;
    }

    public function addCss()
    {
        wp_register_style('vndis-image-widget', plugins_url('css/media.css', __FILE__));
    }

    public function load_widget()
    {
        register_widget('Vndis_Image_Widget');
    }
}

$imageWidget = new Vndis_Image_Widget();
