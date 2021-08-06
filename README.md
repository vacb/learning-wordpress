# learning-wordpress

## Course access

Following a WordPress course on Udemy:
https://www.udemy.com/course/become-a-wordpress-developer-php-javascript/

## Not in repository

Details below of anything held outside the main theme repository - these will
need to be added in order for theme to function.

### Google Maps API key

Added API key for Google Maps to `wp-config.php`:

```php
/** Google Maps API key. */
define( 'GOOGLE_MAPS_API', 'YourKeyHere' );
```

### Post types creation plugin

Post types are created in `university-post-types.php` held in
`wp-content/mu-plugins`. Content as follows:

```php
<?php

function university_post_types () {
     // Campus post type
     register_post_type('campus', array(
        'supports' => array('title', 'editor', 'excerpt'),
        'public' => true,
        'rewrite' => array(
            'slug' => 'campuses'
        ),
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-location-alt',
        'labels' => array(
            'name' => 'Campuses',
            'add_new_item' => 'Add New Campus',
            'edit_item' => 'Edit Campus',
            'all_items' => 'All Campuses',
            'singular_name' => 'Campus'
        )
    ));
    // Event post type
    register_post_type('event', array(
        // Default supports are 'title' and 'editor'
        'supports' => array('title', 'editor', 'excerpt'),
        'public' => true,
        'rewrite' => array(
            'slug' => 'events'
        ),
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'labels' => array(
            'name' => 'Events',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items' => 'All Events',
            'singular_name' => 'Event'
        )
    ));
    // Program post type
    register_post_type('program', array(
        'supports' => array('title'),
        'public' => true,
        'rewrite' => array(
            'slug' => 'programs'
        ),
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-book',
        'labels' => array(
            'name' => 'Programs',
            'add_new_item' => 'Add New Program',
            'edit_item' => 'Edit Program',
            'all_items' => 'All Programs',
            'singular_name' => 'Program'
        )
    ));
    // Academic post type
    register_post_type('academic', array(
        'supports' => array('title', 'editor', 'thumbnail'),
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'labels' => array(
            'name' => 'Academics',
            'add_new_item' => 'Add New Academic',
            'edit_item' => 'Edit Academic',
            'all_items' => 'All Academics',
            'singular_name' => 'Academic'
        )
    ));
}

add_action('init', 'university_post_types');
?>
```
