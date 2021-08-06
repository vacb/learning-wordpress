<div class="postitem">
<!-- <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2> -->
  <li class="academic-card__list-item">
    <a class="academic-card" href="<?php the_permalink(); ?>">
    <img class="academic-card__image" src="<?php the_post_thumbnail_url('academicLandscape'); ?>">
    <span class="academic-card__name">
      <?php the_title(); ?>
    </span>
    </a>
  </li>
</div>
