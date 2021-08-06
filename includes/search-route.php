<?php 

add_action('rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch() {
    // namespace, route, array to describe what happens when people visit this url
    // Namespace usually includes a version, so that if people are using the API you can make 
    // big changes without breaking their code by using a new version number
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE, // Usually evaluates to 'GET' but safer on some browsers
        'callback' => 'universitySearchResults'
    ));
}

// $data argument contains an array of the query string e.g. http://university.test/wp-json/university/v1/search?term=barksalot
// gives $data = ['term' => 'barksalot']
function universitySearchResults($data) {
    // WP converts php to json automatically
    $mainQuery = new WP_Query(array(
        'post_type' => array('post', 'page', 'academic', 'program', 'campus', 'event'),
        // 's' stands for search, access $data which contains the search term
        // Use WP function to sanitise the data to avoid injection of malicious code
        's' => sanitize_text_field($data['term'])
    ));

    // Narrow down the object to only return relevant json - create array and then push items onto it

    // Create multiple sub-arrays to organise the results into sections
    $results = array(
        'generalInfo' => array(),
        'academics' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    while($mainQuery->have_posts()) {
        // Gets the relevant data ready and accessible
        $mainQuery->the_post();
        if (get_post_type() == 'post' OR get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            ));
        }

        if (get_post_type() == 'academic') {
            array_push($results['academics'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                // 0 = current post
                'img' => get_the_post_thumbnail_url(0, 'academicLandscape')
            ));
        }

        if (get_post_type() == 'program') {
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()
            ));
        }

        if (get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18);
            }

            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }

        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink()
            ));
        }
        
    }

    // Additional query and related code to show academics related to search term

    // Only run the code below if there are programs to find a relationship with
    if($results['programs']) {
      // Deal with situation where multiple programs are related to the search term e.g. human biology, advanced biology
      // Default behaviour for WP with multiple conditions is 'AND' so specify 'OR' if required
      $programsMetaQuery = array('relation' => 'OR');

      foreach($results['programs'] as $item) {
          array_push($programsMetaQuery, array(
              'key' => 'related_program',
              'compare' => 'LIKE',
              'value' => '"' . $item['id'] . '"'
          ));
      }

      // Additional custom query to search for academics related to the search term e.g. all Biology academics
      $programRelationshipQuery = new WP_Query(array(
          'post_type' => 'academic',
          // Use meta query to search a custom field
          'meta_query' => $programsMetaQuery

          // REPLACED THIS WITH CODE TO ACCOUNT FOR MULTIPLE PROGRAMS RELATED TO SEARCH TERM
          // 'meta_query' => array(
          //    'relation' => 'OR',
          //     array(
          //         'key' => 'related_program',
          //         'compare' => 'LIKE',
          //         // Find id number of any programs that match the user's search term
          //         // Added program id number to json for 'program' post type to facilitate this
          //         // Search results will now show academics related to search term
          //         'value' => '"' . $results['programs'][0]['id'] . '"'
          // ))
      ));

      while($programRelationshipQuery->have_posts()) {
          $programRelationshipQuery->the_post();
          if (get_post_type() == 'academic') {
              array_push($results['academics'], array(
                  'title' => get_the_title(),
                  'permalink' => get_the_permalink(),
                  // 0 = current post
                  'img' => get_the_post_thumbnail_url(0, 'academicLandscape')
              ));
          }
      }
      
      // Remove duplicates caused by using more than one query with array_unique()
      // SORT_REGULAR - added to work with associative arrays, i.e. please look within each sub-item of an array when determining if they are a duplicate or not
      // Wrap in array_values() to remove index numbers added by array_unique()
      $results['academics'] = array_values(array_unique($results['academics'], SORT_REGULAR));
    }

    return $results;
}