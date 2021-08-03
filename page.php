<?php
get_header();
while(have_posts()) {
    the_post(); 
    pageBanner();
    ?>
    
    
    
    <div class="container container--narrow page-section">

<?php 
        $theParent = wp_get_post_parent_id(get_the_ID());
        if ($theParent) { ?>
            
                <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_the_permalink($theParent); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParent); ?></a> <span class="metabox__main"><?php the_title(); ?></span>
                </p>
            </div>
<?php }
    ?>

<!-- Only display this menu if on a child page or a parent page -->
<?php 
    // get_pages() will return null/0 if the current page has no children
    $testArray = get_pages(array(
        'child_of' => get_the_ID()
    ));

    if ($theParent or $testArray) { ?>

      <div class="page-links">
        <h2 class="page-links__title"><a href="<?php echo get_permalink($theParent); ?>"><?php echo get_the_title($theParent)?></a></h2>
        <ul class="min-list">
          <?php
          if ($theParent) {
            $findChildrenOf = $theParent;
          } else {
            $findChildrenOf = get_the_ID();
          }
        //  wp_list_pages( $args:array|string ) Associative array $annimalSounds = array('cat' => 'meow', 'dog => 'woof');
        // Access associative array: echo $animalSounds['dog']; - returns 'woof'
            wp_list_pages(array(
                'title_li' => NULL,
                'child_of' => $findChildrenOf,
                // Can set 'order' in page properties to manually control sort order
                'sort_column' => 'menu_order'
            ));
           ?>
        </ul>
      </div>

<?php } ?>

        <div class="generic-content">
            <?php the_content(); ?>
        </div>
    </div>
   
<?php }
get_footer();
?>