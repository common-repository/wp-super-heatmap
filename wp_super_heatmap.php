<?php
/*
Plugin Name: WP Super Heatmap
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: This plugin enables making and viewing heat maps in the back-end of WordPress.
Version: 0.1.0
Author: rfrankel
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2

/*  Copyright 2011  Ryan S. Frankel  (email : ryan.frankel@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* *********************************************
	INCLUDE FILES
********************************************* */
include( 'wp_super_heatmap_classes.php' );

/* *********************************************
	INCLUDE FRONT-END SCRIPTS TO PROCESS CLICKS
********************************************* */
add_action( 'wp_enqueue_scripts', 'wp_super_heatmap_script');
function wp_super_heatmap_script() {
	// jQuery in case it hasn't been loaded yet
	wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js');
    wp_enqueue_script( 'jquery' );
    
    // a JSON library that allows to send JSON from jQuery and not just receive it
    wp_deregister_script( 'wp_super_heatmap_json2' );
	wp_register_script( 'wp_super_heatmap_json2', plugins_url('js/json2.js', __FILE__) );
    wp_enqueue_script( 'wp_super_heatmap_json2' );   
    
    // Script for tracking user clicks
    $options = get_option('wp_super_heatmap_options');
    if ( array_key_exists('wp_super_heatmap_track_click', $options) ) {
	    wp_deregister_script( 'wp_super_heatmap_click' );
    	wp_register_script( 'wp_super_heatmap_click', plugins_url('js/wp_super_heatmap_click_js.js', __FILE__) );
	    wp_enqueue_script( 'wp_super_heatmap_click' );
	}
    
    // Script for processing the heatmap
	wp_deregister_script( 'wp_super_heatmap' );
    wp_register_script( 'wp_super_heatmap', plugins_url('js/wp_super_heatmap_js.js', __FILE__) );
    wp_enqueue_script( 'wp_super_heatmap' );
	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	wp_localize_script( 'wp_super_heatmap', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
	
	// The CSS file used for displaying clicks
	wp_register_style( 'wp_super_heatmap_style', plugins_url('css/wp_super_heatmap.css', __FILE__) );
	wp_enqueue_style( 'wp_super_heatmap_style' );
}

/* *********************************************
	INCLUDE BACK-END SCRIPTS AND STYLES
********************************************* */
add_action('admin_init', 'wp_super_heatmap_admin_script');
function wp_super_heatmap_admin_script() {
	// jQuery-ui
	wp_deregister_script( 'jquery-ui' );
	wp_register_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js' );
	wp_enqueue_script( 'jquery-ui' );
	
	// Admin Super Heatmap jQuery
	wp_deregister_script( 'wp_super_heatmap_admin' );
    wp_register_script( 'wp_super_heatmap_admin', plugins_url('js/wp_super_heatmap_admin_js.js', __FILE__) );
    wp_enqueue_script( 'wp_super_heatmap_admin' ); 
    
	// Apple style checkboxes script
    wp_deregister_script('iphone_style_checkboxes');
    wp_register_script( 'iphone_style_checkboxes', plugins_url('js/jquery-ibutton/lib/jquery.ibutton.min.js', __FILE__) );
    wp_enqueue_script( 'iphone_style_checkboxes' );
    // Apple style checkboxes style
    wp_register_style( 'iphone_style_checkboxes_style', plugins_url('js/jquery-ibutton/css/jquery.ibutton.min.css', __FILE__) );
    wp_enqueue_style( 'iphone_style_checkboxes_style' );
	
    // JSON for jQuery and Javascript
    wp_deregister_script('wp_super_heatmap_json');
    wp_register_script('wp_super_heatmap_json', plugins_url( 'js/json.js', __FILE__ ));
    wp_enqueue_script('wp_super_heatmap_json');
    
	// Aristo Datepicker 
	wp_register_style( 'jquery-aristo', plugins_url('css/Aristo/Aristo.css', __FILE__) );
	wp_enqueue_style( 'jquery-aristo');
	
	// Admin Super Heatmap CSS
	wp_register_style( 'wp_super_heatmap_admin_style', plugins_url('css/wp_super_heatmap.css', __FILE__) );
	wp_enqueue_style( 'wp_super_heatmap_admin_style' );
}

/* *********************************************
	ACTIVATION AND INITS
********************************************* */
register_activation_hook(__FILE__,'wp_super_heatmap_install');
add_action('admin_init', 'wp_super_heatmap_init' );

// Init plugin options to white list our options
function wp_super_heatmap_init(){
	register_setting( 'wp_super_heatmap_plugin_options', 'wp_super_heatmap_options', 'wp_super_heatmap_options_validate' );
	register_setting( 'wp_super_heatmap_date_options', 'wp_super_heatmap_date_options', 'wp_super_heatmap_date_options_validate' );
}

function wp_super_heatmap_install() {
	// Create the table in the database
	global $wpdb;
	$wp_super_heatmap_db_version = "1.0";
	
	$table_name = $wpdb->prefix . "wp_super_heatmap_dots";
	
	$sql = "CREATE TABLE " . $table_name . " (
		id INTEGER NOT NULL AUTO_INCREMENT,
		created_date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		x_coord INTEGER NOT NULL,
		y_coord INTEGER NOT NULL,
		neighbors INTEGER NOT NULL, 
		color TEXT NOT NULL,
		selector TEXT NOT NULL,
		url TEXT NOT NULL,
		UNIQUE KEY id (id)
	);";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	// Add the options for the plugin
	$arr = array(	
		"wp_super_heatmap_track_click" => '0',
		"wp_super_heatmap_display_tab" => '0'
	);
	update_option('wp_super_heatmap_options', $arr);
	
	$arr = array(
		"wp_super_heatmap_use_dates" => '0',
		"wp_super_heatmap_start_date" => '',
		"wp_super_heatmap_end_date" => ''
	);
	update_option('wp_super_heatmap_date_options', $arr);
}

