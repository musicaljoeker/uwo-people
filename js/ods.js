(function($) {
	$(function() {
		
	    $("#button-ods").click(function() {

	    	var qval = $('input[name=uwopeople_epantherid]').val(),
	    	qlen = qval.length;
	    	
	    	if(qval != '') {
				
				if(qval.substring(qlen-8,qlen) == '@uwo.edu') {
					qval = qval.substring(0, qlen-8);
				}
								
				var data = {
					action: 'my_action',
					epantheridlist: qval
				};
				
				$.post(ajax_object.ajax_url, data, function(response) {
					var message = response.MSG;
					
					if(message == 'Success') {
						var results = response.RESULT[0];
						
						$('input[name=uwopeople_email]').val(results.mail);
						$('input[name=uwopeople_first_name]').val(results.givenname);
						$('input[name=uwopeople_last_name]').val(results.sn);
						
						if(results.telephonenumber != '') {
							var plen = results.telephonenumber.length,
							pval = results.telephonenumber;
							
							if(pval.substring(4, 5) == ')' && pval.substring(5, 6) != ' ') {
								pval = pval.substring(0, 5) + ' ' + pval.substring(5, plen);
							}
							
							$('input[name=uwopeople_phone]').val(pval);
						}
						
						$('input[name=uwopeople_job_title]').val(results.title);
						$('input[name=uwopeople_department]').val(results.appointingdepartment);
						$('input[name=uwopeople_building]').val(results.buildingname);
						$('input[name=uwopeople_room]').val(results.roomnumber);
						
					} else {
						alert('No details were found for this ePantherID');
					}
				});	
			
	    	} else {
	    		alert('Please enter an ePantherID');
	    	}
	    	
	    });   

	});
})(jQuery);