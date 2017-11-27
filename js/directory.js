// TODO: Write JS lib.

var directory = {

    // Get needed JSON from API
    // This actually does a POST
    find: function($key, $value) {
        jQuery.post(
            ajax_object.ajax_url,
            {
                'action': 'get_uwopeople',
                'data':  ['uwopeople_job_title', 'IS Tech Srv Senior', 'LIKE']
            },
            function(response){
                console.log(response);
            }
        );
    }

};