/* *********************************************
	VALIDATE OPTIONS CALLBACK
********************************************* */
function wp_super_heatmap_options_validate($input) {	
	return $input;
}

function wp_super_heatmap_date_options_validate($input) {
	$input['wp_super_heatmap_start_date'] = wp_filter_nohtml_kses($input['wp_super_heatmap_start_date']);
	$input['wp_super_heatmap_end_date'] = wp_filter_nohtml_kses($input['wp_super_heatmap_end_date']);
	return $input;
}



/* *********************************************
	AJAX FUNCTION TO SAVE CLICK
********************************************* */
add_action( 'wp_ajax_nopriv_wp_super_heatmap_add_dot', 'wp_super_heatmap_add_dot' );
add_action( 'wp_ajax_wp_super_heatmap_add_dot', 'wp_super_heatmap_add_dot' );

function wp_super_heatmap_add_dot() {
	// Strip slashes on POST
	if ( get_magic_quotes_gpc() ) {
		$_POST = array_map( 'stripslashes_deep', $_POST );
	}
	// WP Database setup
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_super_heatmap_dots";
	
	// Get POST Variables from AJAX
	$x_coord = $_POST['x_coord'];
	$y_coord = $_POST['y_coord'];
	$created_date = date( 'Y-m-d H:i:s' );
	$selector = $_POST['selector'];
	$url = $_POST['current_url'];
	
	$data = array(
		'x_coord' => $x_coord,
		'y_coord' => $y_coord,
		'created_date' => $created_date,
		'selector' => $selector,
		'url' => $url
	);
	
	$wpdb->insert($table_name, $data);
	
	echo "Success!";
	exit;
}

/* *********************************************
	AJAX FUNCTION TO DISPLAY HEATMAP
********************************************* */
add_action( 'wp_ajax_nopriv_wp_super_heatmap_display', 'wp_super_heatmap_display' );
add_action( 'wp_ajax_wp_super_heatmap_display', 'wp_super_heatmap_display' );

