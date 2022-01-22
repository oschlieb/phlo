$(document).ready(function() {

	// on focus select all
	var selectAll = function() { 
		var save_this = $(this);
	    window.setTimeout (function(){ 
	       save_this.select(); 
	    },100);
	};
	$('#password_new').focus(selectAll);
	$('#password_repeat').focus(selectAll);

    // update hidden field content
	$('.onchange').change(function() {
  		 updatehiddenfield($(this).attr('name'));
	});
	function updatehiddenfield(fieldname) {
  		var input = $('#validate');
		input.val(input.val() + fieldname + ',');	
	}
	
	// delete profile (sidebar)
	$('.delete').on('click', function() {		
		if (confirm('Are you sure you want to delete your profile? Can\'t undo.')) {
			deleteprofiledb();
			//return true;	
    	}
    	return false;    
	});
	
	// delete profile from database
	function deleteprofiledb() {
		var customer_id = $('#delete-customer').val();
		var redirect = $('#redirect').val();
    	$.ajax ({
    		type: 'POST',
    		url: '../../ajax/deleteprofile',
        	data: "{name:"+customer_id+"}",
            success: function (result) {
                //window.location.replace(redirect);
                console.log(result);
            },
            error: function (result) {  
             	console.log(result);              
            }
        });
	}
	
});
