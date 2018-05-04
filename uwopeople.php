<?php
/*
Plugin Name: UWO People
Plugin URI: http://uwm.edu/wordpress
Description: Create your staff/faculty profiles and directory!
Author: Ricky Kaja, Regan Jacobson, Christopher Maio, Michael Schuett, Jeremy Streich, Tamara Edmond, Mark Fairweather, Joseph Kerkhof
Version: 0.2
Author URI: http://uwm.edu/wordpress
*/

/**
 * Manual includes
 * - Titan Framework
 * - Shortcode Support
 * - Options Page
 */
include_once dirname(__FILE__) . '/include/titan-framework/titan-framework-embedder.php';
include_once dirname(__FILE__) . '/include/shortcodes.php';
include_once dirname(__FILE__) . '/include/options.php';

/**
 * Init function
 */
add_action( 'init', 'uwopeople' );
function uwopeople()
{
    // Create the custom post type
    uwopeople_generator();

    // Create our custom fields
    uwopeople_create_fields();

    // Check if the slug has been changed
    $customtax = get_post_type_object('uwopeople');
    $people_path = "";

    if ( class_exists( 'TitanFramework' ) )
    {
        $people_path = TitanFramework::getInstance( 'uwopeople' )->getOption('people_path');
    }

    if( $customtax != $people_path ) {
        flush_rewrite_rules();
    }
}

/**
 * Define and register the custom post type
 */
function uwopeople_generator()
{
	$labels = array(
		'name'               => __( 'UWO People', 'post type general name' ),
		'singular_name'      => __( 'UWO People', 'post type singular name' ),
		'add_new'            => __( 'Add New', 'uwopeople' ),
		'add_new_item'       => __( 'Add UWO People' ),
		'edit_item'          => __( 'Edit UWO People' ),
		'new_item'           => __( 'New UWO People' ),
		'all_items'          => __( 'All UWO People' ),
		'view_item'          => __( 'View UWO People' ),
		'search_items'       => __( 'Search UWO People' ),
		'not_found'          => __( 'No UWO People found' ),
		'not_found_in_trash' => __( 'No UWO People found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'People'
	);

    // Grab needed info to determine if the slug has been changed or not.
    $people_path = "";
    if ( class_exists( 'TitanFramework' ) )
    {
        $people_path = TitanFramework::getInstance( 'uwopeople' )->getOption('people_path');
    }

	$args = array(
		'labels'          => $labels,
		'description'     => 'UWO People profiles',
		'public'          => true,
		'menu_position'   => 5,
		'supports'        => array( 'editor', 'author' ),
		'has_archive'     => true,
		'menu_icon' 	  => '',
		'rewrite'         => array(
            'slug' => (!empty($people_path) ? $people_path : 'people'),
            'with_front' => false
        )
	);

	register_post_type( 'uwopeople', $args );

    // Add in the taxonomy support for classifying our UWO People
    register_taxonomy('uwopeople_classification', 'uwopeople', array(
        'hierarchical' => true,
        'labels' => array(
            'name' => __( 'Classification' ),
            'singular_name' => __( 'Classification' ),
            'search_items' =>  __( 'Search Classifications' ),
            'all_items' => __( 'All Classifications' ),
            'parent_item' => __( 'Parent Classification' ),
            'parent_item_colon' => __( 'Parent Classification:' ),
            'edit_item' => __( 'Edit Classification' ),
            'update_item' => __( 'Update Classification' ),
            'add_new_item' => __( 'Add New Classification' ),
            'new_item_name' => __( 'New Classification Name' ),
            'menu_name' => __( 'Classifications' ),
        ),
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'classification',
            'with_front' => false,
            'hierarchical' => true,
        ),
    ));
}

/**
 * Create custom fields for the UWO People Custom Post Type
 * These are created using the Titan Framework to write less code.
 */
