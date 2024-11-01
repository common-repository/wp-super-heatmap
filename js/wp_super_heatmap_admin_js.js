jQuery(document).ready(function() {
	// Checkbox Style
	jQuery('.ibutton').iButton();
	
	// When User clicks to clear heatmap
	jQuery('#clear-database').click( function() { 
		jQuery.get(
			ajaxurl,
			{
				action: 'wp_super_heatmap_clear_database'
			},
			function (response) {
				alert('The database has been cleared of all clicks-tracks.');
			}
		);
	});
});

