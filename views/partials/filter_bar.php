<?php

wp_enqueue_style( 'filter-bar-css' );
wp_enqueue_script( 'filter-bar-js' );

// This requires you to use the /classification/$class/$tax
//set and define any taxonomies that may be applied to the archive page
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
//check if a taxonomy filter is present and then if it is set the values to filter by it
if(is_object($term)) { $termname = $term->name; $class = $term->slug; } else { $class = ''; }

$get_uwopeople_cats = get_terms('uwopeople_classification', array('number' => 15, 'parent' => 0)); 
list($titan, $options) =  uwopeople_get_plugin_options();
$people_archive_listing = $titan->getOption('directory_layout', get_the_ID());

$uwo_filter = '
    <div class="filter-bar">
        <div class="row">
          <div class="searchwrap">
                  <input type="search" tabindex="1" class="search-field-people" placeholder="Search..." value="" name="" title="Search:">
                  <div class="cancel-search fa fa-times-circle"></div>
          </div>
                <ul class="options">';

  if ($people_archive_listing == "gridlist") { $uwo_filter .= '<li><ul class="view" style="padding: 0 !important;"><li class="view-grid view-active">Grid</li><li class="view-list">List</li></ul></li>'; }

  if(!empty($get_uwopeople_cats) && !is_wp_error($get_uwopeople_cats)) { $uwo_filter .= '<li class="filter">Filters</li>'; }
  $uwo_filter .= '</ul></div>';

  if(!empty($get_uwopeople_cats) && !is_wp_error($get_uwopeople_cats)) {
    //RDK setting a people path var (check with chris and michael about this
    $people_path = esc_html( $titan->getOption( 'people_path', get_the_ID() ), true );
    if ( !empty($people_path) ) {
      $path = site_url(esc_html($titan->getOption('people_path', get_the_ID()), true));
    } else {
      $path = site_url('/people/');
    }

    $children_exist = 0;

    foreach($get_uwopeople_cats as $classification) {
      $check_child_cats = get_terms('uwopeople_classification', array('parent' => $classification->term_id));

      if(!empty($check_child_cats)) {
        $children_exist = 1;
      }
    }

    $uwo_filter .= '<div class="filter-panel">';
    if($children_exist) {
      $uwo_filter .=  '<div class="row filter-parent">
                            <ul>
                              <li id="tab0" class="tab"><a href="' . $path . '">View All</a></li>';

      foreach($get_uwopeople_cats as $classification) {
        $uwo_filter .= '<li id="tab' . $classification->term_id . '" class="tab"><a href="' . get_term_link( $classification ) . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $classification->name) . '">' . $classification->name . '</a>';
      }
      $uwo_filter .= '</ul></div>';

      foreach($get_uwopeople_cats as $classification) {
        $get_child_cats = get_terms('uwopeople_classification', array('parent' => $classification->term_id));
        $items_per_column = ceil(count($get_child_cats) / 3);
        $child_cat_list = '';

        if(!empty($get_child_cats)) {

          $i=1;

          $child_cat_list .= '<div id="list-tab'. $classification->term_id .'" class="row filter-list"><ul>';

          foreach($get_child_cats as $child_class) {

            $child_cat_list .= '<li class="item"><a href="' . get_term_link( $child_class ) . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $child_class->name) . '">' . $child_class->name . '</a></li>';

            if ($i == $items_per_column) {
              $child_cat_list .= '</ul><ul>';
              $i=1;
            } else {
              $i++;
            }
          }

          $child_cat_list .= '</ul></div>';
        }

        $uwo_filter .= $child_cat_list;
      }
    } else {
      $items_per_column = ceil((count($get_uwopeople_cats)+1) / 3);
      $cat_list = '';
      $i=1;

      $cat_list .= '<div id="list-tab'. $classification->term_id .'" class="row filter-list"><ul><li class="item"><a href="'. $path .'">View All</a></li>';

      if($items_per_column == 1) {
        $cat_list .= '</ul><ul>';
        $i=1;
      } else {
        $i++;
      }

      foreach($get_uwopeople_cats as $classification) {

        $cat_list .= '<li class="item"><a href="' . get_term_link( $classification ) . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $classification->name) . '">' . $classification->name . '</a></li>';

        if ($i == $items_per_column) {
          $cat_list .= '</ul><ul>';
          $i=1;
        } else {
          $i++;
        }
      }

      $cat_list .= '</ul></div>';

      $uwo_filter .= $cat_list;
    }

     $uwo_filter .= '</div><!-- .filter-panel -->';
  }
$uwo_filter .= '</div><!-- .filter-bar -->';