function uwopeople_create_fields()
{
    if ( class_exists( 'TitanFramework' ) )
    {
        $titan = TitanFramework::getInstance( 'uwopeople' );

        $postMetaBox = $titan->createMetaBox( array(
            'name' => 'People Details',
            'post_type' => 'uwopeople',
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Profile Photo',
            'id' => 'profile_photo',
            'type' => 'upload',
            'desc' => 'Upload your profile photo, or leave it blank to use the default image.',
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Email Address',
            'id' => 'email',
            'type' => 'text',
            'desc' => 'Your UWO Email Address.'
        ) );

        $postMetaBox->createOption( array(
            'name' => 'First Name',
            'id' => 'first_name',
            'type' => 'text',
            'desc' => 'Your first name.'
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Last Name',
            'id' => 'last_name',
            'type' => 'text',
            'desc' => 'Your last name.'
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Job Title',
            'id' => 'job_title',
            'type' => 'text',
            'desc' => 'Your job title.'
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Phone',
            'id' => 'phone',
            'type' => 'text',
            'desc' => 'Your phone number.'
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Department',
            'id' => 'department',
            'type' => 'text',
            'desc' => 'Your department'
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Building',
            'id' => 'building',
            'type' => 'text',
            'desc' => 'Your building, e.g. Golda Meir Library.'
        ) );

        $postMetaBox->createOption( array(
            'name' => 'Room',
            'id' => 'room',
            'type' => 'text',
            'desc' => 'Your room number.'
        ) );

        $customFieldsMetaBox = $titan->createMetaBox( array(
            'name' => 'Additional Fields',
            'post_type' => 'uwopeople',
        ) );

        $custom_fields = preg_replace("/[\r\n]+/", "\n", $titan->getOption('custom_fields'));
        $split = explode( PHP_EOL, $custom_fields );
        if ( is_array( $split ) )
        {
            foreach ($split as $data)
            {
                if ( strpos( $data, '|' ) !== false )
                {
                    $field = explode( '|', $data );

                    // Allowed field types
                    $allowed_field_types = array( 'text', 'textarea' );

                    if ( in_array( $field[1], $allowed_field_types ) )
                    {
                        $field_type = $field[1];
                        $field_id = $field[0];
                        $field_desc = $field[2];
                        $field_name = ucwords( str_replace( '_', ' ', $field[0] ) );
                        $customFieldsMetaBox->createOption( array(
                            'name' => $field_name,
                            'id' => $field_id,
                            'type' => $field_type,
                            'desc' => $field_desc
                        ) );
                    }

                    // TODO: Clean up this logic, and present errors to the user.
                }
            }
        }
    }
}

/**
 * Add ability to filter admin list of uwopeople based on custom taxonomy "Classification"
 */
add_action( 'restrict_manage_posts', 'uwopeople_filter_list' );
function uwopeople_filter_list()
{
    global $wp_query;

    $screen = get_current_screen();

    if ( $screen->post_type == 'uwopeople' )
    {
        $selected = isset($wp_query->query['uwopeople_classification']) ? $wp_query->query['uwopeople_classification'] : NULL;

        wp_dropdown_categories( array(
            'show_option_all' => 'Show All Classifications',
            'taxonomy' => 'uwopeople_classification',
            'name' => 'uwopeople_classification',
            'orderby' => 'name',
            'selected' => $selected,
            'hierarchical' => false,
            'depth' => 3,
            'show_count' => false,
            'hide_empty' => true,
        ) );
    }
}

/**
 * This filter is only necessary for filtering by Classifications.
 */
add_filter( 'parse_query','uwopeople_perform_filtering' );
function uwopeople_perform_filtering( $query )
{
	global $pagenow;

	$post_type = 'uwopeople';
	$taxonomy = 'uwopeople_classification';

	if ($pagenow == 'edit.php' &&
        isset($query->query_vars['post_type']) &&
        $query->query_vars['post_type'] == $post_type &&
        isset($query->query_vars[$taxonomy]) &&
        is_numeric($query->query_vars[$taxonomy]) &&
        $query->query_vars[$taxonomy] != 0)
    {
		$term = get_term_by('id', $query->query_vars[$taxonomy], $taxonomy);
		$query->query_vars[$taxonomy] = $term->slug;
	}
}

/**
 * Add custom "meta boxes" (Loading from ODS and content box)
 */
add_action('add_meta_boxes', 'add_uwopeople_metaboxes');

