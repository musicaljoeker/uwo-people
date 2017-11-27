<?php

// Sanitize the order_by option in the shortcodes
function sanitize_orderby( $order_by )
{
    $titan = TitanFramework::getInstance( 'uwopeople' );

    $accepted = array(
        'uwopeople_first_name',
        'uwopeople_last_name',
    );

    // Let's normalize the input
    $order_by = strtolower( trim( $order_by ) );

    // We need to rewrite these, since we let the end users have 'friendly' options in the shortcode
    if ( $order_by == 'lastname' ) $order_by = 'uwopeople_last_name';
    else if ( $order_by == 'firstname' ) $order_by = 'uwopeople_first_name';
    else $order_by = sprintf( 'uwopeople_%s', $order_by );

    // We also want to handle custom fields. Since we allow both Titan-defined ones
    // and the usage of ones from plugins like Advanced Custom Fields we need to iterate.
    $custom_fields = explode( PHP_EOL, preg_replace( "/[\r\n]+/", "\n", $titan->getOption( 'custom_fields' ) ) );
    if ( is_array( $custom_fields ) && !empty( $custom_fields ) )
    {
        foreach ( $custom_fields as $custom_field )
        {
            $field = explode( '|', $custom_field );
            
            $accepted[] = sprintf( 'uwopeople_%s', trim( $field[0] ) );
        }
    }
    
    return ( !in_array( $order_by, $accepted ) ) ? null : $order_by;
}

// Sanitize the order_dir option in the shortcodes
function sanitize_orderdir( $order_dir )
{
    $accepted = array( 'asc', 'desc' );
    $order_dir = strtolower( $order_dir );

    return ( !in_array( $order_dir, $accepted ) ) ? null : $order_dir;
}

// Sanitize the display_name option in the shortcodes
function sanitize_displayname($displayname)
{
    $accepted = array('firstlast', 'lastfirst', 'first');
    $displayname = strtolower($displayname);

    return ( !in_array( $displayname, $accepted ) ) ? null : $displayname;
}

// Add in the ability to display the people directory on any page
function uwopeople_func( $atts ) 
{
    extract( shortcode_atts( array(
        'classification' => null,
        'person' => null,
        'order_by' => null,
        'order_dir' => null,
        'display_name' => null,
        'disable_links' => false,
        'layout'=>'gridlist'
    ), $atts ) );

    // Sanitize our attributes
    $atts['order_by'] = ( isset( $atts['order_by'] ) ) ? sanitize_orderby( $atts['order_by'] ) : null;
    $atts['order_dir'] = ( isset( $atts['order_dir'] ) ) ? sanitize_orderdir( $atts['order_dir'] ) : null;
    $atts['display_name'] = ( isset( $atts['display_name'] ) ) ? sanitize_displayname( $atts['display_name'] ) : null;
    $disable_links = (isset($atts['disable_links']) && $atts['disable_links'] === 'true' ? true : false);
    

    // We want to call either the person or directory partial, depending on
    // what shortcode attributes we were provided.
    if ( isset( $atts['person'] ) ) 
    {
        if ( file_exists( plugin_dir_path( __FILE__ ) . '/../views/partials/person.php' ) ) 
        {
            uwopeople_load_styles( true );

            $person_post = get_posts( array(
                'name' => $atts['person'], 
                'post_type' => 'uwopeople'
            ) );

            if ( count($person_post) == 0 )
                return 'Sorry, the person "' . $atts['person'] . '" was not found.';
            
            $post = $person_post[0];
            $is_shortcode = true;

            // Include the person partial. This will result in a $return variable
            // that includes all of the HTML to be output.
            include( plugin_dir_path( __FILE__ ) . '/../views/partials/person.php' );

            // we have to do it this way, so that the returned information
            // goes where the shortcode is, not randomly in the page.
            return $the_loop;
        }
    } 
    else 
    {
        if ( file_exists( plugin_dir_path( __FILE__ ) . '/../views/partials/directory.php' ) ) 
        {
            uwopeople_load_styles( true );

            // Include the directory partial. This will result in a $return variable
            // that includes all of the HTML to be output.
            include( plugin_dir_path( __FILE__ ) . '/../views/partials/directory.php' );

            // we have to do it this way, so that the returned information
            // goes where the shortcode is, not randomly in the page.
            return $the_loop;
        }
    }
}

// Add in the ability to display the people directory on any page
function uwopeople_filterbar( $atts ) 
{
  include( plugin_dir_path( __FILE__ ) . '/../views/partials/filter_bar.php');
  return $uwo_filter;
}

add_shortcode( 'uwopeople', 'uwopeople_func' );
add_shortcode( 'uwofilterbar','uwopeople_filterbar');
