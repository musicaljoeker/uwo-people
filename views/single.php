<?php
/*
Template Name: UWO People Profile Details
*/

get_header(); ?>

    <div id="content" class="content-area">
        <div id="primary" class="site-content" role="main">

            <?php
            $is_shortcode = false;
            if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'views/partials/person.php' ) ) 
            {
                include plugin_dir_path( dirname( __FILE__ ) ) . 'views/partials/person.php';
                echo $the_loop;
            }

            ?>

        </div><!-- #primary -->
    </div><!-- #content -->

<?php get_footer(); ?>