function add_uwopeople_metaboxes()
{
	global $_wp_post_type_features;

	if ( isset( $_wp_post_type_features['uwopeople']['editor'] ) && $_wp_post_type_features['uwopeople']['editor'] )
    {
		unset( $_wp_post_type_features['uwopeople']['editor'] );
        // add_meta_box( 'uwopeople-ods', 'Populate from the UWO Directory', 'uwopeopleods', 'uwopeople', 'normal', 'high' );
		add_meta_box( 'uwopeople-details', 'Profile Text', 'uwopeopledetails', 'uwopeople', 'normal', 'low' );
	}

}

/**
 * Add meta box into the post type to load data from the Operational Data Store
 */
function uwopeopleods()
{
    global $post;

    if ( file_exists( dirname( __FILE__ ) . '/views/editor.php' ) )
    {
        require dirname( __FILE__ ) . '/views/editor.php';
    }
}

/**
 * Add content box into the post type, but with media buttons disabled.
 */
function uwopeopledetails()
{
    global $post;
    wp_editor( $post->post_content, 'content', array( 'textarea_name' => 'content', 'media_buttons' => true ) );
}

/**
 * Customize the save function for UWO People
 * We need to do this in order to set the title to the first
 * and last name, and in order to set the slug appropriately.
 */
add_action( 'save_post', 'save_uwopeople_meta', 1, 2 );

/**
 * This function sets the title and slug of a UWO People post
 * automatically. This is done on every save, and you currently
 * cannot change the slug without it being overwritten.
 *
 * It is currently saving the slug in the format:
 * lastname-firstname
 *
 * @param $post_id
 * @param $post
 */
function save_uwopeople_meta( $post_id, $post )
{
    global $wpdb;

    // Only run this is the post type is of uwopeople!
    if ( $post->post_type == 'uwopeople' && $post->post_status != 'trash' )
    {
        $titan = TitanFramework::getInstance( 'uwopeople' );

        // Get what is currently saved as the last name and first name
        $postFirstName = $titan->getOption( 'first_name', $post->ID );
        $postLastName = $titan->getOption( 'last_name', $post->ID );

        // Update that with whatever might be getting saved
        if ( array_key_exists( 'uwopeople_first_name', $_POST ) && array_key_exists( 'uwopeople_last_name', $_POST ) )
        {
            $postFirstName = $_POST['uwopeople_first_name'];
            $postLastName = $_POST['uwopeople_last_name'];
        }

        // Generate a title and slug based off of that information
        $title = $postFirstName . ' ' . $postLastName;
        $slug = generate_slug( $post->ID, $postFirstName, $postLastName );

        // Update that information in the database
        $wpdb->update( $wpdb->posts, array(
            'post_title' => $title,
            'post_name' => $slug
        ), array(
            'ID' => $post_id
        ) );
    }
}

/**
 * Make sure that we are not making UWO People profiles that are impossible
 * to get to. We don't care about the title, but the slug is the most important
 * to WordPress. Keep incrementing a number until we can create a people object
 * without clashing with another existing slug in the system.
 *
 * @param $first_name
 * @param $last_name
 * @return string
 */
function generate_slug( $post_id, $first_name, $last_name )
{
    $pre_generated_slug = sanitize_title( $last_name ) . '-' . sanitize_title( $first_name );

    $generated_slug = $pre_generated_slug; // Let's save what we *think* we will use
    $i = 0; // increment this to keep trying new numbers to save in case of dumplicates
    $exists = get_page_by_path( $pre_generated_slug , 'OBJECT', 'uwopeople' );
    while ( is_object( $exists ) && $exists->ID != $post_id )
    {
        $generated_slug = $pre_generated_slug . '-' . ++$i;
        $exists = get_page_by_path( $generated_slug, 'OBJECT', 'uwopeople' );
    }

    return $generated_slug;
}

/**
 * // TODO: Remove this function and hook and utilize the build in wordpress themes so they are more readily available to be overridden.
 *
 * Load paths to custom templates
 * single-uwopeople.php
 * archive-uwopeople.php
 */
add_filter( 'template_include', 'include_uwopeople_template_function', 1 );
function include_uwopeople_template_function( $template_path )
{
    if ( get_post_type() == 'uwopeople' )
    {
        if ( is_single() )
        {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'views/uwopeople-single.php' ) ) )
            {
                $template_path = $theme_file;
            }
            else
            {
                $template_path = plugin_dir_path( __FILE__ ) . '/views/single.php';
            }
        }
        else if ( is_archive() )
        {
            if ( $theme_file = locate_template( array ( 'views/uwopeople-archive.php' ) ) )
            {
                $template_path = $theme_file;
            }
            else
            {
                $template_path = plugin_dir_path( __FILE__ ) . '/views/archive.php';
            }
        }
    }

    return $template_path;
}

