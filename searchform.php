<!-- Any time you echo a url from the database, security best practice is to wrap it in the esc_url() function -->
<!-- Not to protect site from hacking, but to protect visitors if site has been hacked -->
<!-- GET makes sure content ends up in the url (post won't) - normally get is default -->

<form class="search-form" method="get" action="<?php echo esc_url(site_url('/')); ?>">
  <!-- Use s to match default WP search url -->
  <label class="headline headline--medium" for="s">Perform a new search:</label>
  <div class="search-form-row">
    <input class="s" type="search" name="s" id="s" placeholder="What are you looking for?">
    <input class="search-submit" type="submit" value="Search">
  </div>  
</form>