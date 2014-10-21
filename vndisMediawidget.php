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
    function Vndis_Image_Widget()
    {
        /* Widget settings. */
        $widget_ops = array('classname' => 'vndis_image_widget', 'description' => __('List media image library.', 'vndis_image_widget'));

        /* Widget control settings. */
        $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'vndis_image_widget');

        /* Create the widget. */
        $this->WP_Widget('vndis-image-widget', __('Vndis Media Widget', 'vndis_image_widget'), $widget_ops, $control_ops);

        /* Actions */
        add_action('widgets_init', array(&$this, 'load_widget'));

    }

    /**
     * Register the widget
     */
    function load_widget()
    {
        register_widget('Vndis_Image_Widget');
        $this->addCss();
    }

    /**
     * Display the widget on the screen.
     */
    function widget($args, $instance)
    {
        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title']);
        $count = isset($instance['count']) ? $instance['count'] : 5;

        /* Before widget (defined by themes). */
        $show = $before_widget;

        /* Display the widget title if one was input (before and after defined by themes). */
        if ($title)
            $show .= $before_title . $title . $after_title;
        $arrImages = $this->get_media_library_images($count);
        if (count($arrImages) > 0)
        {
            $show .= "<ul>";
            foreach ($arrImages as $image)
            {
                var_dump($image); return;
                $show .= '<li><a href="' .$image->url . '"><img src="' . $image->img . '" alt="'.$image->title .'"/></a></li>';
			}
            $show .= "</ul>";
        }
        else
        {
            _e('No media selected', 'vndis_image_widget');
        }

        /* After widget (defined by themes). */
        $show .= $after_widget;
        echo $show;
    }

    /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        /* Strip tags for title to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        if (!is_numeric($instance['count']))
            $instance['count'] = 5;

        return $instance;
    }

    function form($instance)
    {

        /* Set up some default widget settings. */
        $defaults = array('title' => __('Media', 'vndis_image_widget'), 'count' => 5);
        $instance = wp_parse_args((array)$instance, $defaults);
        ?>

        <!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'vndis_image_widget'); ?></label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
                   value="<?php echo $instance['title']; ?>" style="width:100%;"/>
        </p>

        <!-- Count: How many items should we list? Blank to list all items. -->
        <p>
            <label
                for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of media items to list:', 'vndis_image_widget'); ?></label>
            <input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"
                   value="<?php echo $instance['count']; ?>" style="width:100%;"/>
        </p>

    <?php
    }
    function get_media_library_images($number = 5)
    {
        $args = array('post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'inherit', 'posts_per_page' => $number, 'orderby' => 'rand');
        $query_images = new WP_Query($args);
        $images = array();
        foreach ( $query_images->posts as $image) {
            $obj        = new stdClass();
            $obj->id    = $image->ID;
            $obj->url   = get_attachment_link($image->ID);
            $obj->img   = wp_get_attachment_url($image->ID);
            $obj->title = get_the_title($image->post_parent);
            $images[]= $obj;
        }
        return $images;
    }
    function addCss(){
        wp_enqueue_style('image-widget-css', plugins_url('css/media.css', __FILE__ ) );
    }
}

//start the plugin
$iWidget = new Vndis_Image_Widget();
