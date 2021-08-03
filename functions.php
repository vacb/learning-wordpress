<?php 

// Create function and point it to the css file. For default use get_stylesheet_uri() function
// wp_enqueue_style( $handle, $src, $deps, $ver, $media );
// wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
function university_files() {
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
    
}

// Tell wordpress to run function at the right moment
add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
    // Sets page title
    add_theme_support( 'title-tag' );
    // Removed dynamic nav per tutorial, but left for reference:
        // register_nav_menu('headerMenuLocation', 'Header Menu Location');
        // register_nav_menu('footerMenuLocationOne', 'Footer Menu Location One');
        // register_nav_menu('footerMenuLocationTwo', 'Footer Menu Location Two');
        // Then you can set up and edit menu in wp-admin
    add_theme_support('post-thumbnails');
    // Add another size option to the defaults created by WP (won't retroactively create without Regenerate Thumbnails plugin)
    // Nickname, width, height, crop (default: false)
    add_image_size('academicLandscape', 400, 260, true);
    add_image_size('academicPortrait', 480, 650, true);
}

add_action('after_setup_theme', 'university_features');

// Events custom post type removed and added to wp-content/mu-plugins/university-post-types.php

// Code to adjust default queries e.g. want to sort and filter event archive page

function university_adjust_queries ($query) {
    // Manipulate query object when received from WP
    // Add  $query->is_main_query() to make sure we're not manipulating a custom query
    if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => $today,
              'type' => 'numeric'
            )
            ));
    }
    // Adjust programs query
    if (!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
}

add_action('pre_get_posts', 'university_adjust_queries');