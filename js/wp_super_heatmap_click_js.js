jQuery(document).ready(function() {
	//*********************************************
	// Record a User click for the heat map
	//*********************************************
	jQuery('body').live('click', function(event) {
		// Pause the normal function in case this is a link
		event.preventDefault();
		
		// Make sure we aren't clicking the Heatmap functionality
		if ( (event.target.className == 'heatmap-bar-off') 
			|| (event.target.className == 'heatmap-bar-on')  
			|| (event.target.className == 'heatmap-bar-span')
		){
			return false;
		}
		
		//Check if the overlay is on
		if ( jQuery('.heatmap-overlay').length ) {
			return false;
		}
		// Function for reversing array
		jQuery.fn.reverse = [].reverse;
		
		// Find the current url
		current_url = document.location.href;
		
		// Wrap the page in a div
		//jQuery('body').children().wrapAll('<div class="wp_super_heatmap"></div');

		// Find the x and y coordinates of the click offset to the target node
		 x_coord = event.pageX - jQuery( event.target ).offset().left;
		 y_coord = event.pageY - jQuery( event.target ).offset().top;
		
		// Find the attributes of this node
			//nodeName
			 nodeName = event.target.nodeName.toLowerCase();

			//nodeClass	
			if (event.target.className.length > 0) {
				 nodeClass = event.target.className.split(" ").join(".");
			} else {  nodeClass = ""; }
				
			//nodeID
			if (typeof jQuery(event.target).attr('id') != 'undefined') {
				nodeID = jQuery(event.target).attr('id').split(" ").join("#");
			} else { nodeID = ""; }
			
			//nodeIndex
			nodeIndex = jQuery(event.target).prevAll(nodeName).size();
			
			//fullNode
			if ( nodeClass.length > 0) {
				fullNode = nodeName + '.' + nodeClass + ':eq(' + nodeIndex + ')';
			} else if ( nodeID.length > 0 ) {
				fullNode = nodeName + '#' + nodeID + ':eq(' + nodeIndex + ')';
			} else {
				fullNode = nodeName + ':eq(' + nodeIndex + ')';
			}
			
		 jQuery_nodeParents = jQuery(event.target).parents()
			.map(function () { 
				// Get the index of this node
				parentIndex = jQuery(this).prevAll(this.nodeName).size();
				
                if (this.className.length > 0 ) {
					connectedClasses = this.className.split(" ").join(".");
					parentIndex = jQuery(this).prevAll(this.nodeName + '.' + connectedClasses).size();
					return (this.nodeName + '.' + connectedClasses + ':eq(' + parentIndex + ')');
				} else if (typeof jQuery(this).attr('id') != 'undefined') {
					connectedIDs = jQuery(this).attr('id').split(" ").join("#");
					parentIndex = jQuery(this).prevAll(this.nodeName + '#' + connectedIDs).size();
					return (this.nodeName + '#' + connectedIDs + ':eq(' + parentIndex + ')');
				} else { 
					return this.nodeName + ':eq(' + parentIndex + ')'; 
				}
			}).get().reverse().join(" > ").toLowerCase();
			
		// Append this node name onto its parents
		 jQuerySelector = jQuery_nodeParents + ' > ' + fullNode;

		jQuery.post(
		    MyAjax.ajaxurl,
		    {
		        // wp_ajax_nopriv_wp_super_heatmap_add_dot and wp_ajax_wp_super_heatmap_add_dot
		        action   : 'wp_super_heatmap_add_dot',
		        x_coord  : x_coord,
		        y_coord  : y_coord,
		        selector : jQuerySelector,
		        current_url      : current_url
		    },
		    function( response ) {
		    	return true;
		    }
		);	
		
		// Redirect if this is a link
		if ( jQuery(event.target).closest('a').attr('href').length ) {
			var url = jQuery(event.target).closest('a').attr('href');
			if ( jQuery(event.target).closest('a').attr('target') == '_blank') {
				window.setTimeout(function() 
					{ window.open(url, '_blank') },
					window.name,
					1000);
			} else {
				window.setTimeout(function(){document.location.href=url;}, 1000); // timeout and waiting until effect is complete */
			} 			
		}
		
		return false;
	});

}); 