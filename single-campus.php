<?php
    get_header();
    pageBanner();

    while(have_posts()) {
        the_post(); 
?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Campuses</a> <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>
        <div class="generic-content">
            <?php the_content(); ?>
        </div>

        <!-- Code for map - while loop removed as only need to show single campus -->
        <div class="acf-map">
            <?php $mapLocation = get_field('map_location'); ?>
            <div class="marker" data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
                <h3>
                        <?php the_title(); ?>
                </h3>
                <?php echo $mapLocation['address']; ?>
            </div>
        </div>

        <?php
            $relatedPrograms = new WP_Query(array(
                'posts_per_page' => -1,
                'post_type' => 'program',
                'orderby' => 'title',
                'order' => 'ASC',
                'meta_query' => array(
                // One inner array for each filter
                    array(
                        'key' => 'related_campus',
                        'compare' => 'LIKE',
                        // Concatenate in "" to search for "12" rather than 12, say - arrays are serialised to this avoids false positives
                        'value' => '"' . get_the_ID() . '"'
                    )
                )
            ));

            if ($relatedPrograms->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Programs Available At This Campus</h2>';

                echo '<ul class="min-list link-list">';
                while($relatedPrograms->have_posts()) {
                    $relatedPrograms->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </li>

                    <?php 
                }
                echo '</ul>';
            }

            // Reset the global post object, including the data returned by the_title() and the_ID() used below, back to the default url-based query
            // If you don't reset, the events section will disappear as it will be referencing info from the last query
            // Run this in between queries
            wp_reset_postdata();

            // Custom query to pull in the next two upcoming events for this campus
            $today = date('Ymd');
            $campusEvents = new WP_Query(array(
                'posts_per_page' => 2,
                'post_type' => 'event',
                'meta_key' => 'event_date',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                // One inner array for each filter
                    array(
                        'key' => 'event_date',
                        'compare' => '>=',
                        'value' => $today,
                        'type' => 'numeric'
                    ),
                    array(
                        'key' => 'event_location',
                        'compare' => 'LIKE',
                        // Concatenate in "" to search for "12" rather than 12, say - arrays are serialised to this avoids false positives
                        'value' => '"' . get_the_ID() . '"'
                    )
                )
            ));
           
            if ($campusEvents->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Upcoming Events At The ' .  get_the_title() . ' Campus</h2>';
    
                while($campusEvents->have_posts()) {
                    $campusEvents->the_post();
                    get_template_part('template-parts/content-event');
                }
            }

            ?>

    </div>
   
<?php 
}
get_footer();

?>