function wp_super_heatmap_display() {
	// Strip slashes on POST
	if ( get_magic_quotes_gpc() ) {
		$_POST = array_map( 'stripslashes_deep', $_POST );
	}
	
	// WP Database setup
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_super_heatmap_dots";
	
	// Get the current URL
	$url = $_POST['current_url'];
	
	// Get the date options for the plugin
/*
	$options = get_option('wp_super_heatmap_date_options');
	if ( $options['wp_super_heatmap_use_dates'] == '1' ) {
		$start_date = $options['wp_super_heatmap_start_date'];
		$end_date = $options['wp_super_heatmap_end_date'];
		$dots = $wpdb->get_results( 
			"
			SELECT *
			FROM $table_name
			WHERE url = '$url' AND 
			
			"
		);
	}
*/
	
	$dots = $wpdb->get_results( 
		"
		SELECT *
		FROM $table_name
		WHERE url = '$url'
		"
	);
	
	$dot_collection = new wp_super_heatmap_dot_collection();
	foreach ($dots as $dot) {
		$dot_collection->add_dot( $dot->x_coord, $dot->y_coord, $dot->created_date, $dot->selector );
	}
	
	echo json_encode($dot_collection);
	exit;
}

/* *********************************************
	AJAX FUNCTION TO CLEAR DATABASE
********************************************* */
add_action( 'wp_ajax_nopriv_wp_super_heatmap_clear_database', 'wp_super_heatmap_clear_database' );
add_action( 'wp_ajax_wp_super_heatmap_clear_database', 'wp_super_heatmap_clear_database' );

function wp_super_heatmap_clear_database() {
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_super_heatmap_dots";
	
	$wpdb->query("TRUNCATE TABLE $table_name");
	
	exit;
}

/* *********************************************
	FOR DISPLAY HEATMAP BAR
********************************************* */
add_action( 'wp_footer', 'insert_display_heatmap_bar');
function insert_display_heatmap_bar() {
	if ( current_user_can('administrator') ) {
		$options = get_option('wp_super_heatmap_options');
		if ($options['wp_super_heatmap_display_tab'] == '1') {
		?>
			<div class="heatmap-bar-off">
				<span class="heatmap-bar-span">Display Heatmap</span>
				<div id="spinner" class="spinner" style="display:none; float: right; margin-top: 3px;">
					<img id="img-spinner" src="<?php echo plugins_url('images/ajax-loader.gif', __FILE__); ?>" alt="Loading"/>
				</div>
			</div>
		<?php
		}
	}
}

/* *********************************************
	AJAX Function for calculating neighbors
********************************************* */
add_action( 'wp_ajax_nopriv_wp_super_heatmap_calculate_neighbors', 'wp_super_heatmap_calculate_neighbors' );
add_action( 'wp_ajax_wp_super_heatmap_calculate_neighbors', 'wp_super_heatmap_calculate_neighbors' );

