<?php
    get_header();
    pageBanner();

    while(have_posts()) {
        the_post(); 
?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs</a> <span class="metabox__main"><?php the_title(); ?></span>
                </p>
        </div>
        <div class="generic-content">
            <?php the_content(); ?>
        </div>

        <?php
            $relatedAcademics = new WP_Query(array(
                'posts_per_page' => -1,
                'post_type' => 'academic',
                'orderby' => 'title',
                'order' => 'ASC',
                'meta_query' => array(
                // One inner array for each filter
                    array(
                        'key' => 'related_program',
                        'compare' => 'LIKE',
                        // Concatenate in "" to search for "12" rather than 12, say - arrays are serialised to this avoids false positives
                        'value' => '"' . get_the_ID() . '"'
                    )
                )
            ));

            if ($relatedAcademics->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">' . get_the_title() . ' Academics</h2>';

                echo '<ul class="academic-cards">';
                while($relatedAcademics->have_posts()) {
                    $relatedAcademics->the_post(); ?>
                    <li class="academic-card__list-item">
                        <a class="academic-card" href="<?php the_permalink(); ?>">
                            <img class="academic-card__image" src="<?php the_post_thumbnail_url('academicLandscape'); ?>">
                            <span class="academic-card__name">
                                <?php the_title(); ?>
                            </span>
                        </a>
                    </li>

                    <?php 
                }
                echo '</ul>';
            }

            // Reset the global post object, including the data returned by the_title() and the_ID() used below, back to the default url-based query
            // If you don't reset, the events section will disappear as it will be referencing info from the last query
            // Run this in between queries
            wp_reset_postdata();

            $today = date('Ymd');
            $homepageEvents = new WP_Query(array(
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
                        'key' => 'related_program',
                        'compare' => 'LIKE',
                        // Concatenate in "" to search for "12" rather than 12, say - arrays are serialised to this avoids false positives
                        'value' => '"' . get_the_ID() . '"'
                    )
                )
            ));

            if ($homepageEvents->have_posts()) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Upcoming ' .  get_the_title() . ' Events</h2>';
    
                while($homepageEvents->have_posts()) {
                    $homepageEvents->the_post(); ?>
                        <div class="event-summary">
                            <a class="event-summary__date t-center" href="#">
                            <span class="event-summary__month">
                                <?php 
                                $eventDate = new DateTime(get_field('event_date'));
                                echo $eventDate->format('M');
                            ?>
                            </span>
                            <span class="event-summary__day">
                            <?php 
                                $eventDate = new DateTime(get_field('event_date'));
                                echo $eventDate->format('d');
                            ?>
                            </span>
                            </a>
                            <div class="event-summary__content">
                            <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                            <p>
                            <?php if (has_excerpt()) {
                                    echo get_the_excerpt();
                                    } else {
                                    echo wp_trim_words(get_the_content(), 18);
                                    }
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>
                            </div>
                        </div>
    
                    <?php 
                }
            }

            ?>

    </div>
   
<?php 
}
get_footer();

?>