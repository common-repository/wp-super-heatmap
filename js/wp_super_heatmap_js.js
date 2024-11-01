jQuery(document).ready(function() {
	//*********************************************
	// AJAX Loading Spinner
	//*********************************************
	jQuery("#spinner").bind("ajaxSend", function() {
		jQuery(this).show();
	}).bind("ajaxStop", function() {
		jQuery(this).hide();
	}).bind("ajaxError", function() {
		jQuery(this).hide();
	});
	
	//*********************************************
	// Turn off the heat map when the user clicks the button
	//*********************************************
	jQuery('.heatmap-bar-on').live('click', function() { 
		// Remove any dots
		jQuery('.dots').remove();
		// Remove the overlay
		jQuery('.heatmap-overlay').remove();
		// Change the button to turn on/off
		jQuery('.heatmap-bar-on').removeClass('heatmap-bar-on').addClass('heatmap-bar-off');
		jQuery('.heatmap-bar-off span').addClass('heatmap-bar-span').html('Display Heatmap');
	});
	
	//*********************************************
	// Display the heat map when the user clicks the button
	//*********************************************
	jQuery('.heatmap-bar-off').live('click', function() { 
		// Remove any previous dots just in case
		jQuery('.dots').remove();
		// Change the button to turn on/off
		jQuery('.heatmap-bar-off').removeClass('heatmap-bar-off').addClass('heatmap-bar-on');
		jQuery('.heatmap-bar-on span').addClass('heatmap-bar-span').html('Exit Heatmap');
		
		// Add the overlay layer
		jQuery('body').append("<div class='heatmap-overlay'></div>");
		
		// Get the dots and process
		current_url = document.location.href;
		jQuery.post(
			MyAjax.ajaxurl,
			{
				action: 'wp_super_heatmap_display',
				current_url : current_url
			},
			function( response ) {
				var data = jQuery.parseJSON(response);
				
				// Loop over each dot
				jQuery.each(data.dot_collection, function(i) { 
					x_coord = parseInt(this.x_coord, 10);
					y_coord = parseInt(this.y_coord, 10);
					var selector = this.selector;
					
					// Check if jQuery(selector) is valid
					if (jQuery(selector).length) {
						// Calculate the offset of the point's selector to the top left
						final_x_coord = x_coord + parseInt( jQuery(selector).offset().left,10 );
						final_y_coord = y_coord + parseInt( jQuery(selector).offset().top, 10 );
						
						this.x_coord = final_x_coord;
						this.y_coord = final_y_coord;
					} else {
						// If the point is not on this page delete it from the dot_collection
						// **** DELETE THIS (like this. not this line) ****
						data.dot_collection.splice(i, 0);
					}
				});
				
				// JSON stringify the collection
				send_data = JSON.stringify(data);
				
				// Create an AJAX push to push all of the data back to the server with the final points
				jQuery.post(
					MyAjax.ajaxurl,
					{
						action: 'wp_super_heatmap_calculate_neighbors',
						dot_collection: send_data
					},
					function (response) {
						jQuery('body').append('<div class="dots"></div>');
						jQuery('.dots').append(response);
					},
					"html"
				);
		    }
		);
		
		return false;
	});
	
}); //end jQuery(document).ready(function() {