/**
 * AJAX code triggered by "Load ODS" button
 */
add_action('wp_ajax_my_action', apply_filters('uwopeople-ajax-override', 'my_action_callback'));
function my_action_callback()
{
	$url = apply_filters('uwopeople-ods-url', 'https://www4.uwo.edu/api/index.cfm?controller=search&action=ods&format=json');

    $args = array(
        'body' => array(
            'epantheridlist' => $_POST['epantheridlist']
        )
    );

    // Handles case where curl is not installed.
    // See docs for the workflow this runs through.
    // https://core.trac.wordpress.org/ticket/4779
    $output = wp_remote_post($url, $args);

    header('Content-type: application/json');
	echo $output['body'];

	die();
}

/**
 * Load custom javascript (jQuery)
 */
add_action( 'admin_enqueue_scripts', 'uwopeople_load_scripts' );
function uwopeople_load_scripts( $hook )
{
    wp_enqueue_style( 'uwopeople-admin', plugins_url( '/style/admin.css', __FILE__ ), array(), null, 'screen' );

    if ( get_post_type() == 'uwopeople' && !( $hook != 'post.php' && $hook != 'post-new.php' ) )
    {
        wp_register_script( 'uwopeople-script', apply_filters('uwopeople-ods-script', plugins_url( '/js/ods.js', __FILE__ )), array( 'jquery' ) );
        wp_enqueue_script( 'uwopeople-script' );
        wp_localize_script( 'uwopeople-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
}

/**
 * Load custom CSS
 */
add_action( 'wp_enqueue_scripts', 'uwopeople_load_styles' );
function uwopeople_load_styles()
{
    wp_register_script( 'filter-bar-js', plugins_url( '/js/filter-bar.js', __FILE__ ), array( 'jquery' ), '', true );

    wp_register_style( 'filter-bar-css', plugins_url( '/style/filter-bar.css', __FILE__ ) );
    wp_register_style( 'uwopeople-style', apply_filters( 'uwopeople-style', plugins_url( '/style/style.css', __FILE__ ) ) );
}

/**
 * Define custom admin menu columns
 */
add_filter( 'manage_edit-uwopeople_columns', 'adding_uwopeople_columns' );
function adding_uwopeople_columns( $columns )
{
    unset( $columns['date'] ); // we want to move date column to the end
    $columns['profile_photo'] = 'Image';
    $columns['uwopeople_email'] = 'Email';
    $columns['uwopeople_job_title'] = 'Position';
	$columns['date'] = 'Date'; // re-add it here
    return $columns;
}

/**
 * Define display of custom admin menu columns
 */
add_action( 'manage_posts_custom_column', 'populating_uwopeople_columns' );
function populating_uwopeople_columns( $column )
{
    $titan = TitanFramework::getInstance( 'uwopeople' );
    $titan->getOption( 'uwopeople_email', get_the_ID() );

    if ( 'profile_photo' == $column)
    {
        $featured_image = $titan->getOption( 'profile_photo', get_the_ID() );
        if ( strpos($featured_image, 'http' ) === false )
            $featured_image = wp_get_attachment_url( $featured_image );

        if ( $featured_image )
        {
            echo '<img width="55" src="' . $featured_image . '" />';
        }
        else
        {
            echo 'No image';
        }
    }
    else
    {
        // TODO: Right here, $titan->getOption() won't work. Don't ask why.
        echo esc_html( get_post_meta( get_the_ID(), $column, true ), true );
    }
}

/**
 * Make custom admin menu columns sortable
 */
add_filter( 'manage_edit-uwopeople_sortable_columns', 'sorting_uwopeople_columns' );
function sorting_uwopeople_columns( $columns )
{
    $columns['uwopeople_email'] = 'uwopeople_email';
    $columns['uwopeople_job_title'] = 'uwopeople_job_title';

    return $columns;
}

/**
 * Defines how to order results when sorting
 */
add_filter( 'request', 'column_orderby' );
function column_orderby ( $vars )
{
    if ( !is_admin() ) return $vars;

    if ( isset( $vars['orderby'] ) && 'uwopeople_email' == $vars['orderby'] )
    {
        $vars = array_merge( $vars, array(
            'meta_key' => 'uwopeople_email',
            'orderby' => 'meta_value'
        ) );
    }
    else if ( isset( $vars['orderby'] ) && 'uwopeople_job_title' == $vars['orderby'] )
    {
        $vars = array_merge( $vars, array(
            'meta_key' => 'uwopeople_job_title',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
}

/**
 * Return the proper image to the user!
 *
 * @param $id Attachment ID
 * @param $titan Titan instance
 * @param $size Size of the image from WordPress, e.g. medium, small, full
 * @param $width Width of the image in pixels, for the <img> tag
 * @param $alt Alt text for the image
 * @return mixed image URL or image tag
 */
function uwopeople_get_featured_image( $id, $titan, $size = '' ) {

    $featured_image = $titan->getOption('profile_photo', $id);

    // Do we not have a profile photo set? Let's try our default image
    if ( empty( $featured_image ) ) {
        $featured_image = $titan->getOption('default_image');
    }

    // Associate that with an image attachment if necessary
    if ( strpos( $featured_image, 'http' ) === false ){
        $featured_image = wp_get_attachment_image_src( $featured_image, $size )[0];
    }

    // Did that not work? Then grab the plugin default image
    if ( empty( $featured_image ) ) {
        $featured_image = plugins_url('images/profile-default.jpg', __FILE__);
    }

    return $featured_image;
}

/**
 * Return the needed options in order to generate the page how the
 * user would like.
 *
 * @return array
 */
function uwopeople_get_plugin_options ()
{
    $titan = TitanFramework::getInstance( 'uwopeople' );

    /**
     * Get our default options from the Titan Framework
     */

    $options = array(
        'order_dir'         => $titan->getOption( 'order_dir' ),
        'order_by'          => $titan->getOption( 'order_by' ),
        'display_name'      => $titan->getOption( 'display_name' ),
        'directory_layout'  => $titan->getOption( 'directory_layout' ),
        'custom_fields'     => $titan->getOption( 'custom_fields' ),
        'default_image'     => $titan->getOption( 'default_image' )
    );

    return array($titan, $options);
}

/**
 * Return instance of WP_Query
 * used when you want to get all users.
 *
 * Need to call uwopeople_get_plugin_options() before running.
 *
 * @param $options
 * @return WP_Query
 */
function uwopeople_get_all_query( $options)
{
    $meta_query = array(
        'relation' => 'AND',
        array( 'key' => 'uwopeople_last_name' ),
        array( 'key' => 'uwopeople_first_name' )

    );

    // If we aren't ordering by one of the allowed ones, that must mean we are
    // ordering by a custom field. That means we need to add that into the meta query.
    if ( $options['order_by'] != 'uwopeople_last_name' && $options['order_by'] != 'uwopeople_first_name' )
    {
        $custom_fields = explode( PHP_EOL, preg_replace( "/[\r\n]+/", "\n", $options['custom_fields'] ) );
        if ( is_array( $custom_fields ) && !empty( $custom_fields ) )
        {
            foreach ( $custom_fields as $custom_field )
            {
                $field = explode( '|', $custom_field );
                $field = sprintf( 'uwopeople_%s', trim( $field[0] ) );

                if ( $options['order_by'] == $field )
                    $meta_query[] = array( 'key' => $field );
            }
        }
    }
    /*else if ( $options['order_by'] != 'uwopeople_last_name' )
    {
        $options['order_by'] = array('uwopeople_last_name','uwopeople_first_name');
    }
    else if( $options['order_by'] != 'uwopeople_first_name' )
    {
        $options['order_by'] = array('uwopeople_first_name','uwopeople_last_name');
    }*/

    $query = array(
        'post_type'                 => 'uwopeople',
        'post_status'               => 'publish',
        'posts_per_page'            => -1,
        'orderby'                   => 'meta_value',
        'meta_query'                => $meta_query,
        'order'                     => $options['order_dir'],
        'meta_key'                  => $options['order_by'],
        'uwopeople_classification'  => (isset($options['filter_cat'])) ? $options['filter_cat'] : "",
    );

    return new WP_Query( $query );
}