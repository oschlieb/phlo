$(document).ready(function() { 						   
	
	/*-------------------------------------------------------------------------------
	Common functions
	-------------------------------------------------------------------------------*/
	
	// select all text in input[] field
	var selectAll = function() { 
		var save_this = $(this);
	    window.setTimeout (function(){ 
	       save_this.select(); 
	    },100);
	};
	$('.title').focus(selectAll);
	$('.subtitle').focus(selectAll);

    //select change for phlo types    
    $('#select-phlo-type').on('change', function() {
    	var select_val = "#" + $(this).val();
    	$('.hide').not(select_val).hide();    	
    	$(select_val).show();
    })    
    
    
	/*-------------------------------------------------------------------------------
	Buttons & Events
	-------------------------------------------------------------------------------*/
	
    // show hide open/close button        
    $('.man').on('click', function() {
		$(this).closest('li').toggleClass('collapsed').toggleClass('expanded');
	})

	// show hide answer content	
	$(document).on('click','.answer', function() {
  		$(this).nextAll('div').slideToggle('slow');
	});
	
	// remove a title or subtitle
	$('.remove').on('click', function() {		
		if (confirm('Are you sure? Can\'t undo.')) {
        	var content = 'id=phlotitle_3&action=remove';
        	updatedb(content,'remove');
        	$(this).closest('li').remove();
    	}
    	return false;    
	});

	// add a new item
	$('.add').on('click', function() {
		var last = $(this).closest('li').attr('id');
		var last_nested = $(this).closest('li').children('ol').children('li:last-child').attr('id');
		var l = last.replace('phloorder_','');
		if(last_nested) { // if there are already items
			var ln = last_nested.replace('phloorder_','');
			var content = 'last_id=' + ln + '&parent_id=' + l + '&action=add_new';
		} else { // no items created
			var last_nested = $(this).closest('div').attr('id');		
			var content = 'last_id=&parent_id=' + l + '&action=add_first';
			$('#'+last).removeClass('phlo-leaf').addClass('phlo-branch phlo-expanded ');
		}
		$.post("/inc/update.php", content, function(response) {		
			$('body').addClass("loading");     	
			$('#'+last_nested).after(response);
			$('body').removeClass("loading");
		});
	});

	/*-------------------------------------------------------------------------------
	Create sortable nested lists
	-------------------------------------------------------------------------------*/		
	
	// move nested listed within themselves
	$(document).mouseup(function (e) { 		
		if ($(e.target).is('.move')) { 
			var t = '#' + $(e.target).closest('ol').attr('id');
			$(t).disableSelection();
	 	}	 	
	}); 
	$(document).mouseover(function (e) {
		if ($(e.target).is('.move')) { 
			var t = '#' + $(e.target).closest('ol').attr('id');
			$(t).sortable();
	 	}	 	
	}); 

	/*-------------------------------------------------------------------------------
	Capture list items and return array for database insertion
	-------------------------------------------------------------------------------*/
	
	// save phlo title changes
	$('.sort .title').change(function() {  			
  		var content = 'content=' + $(this).val() + '&id=' + $(this).attr('id') + '&action=update_title';  		
		updatedb(content,'notify');
	});	

	// save phlo subtitle changes
	$('.sort .subtitle').change(function() {  			
  		var content = 'content=' + $(this).val() + '&id=' + $(this).attr('id') + '&action=update_subtitle';
		updatedb(content,'notify');
	});	

	
	/*-------------------------------------------------------------------------------
	Serialize form values and submit form
	-------------------------------------------------------------------------------*/
	$('#phlo-form').submit(function (e) {		
		var form = this;
		var order = "array(";
		e.preventDefault();
		$("li").each(function() { // get order of lists
			var list_id= $(this).attr('id');			
			var ret = list_id.split("_");
			order += "'" + ret[0] + "'=>'" + ret[1] + "',";
		});
		order = order.substring(0,order.length - 1);
		order += ")";
    	$('#phloorder').val(order); // set order in hidden field
    	setTimeout(function () {
        	form.submit(); // submit form
	    }, 1000);	    
	});

	/*-------------------------------------------------------------------------------
	Functions
	-------------------------------------------------------------------------------*/
	
	// update database with text changes
	function updatedb(content,type) {
		$.post("/inc/update.php", content, function(response) {		
			if(type == 'notify') { // notify when an AJAX request is made
				if (response == 'success') {
					$('.success').slideDown('slow');
					closeNoteBox('.success');
				}			
			}
			if(type == 'debug') { // for testing
				$('#output').text(response+'\n\n');
			}
		});
	}
	
	// close notification box delay
	function closeNoteBox(box) {		
		window.setTimeout (function(){ 
	       $(box).slideUp('slow');
	    },3000);
	}
	
		
});	