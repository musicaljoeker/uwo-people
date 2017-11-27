<?php 

class UWOPeopleSettingsPage
{
    /**
     * Initialize admin option page using Titan Framework
     */
    public function __construct() 
    {
        add_action( 'tf_create_options', array( $this, 'create_uwopeople_options' ) );
    }

    function create_uwopeople_options()
    {
        $titan = TitanFramework::getInstance( 'uwopeople' );

        $panel = $titan->createAdminPanel( array(
            'name' => 'People Settings',
            'parent' => 'edit.php?post_type=uwopeople',
        ) );

        $settingsTab = $panel->createTab( array(
            'name' => 'Settings',
        ) );

        $settingsTab->createOption( array(
            'name' => 'Default Image',
            'id' => 'default_image',
            'type' => 'upload',
            'default' => plugins_url('images/profile-default.jpg', dirname(__FILE__)),
            'desc' => 'The default image if a person does not have a profile picture.',
        ) );

        $settingsTab->createOption( array(
            'name' => 'People Directory Path',
            'id' => 'people_path',
            'type' => 'text',
            'desc' => 'This is the URL to the People Directory, normally "people"',
            'placeholder' => 'people',
        ) );

        $settingsTab->createOption( array(
            'name' => 'Order By',
            'id' => 'order_by',
            'type' => 'select',
            'desc' => 'Which field would you like to sort by? By default, we sort by last name.',
            'options' => array(
                'uwopeople_last_name' => 'Last Name',
                'uwopeople_first_name' => 'First Name'
            ),
            'default' => 'uwopeople_last_name',
        ) );
        
        $settingsTab->createOption( array(
            'name' => 'Order Direction',
            'id' => 'order_dir',
            'type' => 'select',
            'desc' => 'When ordering by the field above, whether or not is is Ascending (A-Z) or Descending (Z-A). By default, we sort Ascending.',
            'options' => array(
                'ASC' => 'Ascending',
                'DESC' => 'Descending'
            ),
            'default' => 'ASC',
        ) );

        $settingsTab->createOption( array(
            'name' => 'Name Display',
            'id' => 'display_name',
            'type' => 'select',
            'desc' => 'How you would like to display names in the Directory and Profile page. By default, we display "Last, First"',
            'options' => array(
                'lastfirst' => 'Last, First',
                'firstlast' => 'First Last',
                'first' => 'First',
            ),
            'default' => 'lastfirst',
        ) );
        
        $settingsTab->createOption( array(
            'name' => 'People Directory Layout',
            'id' => 'directory_layout',
            'type' => 'select',
            'desc' => 'How you would like to display people in the Directory page. By default, we display "Grid or List Toggle" ',
            'options' => array(
                'gridlist' => 'Grid or List Toggle (Default)',
                'gridonly' => 'Grid Only',
                'listonly' => 'List Only',
                
            ),
            'default' => 'gridlist',
        ) );

        $settingsTab->createOption( array(
            'name' => 'Link to Profile Pages',
            'id' => 'disable_links',
            'type' => 'radio',
            'options' => array 
            (
                'false' => 'True',
                'true' => 'False'
            ),
            'desc' => 'If you\'d like to have links from the Directory to the Profile pages, set this option to true.<br/>If you want to disable the links set this to False.',
            'default' => 'false'
        ) );

        $settingsTab->createOption( array(
            'name' => 'Custom Fields',
            'id' => 'custom_fields',
            'type' => 'textarea',
            'desc' => 'If you\'d like to add custom fields to your people profiles, define them below. Accepted values for <em>type</em> are "text" or "textarea".<br /><br />If you want more advanced fields, try using the Advanced Custom Fields plugin and reference them below by just entering in the slug of the custom field.<br /><br /> <em>Example: field_name|type|Field Description</em>',
            'is_code' => true,
            'placeholder' => 'field_name|type|Field Description'
        ) );

        $settingsTab->createOption( array(
            'type' => 'save'
        ) );

        $shortcodesTab = $panel->createTab( array(
            'name' => 'Shortcodes',
        ) );

        $shortcodesTab->createOption( array(
            'name' => 'Provided Shortcodes',
            'type' => 'heading',
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => '[uwopeople]',
            'desc' => 'Just using this shortcode will display the People Directory wherever it is placed.'
        ) );

        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => '[uwofilterbar]',
            'desc' => 'Using this shortcode will display the People Filter wherever it is placed. To be used in conjunction with [uwopeople] shortcode(s).'
        ) );
        $shortcodesTab->createOption( array(
            'name' => 'Parameters for [uwopeople]',
            'type' => 'heading',
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'person="value"',
            'desc' => 'Displays the person\'s profile. Specify the <em>slug</em> of the person, i.e. <strong>smith-john</strong>'
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'classification="value"',
            'desc' => 'Displays the People Directory, filtered by classification. Specify the <em>slug</em> of the classification, i.e. <strong>ft-staff</strong> instead of "Full Time Staff"'
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'order_by="value"',
            'desc' => 'Allows you to change between <strong>firstname</strong> or <strong>lastname</strong> to order the directory by.'
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'order_dir="value"',
            'desc' => 'Allows you to change between <strong>asc</strong> or <strong>desc</strong> in regards to which direction (Ascending or Descending) to order the directory by.'
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'display_name="value"',
            'desc' => 'Allows you to change between ways of displaying your name. Options are "Last, First" <strong>(lastfirst)</strong>, "First Last" <strong>(firstlast)</strong>, or simply First <strong>(first)</strong>.'
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'layout="value"',
            'desc' => 'Allows you to change between list view and grid view. Options are <strong>gridlist</strong>, <strong>gridonly</strong>, or <strong>listonly</strong>.'
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'disable_links="true"',
            'desc' => 'The people directory created will not link to the profile pages.'

        ) );

        $shortcodesTab->createOption( array(
            'name' => 'Useage for [uwofilterbar]',
            'type' => 'heading',
        ) );
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'Useage',
            'desc' => 'The filter bar will work on all divs with a class of <code>people-container</code>, if you want the shortcode to act on a group of shortcodes, you must wrap the group in a div with the class <code>people-container</code>'
        ) );                
        $shortcodesTab->createOption( array(
            'type' => 'note',
            'name' => 'Example',
            'desc' => '[uwofilterbar]<br/>&lt;div class="people-container"&gt;[uwopeople <strong>...</strong>][uwopeople <strong>...</strong>]&lt;/div&gt;'
        ) );

    }
}

new UWOPeopleSettingsPage();