function wp_super_heatmap_calculate_neighbors() {
	// Get the data from the post
	$_POST = array_map( 'stripslashes_deep', $_POST );

	$dots = json_decode($_POST['dot_collection']);
	$dot_collection = $dots->dot_collection;
	
	// Get the number of dots
	$number_of_dots = count($dot_collection);
	
	//Set up constants for neighborhood distances
	$RED_NEIGHBORHOOD  = $number_of_dots * 0.110;
	$DARK_ORANGE_NEIGHBORHOOD = $number_of_dots * 0.100;
	$ORANGE_NEIGHBORHOOD = $number_of_dots * 0.090;
	$LIGHT_ORANGE_NEIGHBORHOOD = $number_of_dots * 0.080;
	$YELLOW_NEIGHBORHOOD = $number_of_dots * 0.050;
	$LIGHT_YELLOW_NEIGHBORHOOD = $number_of_dots * 0.03;
	$LIGHT_GREEN_NEIGHBORHOOD = $number_of_dots * 0.025;
	$GREEN_NEIGHBORHOOD = $number_of_dots * 0.02;
	
	// Set up the color profile
	$RED_ID = "red";
	$RED = "#ee2424";
	$DARK_ORANGE_ID = "dark-orange";
	$DARK_ORANGE = "#f47b20";
	$ORANGE_ID = "orange";
	$ORANGE = "#faed35";
	$LIGHT_ORANGE_ID = "light-orange";
	$LIGHT_ORANGE = "#b2d33b";
	$YELLOW_ID = "yellow";
	$YELLOW = "#57b94a";
	$LIGHT_YELLOW_ID = "light-yellow";
	$LIGHT_YELLOW = "#199292";
	$LIGHT_GREEN_ID = "light-green";
	$LIGHT_GREEN = "#415fac";
	$GREEN_ID = "green";
	$GREEN = "#28377f";
	$DARK_GREEN_ID = "dark-green";
	$DARK_GREEN = "#283479";
	
	// Decide on the dot size 
	$DOT_SIZE = 10 . "px";
	
	// Determine the max distance for the neighborhood
	$max_distance_to_neighbor = 20*20; // squared to remove the sq. root from distance calc
	
	// Loop through each dot
	$returner = "";
	foreach( $dot_collection as $dot ) {
		// Clear number of neighbors
		$dot->number_of_neighbors = 0;
		
		// Inner Loop through each dot
		foreach ( $dot_collection as $neighbor_dot ) {
			// Calculate the distance and add a neighbor if appropriate
			$distance = ($dot->x_coord - $neighbor_dot->x_coord) * ($dot->x_coord - $neighbor_dot->x_coord) + 
						($dot->y_coord - $neighbor_dot->y_coord) * ($dot->y_coord - $neighbor_dot->y_coord);
			if ( $distance <= $max_distance_to_neighbor ) {
				$dot->number_of_neighbors = $dot->number_of_neighbors + 1;
			}
		}
		
		// Calculate the current ID for a dot with this many neighbors
		if ($dot->number_of_neighbors > $RED_NEIGHBORHOOD) { $dot->dot_id = $RED_ID; } 
		elseif ($dot->number_of_neighbors > $DARK_ORANGE_NEIGHBORHOOD) { $dot->dot_id = $DARK_ORANGE_ID; }
		elseif ($dot->number_of_neighbors > $ORANGE_NEIGHBORHOOD) { $dot->dot_id = $ORANGE_ID; }
		elseif ($dot->number_of_neighbors > $LIGHT_ORANGE_NEIGHBORHOOD) { $dot->dot_id = $LIGHT_ORANGE_ID; }
		elseif ($dot->number_of_neighbors > $YELLOW_NEIGHBORHOOD) { $dot->dot_id = $YELLOW_ID; }
		elseif ($dot->number_of_neighbors > $LIGHT_YELLOW_NEIGHBORHOOD) { $dot->dot_id = $LIGHT_YELLOW_ID; }
		elseif ($dot->number_of_neighbors > $GREEN_NEIGHBORHOOD) { $dot->dot_id = $GREEN_ID; }
		else { $dot->dot_id = $DARK_GREEN_ID; }
		
		// Build the dot for the response
		$top = ($dot->y_coord - ($DOT_SIZE / 2)) . "px";
		$left = ($dot->x_coord - ($DOT_SIZE / 2)) . "px"; 
		$id = $dot->dot_id;
		$returner .= "<div class='dot' id='$id' style='top: $top; left: $left; width: $DOT_SIZE; height: $DOT_SIZE; border-radius: $DOT_SIZE; position: absolute;'></div>";
	}
	
	echo $returner;
	exit;
}

/* *********************************************
	ADMIN MENU
********************************************* */
add_action('admin_menu', 'wp_super_heatmap_admin_menu');

function wp_super_heatmap_admin_menu() {
	add_menu_page('WP Super Heatmap Options', 'WP Super Heatmap', 'manage_options', 'wp_super_heatmap_page', 'wp_super_heatmap_plugin_options');
}

