<?php 

require get_theme_file_path('/includes/search-route.php');

// Adding custom fields to rest api

function university_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {
            return get_the_author();
        }
    ));
    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() {
            return count_user_posts(get_current_user_id(), 'note');
        }
    ));
}
add_action('rest_api_init', 'university_custom_rest');

// $args = NULL provides a default value for where we want headers with no custom title requirement
function pageBanner($args = NULL) {
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }
    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (!$args['photo']) {
        // Check to see if the current post has a banner image custom field value and make sure the current query is not an archive or a blog listing
        // The second two conditions avoid using the wrong banner image if first event in the list of events has a background image
        if (get_field('page_banner_background_image') AND !is_archive() AND !is_home() ) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }    
    }
    ?>
        <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>)"></div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
                <div class="page-banner__intro">
                    <p><?php echo $args['subtitle'] ?></p>
                </div>
            </div>
        </div>
    <?php
}

// Create function and point it to the css file. For default use get_stylesheet_uri() function
// wp_enqueue_style( $handle, $src, $deps, $ver, $media );
// wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
function university_files() {
    wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key=' . GOOGLE_MAPS_API, NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
    
    // Creates a variable named universityData with properties as per the included associative array
    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        // When log into WP, generate secret property named nonce that = randomly generated number that WP creates just for our user session
        'nonce' => wp_create_nonce('wp_rest')
    ));
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
    add_image_size('pageBanner', 1500, 350, true);
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
     // Adjust campus query to bring in all posts i.e. show all map pins rather than default 10
    if (!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()) {
        $query->set('posts_per_page', -1);
    }
}

add_action('pre_get_posts', 'university_adjust_queries');

function universityMapKey($api) {
    $api['key'] = GOOGLE_MAPS_API;
    return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');

// Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');
function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

// Hide WP admin bar for subscriber users
add_action('wp_loaded', 'noSubsAdminBar');
function noSubsAdminBar() {
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

// Customise login screen
add_filter('login_headerurl', 'ourHeaderUrl');
function ourHeaderUrl() {
    return esc_url(site_url('/'));
}

add_filter('login_headertext', 'ourLoginText');
function ourLoginText() {
  return get_bloginfo('name');
}

// WP logo on login page is CSS
add_action('login_enqueue_scripts', 'ourLoginCSS');
function ourLoginCSS() {
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

// Force note posts to be private
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);
function makeNotePrivate($data, $postarr) {
    // Limit number of note posts per user
    if ($data['post_type'] == 'note') {
        // Count current user posts (user id, post type)
        // Check for non-existence of an ID in postarr to establish that we're creating a new post - otherwise will run for del and edit too
        if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
            die('You have reached your note limit.');
        }
    }

    // Sanitise title and content fields
    if ($data['post_type'] == 'note') {
        $data['post_title'] = sanitize_text_field( $data['post_title'] );
        $data['post_content'] = sanitize_textarea_field( $data['post_content'] );
    }
    // Make notes private by default
    if ($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = "private";
    }
    
    return $data;
}

