<?php 
	/**
	 * wp_super_heatmap_dot class.
	 */
	class wp_super_heatmap_dot {
		// Possible dot colors
		const red = "#f72d1e";
		const dark_orange = "#f76c3d";
		const orange = "#f79b55";
		const light_orange = "#f7c96c";
		const yellow = "#f7f883";
		const light_yellow = "#e0f883";
		const light_green = "#c7f682";
		const green = "#9ff27e";
		const dark_green = "#7cf27e";
		
		// Set the number of neighbors for different colors
		private $number_of_pixels_for_neighbor = 10;
		
		public $x_coord = 0;
		public $y_coord = 0;
		public $created_date = "";
		public $number_of_neighbors = 0;
		public $selector = "";
		public $dot_color = "";
		
		public function __construct( $x_coord = 0, $y_coord = 0, $created_date, $selector = "") 
		{
			// User settable
        	$this->x_coord = $x_coord;
        	$this->y_coord = $y_coord;
        	$this->created_date = $created_date;
        	$this->selector = $selector;
        	// Self defined
			$this->number_of_neighbors = 0;
        	$this->dot_color = self::dark_green;
    	}
    	
    	public function __destruct() {
    	}
	}
	
	/**
	 * wp_super_heatmap_dot_collection class.
	 */
	class wp_super_heatmap_dot_collection {
		public $dot_collection = array();
		
		function add_dot( $x_coord, $y_coord, $created_date, $selector) {
			$this->dot_collection[] = new wp_super_heatmap_dot($x_coord, $y_coord, $created_date, $selector);
		}		
	}
?>