function wp_super_heatmap_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	// HTML for Page
	?>
	
	<div class="wrap">
			<h1>WP Super Heatmap Settings</h1>
			<p style="font-size: 1.2em; margin: 0 40px 10px;">Use the options below to configure WP Super Heatmap.  There isn't really too much to do to get it working.  Just enable click-tracking and then wait about a week for enough data to display your heatmap.</p>
			<p style="font-size: 1.2em; margin: 0 40px 10px;">If you could leave feedback for me <a href="http://heatmap.swampedpublishing.com">here</a> it would be much appreciated.  I will be glad to add features if people find this useful. If you find a bug please add it to the Issues section of the <a href="https://github.com/ryan-frankel/wp_super_heatmap">GitHub Project</a>.</p>
			<p style="margin: 0px 40px;">NOTE: It is not a good idea to try and track months and months worth of points as the processing is slow and doesn't generally provide statistically better results.  Also, please remember that you can no longer change your page html or layout without clearing the Heatmap database.</p>
			
			<div class="postbox" style="clear: both; margin: 10px 40px;">
				<h3 class="hndle" style="padding: 10px 0 10px 20px;">
					<span style="font-size: 1.3em;">On/Off Options</span>
				</h3>
			
				<div class="inside">
					<p>Here you can set whether you would like to track clicks on your website.  You can enable or disable this at your convenience.  If you would like the 'Display Heatmap' tab to appear in the front-end when you are logged in as an admin you can set that here too.</p>
					<hr />
					<form method="post" action="options.php">
						<?php settings_fields('wp_super_heatmap_plugin_options'); ?>
						<?php $options = get_option('wp_super_heatmap_options'); ?>
						
						<div class="alignleft">
							<p class="on_off" style="border-right: 1px solid #DFDFDF; margin: 5px; padding: 10px;">
								<label style="font-size: 1.2em; margin-bottom: 5px;" for="display_on_off">Turn Click Tracking On/Off</label>
								<input class="ibutton" name="wp_super_heatmap_options[wp_super_heatmap_track_click]" type="checkbox" value="1"
									<?php if (isset($options['wp_super_heatmap_track_click'])) { checked('1', $options['wp_super_heatmap_track_click']); } ?> 
								/>
							</p>
						</div> 
						
						<div class="alignleft">
							<p class="on_off" style="border-right: 1px solid #DFDFDF; margin: 5px; padding: 10px;">
								<label style="font-size: 1.2em;" for="clicktrack_on_off">Turn Heatmap Display Button On/Off</label>
								<input class="ibutton" name="wp_super_heatmap_options[wp_super_heatmap_display_tab]" type="checkbox" value="1"
									<?php if (isset($options['wp_super_heatmap_display_tab'])) { 
										checked('1', $options['wp_super_heatmap_display_tab']); 
									} ?> 
								/>
							</p>
						</div>
						<div style="clear: both;"></div>
						
						<p>
							<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
					</form>
				</div> <!-- end .inside -->
			</div> <!-- end .postbox -->
		
		<div class="postbox" style="clear: both; margin: 10px 40px;">
			<h3 class="hndle" style="padding: 10px 0 10px 20px;">
				<span style="font-size: 1.3em;">Clear Database</span>
			</h3>
			<div class="inside">
				<p>WARNING: This button will delete ALL data points.  This will give you a clean slate and a new heatmap.</p>
				<input style="margin-bottom: 20px;" type="submit" name="Submit" class="button-secondary" id="clear-database" value="<?php esc_attr_e('Clear Heatmap Database') ?>" />
			</div>
		</div> <!-- end .postbox -->
	</div>
	<?php
}

/* *********************************************
	FUNCTION TO DETERMINE IF INPUT IS A DATE
********************************************* */
function is_date_string( $str ) 
{ 
	$stamp = strtotime( $str ); 
	
	if (!is_numeric($stamp)) 
	{ 
	 return FALSE; 
	} 
	$month = date( 'm', $stamp ); 
	$day   = date( 'd', $stamp ); 
	$year  = date( 'Y', $stamp ); 
	
	if (checkdate($month, $day, $year)) 
	{ 
	 return TRUE; 
	} 
	
	return FALSE; 
}

?>