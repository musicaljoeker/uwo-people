<?php

wp_enqueue_style( 'uwopeople-style' );

/*
This is a template part that is used in the single-uwopeople template.
This was abstracted out in order to display the same HTML using the built-in
pages, or via a shortcode that we registered.

Honestly, a lot of this are not part of views and shouldn't be in here
but alas, this is how it is.
*/

/**
 * Load in the Titan Framework
 * &
 * Get our default options from the Titan Framework
 */

list($titan, $options) =  uwopeople_get_plugin_options();

// Must use this for shortcodes to function outside of the loop.
$post_id = $post->ID;

/**
 * This is used for shortcode overrides of options
 * that are set globally or whatever.
 */
$display_name = $titan->getOption('display_name');

if ( isset( $display_name ) )
{
    if ( isset( $display_name ) && $display_name != null )
    {
        $options['display_name'] = $display_name;
    }
}

$first_name = esc_html( $titan->getOption( 'first_name', $post_id ), true );
$last_name = esc_html( $titan->getOption( 'last_name', $post_id ), true );
$full_name = $last_name . ', ' . $first_name;

if ( $options['display_name'] == 'firstlast' )    $full_name = $first_name . ' ' . $last_name;
else if ( $options['display_name'] == 'first' )   $full_name = $first_name;

$the_loop = '';

if ( $is_shortcode === true )
	$the_loop .= '<h2 class="entry-title-shortcode">' . $full_name . '</h2>';
else
	$the_loop .= '<h1 class="entry-title">' . $full_name . '</h1>';

$the_loop .= '<div class="single-person-container">';
$the_loop .= '<div class="uwopeople-details">';

// By default, let's try to get the profile photo. This may return an integer
// or a URL. Thanks WordPress. If profile photo is empty, then we set it to
// default image, which ALSO might be an integer or a URL. Hopefully this covers all cases.
$featured_image = uwopeople_get_featured_image( $post_id, $titan, '' );

$the_loop .= '<div class="uwopeople-image"><img alt="profile photo" src="' . $featured_image . '" width="200" /></div>';

$the_loop .= '<div class="uwopeople-contact">';

if (has_term('', 'uwopeople_classification'))
{
    $the_loop .= get_the_term_list($post_id, 'uwopeople_classification', '', ', ');
}

$email = esc_html( $titan->getOption( 'email', $post_id ), true );
if ( !empty($email) ) $the_loop .= '<a class="uwopeople-email" href="mailto:' . $email . '">' . $email . '</a>';

$phone = esc_html( $titan->getOption( 'phone', $post_id ), true );
if ( !empty($phone) ) {
  $phone_numeric = preg_replace("/[^0-9,.]/", "", $phone );
  $the_loop .= '<div class="uwopeople-phone"><a href="tel:' . $phone_numeric . '">' . $phone . '</a></div>';
}

$building = esc_html( $titan->getOption( 'building', $post_id ), true );
$room = esc_html( $titan->getOption( 'room', $post_id ), true );
if ( !empty($building) ) $the_loop .= $building . ' ' . $room;

$the_loop .= '</div>';
$the_loop .= '</div><!-- .uwopeople-details -->';

$the_loop .= '<div class="uwopeople-bio">';

$job_title = esc_html( $titan->getOption( 'job_title', $post_id ), true );
$the_loop .= '<div class="uwopeople-jobtitle">' . $job_title . '</div>';

$department = esc_html( $titan->getOption( 'department', $post_id ), true );
$the_loop .= '<div class="uwopeople-department">' . $department . '</div>';

$the_loop .= '<p>' . wpautop(do_shortcode($post->post_content)) . '</p>';

$custom_fields = preg_replace("/[\r\n]+/", "\n", $titan->getOption('custom_fields'));
$split = explode( PHP_EOL, $custom_fields );
if ( is_array( $split ) && !empty( $custom_fields ) )
{
	foreach ( $split as $data )
	{
		$field = explode( '|', $data );
		$the_loop .= '<div class="uwopeople-' . $field[0] . '">';
		if ( count($field) > 1 ) // This means we defined our own in Titan
		{
			$the_loop .= wpautop($titan->getOption( $field[0], $post_id )) . '<br />';
		}
		else // This means we reach out and find one named this
		{
			$the_loop .= wpautop(get_post_meta( $post_id, $field[0], true )) . '<br />';
		}
		$the_loop .= '</div>';
	}
}

$the_loop .= '</div>'; // .uwopeople-bio
$the_loop .= '</div>'; // .person-container

wp_reset_query();
