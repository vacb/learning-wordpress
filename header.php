<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class();?>>
    <header class="site-header">
        <div class="container">
            <h1 class="school-logo-text float-left">
                <a href="<?php echo site_url() ?>"><strong>Fictional</strong> University</a>
            </h1>
            <a class="js-search-trigger site-header__search-trigger" href="<?php echo esc_url(site_url('/search')); ?>"><i class="fa fa-search" aria-hidden="true"></i></a>
            <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
            <div class="site-header__menu group">
            <nav class="main-navigation">

            <!-- Dynamic version of the nav menu -->
                <!-- <?php wp_nav_menu(array(
                    'theme_location' => 'headerMenuLocation'
                    )); 
                ?> -->

            <!-- Static version of the nav menu -->
            <ul>
                <!-- 0 = look up current page -->
                <!-- If current page is 'about us' or if current page has parent 'about us', add the class -->
                <li <?php if (is_page('about-us') or wp_get_post_parent_id(0)) echo 'class="current-menu-item"'; ?>><a href="<?php echo site_url('/about-us'); ?>">About Us</a></li>
                <li <?php if (get_post_type() == 'program') echo 'class="current-menu-item"'; ?>><a href="<?php echo get_post_type_archive_link('program'); ?>">Programs</a></li>
                <li <?php if (get_post_type() == 'event' OR is_page('past-events')) echo 'class="current-menu-item"'; ?>><a href="<?php echo get_post_type_archive_link('event') ?>">Events</a></li>
                <li <?php if (get_post_type() == 'campus') echo 'class="current-menu-item"'; ?>><a href="<?php echo get_post_type_archive_link('campus'); ?>">Campuses</a></li>
                <li <?php if (get_post_type() == 'post') echo 'class="current-menu-item"'; ?>><a href="<?php echo site_url('/blog'); ?>">Blog</a></li>
                </ul>


            </nav>
            <div class="site-header__util">
                <a href="#" class="btn btn--small btn--orange float-left push-right">Login</a>
                <a href="#" class="btn btn--small btn--dark-orange float-left">Sign Up</a>
                <a class="search-trigger js-search-trigger" href="<?php echo esc_url(site_url('/search')); ?>"><i class="fa fa-search" aria-hidden="true"></i></a>
            </div>
            </div>
        </div>
    </header>

<!-- Closing html and body tags moved to footer.php -->