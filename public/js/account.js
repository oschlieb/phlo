$(document).ready(function() { 						   

    // ordering
    $('#orderby').on('change', function() {
    	window.location = '/account/orderby/' + escape($(this).val());
    });
        
    // search
    $('#search').submit(function(e) {
		e.preventDefault();
		window.location = '/account/searchfor/' + escape($('#search-text').val());
	});
		
	// delete
	$('.delete').on('click', function() {		
		if (confirm('Are you sure you want to delete this? Can\'t undo.')) {
			return true;
    	}
    	return false;    
	});
	
});	