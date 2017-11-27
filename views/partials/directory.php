<?php

wp_enqueue_style( 'uwopeople-style' );

?>

<?php

/*
This is a template part that is used in the archive-uwopeople template.
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
$people_archive_listing = $titan->getOption('directory_layout', get_the_ID());

/**
 * This is used for shortcode overrides of options
 * that are set globally or whatever.
 */
if ( isset( $atts ) )
{
    if ( isset($atts['classification']) && $atts['classification'] != null )
    {
        $options['filter_cat'] = $atts['classification'];
    }
    if ( isset($atts['order_by']) && $atts['order_by'] != null )
    {
        $options['order_by'] = $atts['order_by'];
    }
    if ( isset($atts['order_dir']) && $atts['order_dir'] != null )
    {
        $options['order_dir'] = $atts['order_dir'];
    }
    if ( isset($atts['display_name']) && $atts['display_name'] != null )
    {
        $options['display_name'] = $atts['display_name'];
    }
    if ( isset($atts['layout']) && $atts['layout'] != null )
    {
        $people_archive_listing = $atts['layout'];
    }
}

//RDK sets the "term" (classification) to be filtered by in the $options object
if(isset($term) && !isset($options['filter_cat'])) {
	if(is_object($term)) { $options['filter_cat'] = $term->slug; } else { $options['filter_cat'] = ''; }
}

// TODO: Add in the ability to sort by custom fields

/**
 * Construct our query for WordPress. Why do it all separately
 * when we can do the logic before the query? Smart.
 */
$loop = uwopeople_get_all_query($options);

/**
 * Lets go through the loop, and build the HTML structure.
 */
$the_loop = '';

while ( $loop->have_posts() ) : $loop->the_post();

    $url_to_use = get_permalink();

    $the_loop .= '<div class="person">';

    if(!isset($disable_links) || !$disable_links)
    {
        $the_loop .= '<a href="' . $url_to_use . '">';
    }
    else
    {
        $the_loop .= '<a target="' . $url_to_use . '">';
    }

    // By default, let's try to get the profile photo. This may return an integer
    // or a URL. Thanks WordPress. If profile photo is empty, then we set it to
    // default image, which ALSO might be an integer or a URL. Hopefully this covexrs all cases.
	$the_loop .= '<img alt="profile photo" src="' . uwopeople_get_featured_image( get_the_ID(), $titan, 'medium' ) . '" width="150" />';

    $first_name = esc_html( $titan->getOption( 'first_name', get_the_ID() ), true );
    $last_name = esc_html( $titan->getOption( 'last_name', get_the_ID() ), true );
    $full_name = $last_name . ',&nbsp;' . $first_name;

    if ( $options['display_name'] == 'firstlast' )    $full_name = $first_name . ' ' . $last_name;
    else if ( $options['display_name'] == 'first' )   $full_name = $first_name;

	//RDK removed div with class "person-name-title" (didn't see any references to it) added span with class "person-meta"
    $the_loop .= '<span class="person-meta">';
    $the_loop .= '<span class="person-name">' . $full_name . '</span>';

    $job_title = esc_html( $titan->getOption( 'job_title', get_the_ID() ), true );
    if ( !empty($job_title) ) $the_loop .= '<span class="person-title">' . $job_title . '</span>';


        $the_loop .= '</a>';

    $the_loop .= '<span class="person-hidden">';

    $department = esc_html( $titan->getOption( 'department', get_the_ID() ), true );
    //RDK added else clause that creates a span with a non-breaking space (for list view)
    //if ( !empty($department) ) $the_loop .= '<span class="uwopeople-department">' . $department . '</span>';
    if ( !empty($department) ) {
    	$the_loop .= '<span class="uwopeople-department">' . $department . '</span>';
    } else {
    	$the_loop .= '<span class="uwopeople-department"></span>';
    }

    $phone = esc_html( $titan->getOption( 'phone', get_the_ID() ), true );
    //RDK added else clause that creates a span with a non-breaking space (for list view)
    //if ( !empty($phone) ) $the_loop .= '<span class="uwopeople-phone">' . $phone . '</span>';
    if ( !empty($phone) ) {
      $phone_numeric = preg_replace("/[^0-9,.]/", "", $phone );
    	$the_loop .= '<span class="uwopeople-phone"><a href="tel:' . $phone_numeric . '">' . $phone . '</a></span>';
    } else {
    	$the_loop .= '<span class="uwopeople-phone"></span>';
    }

    $email = esc_html( $titan->getOption( 'email', get_the_ID() ), true );
    //RDK added else clause that creates a span with a non-breaking space (for list view)
    //if ( !empty($email) ) $the_loop .= '<span class="uwopeople-email"><a href="mailto:' . $email . '">' . $email . '</a></span>';
    if ( !empty($email) ) {
    	$the_loop .= '<span class="uwopeople-email"><a href="mailto:' . $email . '">' . $email . '</a></span>';
    } else {
    	$the_loop .= '<span class="uwopeople-email"></span>';
    }

    $building = esc_html( $titan->getOption( 'building', get_the_ID() ), true );
    $room = esc_html( $titan->getOption( 'room', get_the_ID() ), true );
    //RDK added else clause that creates a span with a non-breaking space (for list view)
    //if ( !empty($building) ) $the_loop .= '<span class="uwopeople-building">' . $building . ' ' . $room . '</span>';
    if ( !empty($building) ) {
    	$the_loop .= '<span class="uwopeople-building">' . $building . ' ' . $room . '</span>';
    } else {
    	$the_loop .= '<span class="uwopeople-building"></span>';
    }

    $custom_fields = preg_replace("/[\r\n]+/", "\n", $titan->getOption('custom_fields'));
    $split = explode(PHP_EOL, $custom_fields );
    if ( is_array( $split ) && !empty( $custom_fields ) )
    {
        foreach ( $split as $data )
        {
			$field = explode( '|', $data );
			$the_loop .= '<span class="uwopeople-' . trim($field[0]) . '">';
			if ( count($field) > 1 ) // This means we defined our own in Titan
			{
				$the_loop .= wpautop($titan->getOption( $field[0], get_the_ID() ));
			}
			else // This means we reach out and find one named this
			{
				$the_loop .= wpautop(get_post_meta( get_the_ID(), trim($field[0]), true ));
			}
			$the_loop .= '</span>';
        }
    }
    // TODO: Add in additional custom fields here, with special cases for website, date, etc.
    $the_loop .= '</span></span></div>';

endwhile;

//Add list class for "List Only" view
if (isset($people_archive_listing) && $people_archive_listing == "listonly" ) {
    $the_loop = '<div class="person-container list"><div class="wrapper">' . $the_loop . '</div></div> <!-- This is the end -->';
} else {
    $the_loop = '<div class="person-container"><div class="wrapper">' . $the_loop . '</div></div> <!-- This is the end -->';
}

/**
 * Important! If you do not call wp_reset_query() you will cause
 * other items on the page to freak out.
 */

wp_reset_query();

?>
