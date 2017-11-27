( function( $ ) {
	
	$('.filter-bar').css('display','block');
	
    /* Check if filter-parent div exists, adjust layout accordingly */
    if($('div.filter-parent').length == 0) {
        $('.filter-parent').hide();
        $('.filter-list').first().show();
        $('.filter-list li').first().addClass('filter-item-active');    
    }
	
	/* Toggle grid/list view */ 
	$('.view li').click(function(e){
		e.preventDefault();
		v = $(this);
		v.siblings('.view-active').removeClass('view-active');
		v.addClass('view-active');
	})
	
	/* Set defaults */
	$('.filter-parent li').first().addClass('filter-item-active');
	
	/* Expand/collapse filter panel */
	$('.filter').click(function(e){
		e.preventDefault();
		f = $(this);
		
		f.toggleClass('filter-active');
		$('.filter-panel').toggle();		
	})
	
	/* If setting is "list only", run listview function */
	if ($('.list')[0]) { 
		listView();
	}
	
	/* List view functionality */
	function listView(){
		$('.person-container').removeClass('grid').addClass('list');
		$('.list .person-hidden').show();
		$('.list .person-hidden span').each(function() {
			$(this).hide();
		});
		$('.list .person-hidden .uwopeople-department').show();
		$('.list .person-hidden .uwopeople-phone').show();
		$('.list .person-hidden .uwopeople-email').show();
		$('.list .person-hidden .uwopeople-building').show();
	};	
	
	/* Tabbed format. */
	$('.tab').click(function(e){
	   	e.preventDefault();
	    
	    /* Reset all filter items. */
	    $('.item').removeClass('filter-item-active');
	    
	    url = $('a', this).attr('href');
	    tab = $(this);
	    list = 'list-' + tab.attr('id');
	    
	    $('.tab').removeClass('filter-item-active');
	    tab.addClass('filter-item-active');
	    
	    $('.filter-list').removeClass('filter-list-active');
	    $('#'+list).addClass('filter-list-active');
	    
	    $('input.search-field-people').val('');
	    $('div.cancel-search').hide();
	    
	    if($('.list').length > 0) { 
	    	$('.person-container').load(url + ' .wrapper',
	    		function() {
	    			listView();
	    		} 
	    	);
	    } else {
	    	$('.person-container').load(url + ' .wrapper');
	    }
	})
	
	/* Toggle list items */ 
	$('.item').click(function(e){
	    e.preventDefault();	    
	    
	    url = $('a', this).attr('href');
	    var item = $(this);
	    
	    $('.item').removeClass('filter-item-active');
	    item.addClass('filter-item-active');
	    
	    $('input.search-field-people').val('');
	    $('div.cancel-search').hide();	    
	    
	    if($('.list').length > 0) { 
	    	$('.person-container').load(url + ' .wrapper', 
	    		function() {
	    			listView();	
	    		}
	    	); 
	    } else {
	    	$('.person-container').load(url + ' .wrapper');
	    }
	})
	 
	/* Keyword search */
	$.expr[":"].contains = $.expr.createPseudo(function(arg) {
	    return function( elem ) {
	        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
	    };
	});	
	
	/* check if browser is chrome */
	var is_chrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase()); 
	
	$('.search-field-people').keydown(function() {
		clearTimeout($.data(this, 'timer'));
		var wait = setTimeout(function(){
			var q = $('input.search-field-people').val();
			
			if( q != '' && q != 'Search...' ){
				if(!is_chrome) {
					$('div.cancel-search').show();
				}
				
				/* search extra fields if in "List" view */
				if($('.list').length > 0) { 
					$('.person-name:not(:contains("'+q+'"))').parents('.person').hide();
					$('.person-title:not(:contains("'+q+'"))').parents('.person').hide();
					$('.uwopeople-department:not(:contains("'+q+'"))').parents('.person').hide();
					$('.uwopeople-phone:not(:contains("'+q+'"))').parents('.person').hide();
					$('.uwopeople-email:not(:contains("'+q+'"))').parents('.person').hide();
					$('.uwopeople-building:not(:contains("'+q+'"))').parents('.person').hide();
					$('.person-name:contains("'+q+'")').parents('.person').show();
					$('.person-title:contains("'+q+'")').parents('.person').show();
					$('.uwopeople-department:contains("'+q+'")').parents('.person').show();
					$('.uwopeople-phone:contains("'+q+'")').parents('.person').show();
					$('.uwopeople-email:contains("'+q+'")').parents('.person').show();
					$('.uwopeople-building:contains("'+q+'")').parents('.person').show();
				} else {
					$('.person-name:not(:contains("'+q+'"))').parents('.person').hide();
					$('.person-title:not(:contains("'+q+'"))').parents('.person').hide();
					$('.person-name:contains("'+q+'")').parents('.person').show();
					$('.person-title:contains("'+q+'")').parents('.person').show();
				}
			} else {
				$('.person').show();
				$('div.cancel-search').hide();
			}
		}, 50);
		$(this).data('timer', wait);
	});	
	
	/* Cancel search button */
	if(!is_chrome) {
		$('div.cancel-search').click(function() {
			$('input.search-field-people').val('');
			$('div.cancel-search').hide();
			$('.person').show();
			if($('.grid').length > 0) {
				$('.person').each(function() {
					$(this).css('display', 'inline-block');
				});
			}
		});
	} else {
		$('.search-field-people').on('input', function(e) {
			if(this.value == '') {
				$('.person').show();
				if($('.grid').length > 0) {
					$('.person').each(function() {
						$(this).css('display', 'inline-block');
					});
				}
			}
		});
	}
	
	/* Grid view functionality */
	$('.filter-bar li.view-grid').click(function(){

		var search_string = $('.search-field-people').val(),
		search_length = search_string.length;
		
		$('.person-container').removeClass('list').addClass('grid');
		$('.search-field').trigger('keydown');
		$('.grid .person-hidden').hide();
		
		/* correct styles applied to List view */
		$('.person').each(function() {
			if(search_length) {
				$(this).not(':hidden').css('display', 'inline-block');
			} else {
				$(this).css('display', 'inline-block');
			}
		});
		
	});
	
	/* List view clicked */
	$('.filter-bar li.view-list').click(function(){
		listView();
	});		
	
	/* 
	this code is needed in cases where a user is linking directly to a specific classification of people, this code will auto-select the appropriate filter-bar options 	
	*/
	if (window.location.pathname.indexOf('/classification/') >= 0) {
		var requested_url = window.location.pathname,
		url_classifications = requested_url.substr(requested_url.indexOf('/classification/')+16, requested_url.length),
		tabs_needed = 0;
		
		$('.filter').toggleClass('filter-active');
		$('.filter-panel').toggle();
		
		$('.filter-panel .tab').each(function() {
			anchor_tag = $('a', this);
			url = anchor_tag.attr('href').substr(anchor_tag.attr('href').indexOf('/classification/')+16, anchor_tag.attr('href').length);
			
			if(requested_url.indexOf(url) >= 0) { 
				tabs_needed = 1;
			    tab = $(this);
			    list = 'list-' + tab.attr('id');
			    
			    $('.tab').removeClass('filter-item-active');
			    tab.addClass('filter-item-active');
			    
			    $('.filter-list').removeClass('filter-list-active');
			    $('#'+list).addClass('filter-list-active');	
			    
				$('.filter-panel .item').each(function() {
					anchor_tag = $('a', this);
					url_child = anchor_tag.attr('href').substr(anchor_tag.attr('href').indexOf('/classification/')+16, anchor_tag.attr('href').length);

					if(requested_url.indexOf(url_child) >= 0) { 
					    var item = $(this);
					    
					    $('.item').removeClass('filter-item-active');
					    item.addClass('filter-item-active');
					} 
				});	
			} 
		});	

		if(!tabs_needed) {
				
			$('.filter-panel .item').each(function() {
				anchor_tag = $('a', this);
				url = anchor_tag.attr('href').substr(anchor_tag.attr('href').indexOf('/classification/')+16, anchor_tag.attr('href').length);

				if(requested_url.indexOf(url) >= 0) { 
				    var item = $(this);
				    
				    $('.item').removeClass('filter-item-active');
				    item.addClass('filter-item-active');
				} 
			});
		}	
	}	

} )( jQuery );
