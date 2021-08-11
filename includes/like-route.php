<?php

add_action('rest_api_init', 'universityLikeRoutes');
function universityLikeRoutes() {
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'POST',
    'callback' => 'createLike'
  ));
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'DELETE',
    'callback' => 'deleteLike'
  ));
}

function createLike($data) {
  $academic = sanitize_text_field($data['academicId']);

  wp_insert_post(array(
    'post_type' => 'like',
    'post_status' => 'publish',
    'post_title' => 'Second php test',
    // Adds WP custom fields, also known as meta fields
    'meta_input' => array(
      // Uses ACF field name for the key name
      'liked_academic_id' => $academic
    )
  ));
}

function deleteLike() {
  return 'Thanks for trying to delete a like';
}