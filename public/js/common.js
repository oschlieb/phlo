$(document).ready(function() { 						   

    // show hide notification box
    $('.note-close').on('click', function() {
    	$(this).parent().remove();
	})

	// show hide sidebar boxes
	$(document).on('click','.blue-box-title', function() {
  		$(this).next('.blue-box').slideToggle('slow');
  		$(this).toggleClass('pad-bottom');
	});
	
});	