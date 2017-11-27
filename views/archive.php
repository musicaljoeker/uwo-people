<?php
/*
Template Name: UWO People Profile Directory
*/

get_header();
?>

<div id="content" class="content-area">
    <div id="primary" class="site-content" role="main">
    <h1 class="entry-title">People Directory</h1>

<?php


  if (file_exists(plugin_dir_path(dirname(__FILE__)) . 'views/partials/filter_bar.php')) {
    include plugin_dir_path(dirname(__FILE__)) . 'views/partials/filter_bar.php';
    echo $uwo_filter;
  }



  if(!isset($disable_links))
  {
    $disable_links = ($titan->getOption('disable_links') == 'true' || $titan->getOption('disable_links') == 'True');
  }

  if (file_exists(plugin_dir_path(dirname(__FILE__)) . 'views/partials/directory.php')) {
    include plugin_dir_path(dirname(__FILE__)) . 'views/partials/directory.php';
    echo $the_loop;
}

		
?>
        
    </div><!-- #primary -->
</div><!-- #content -->

<?php get_footer(); ?>