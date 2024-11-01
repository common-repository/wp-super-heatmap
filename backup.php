<?php //Used to backup code that might get re-used ?>

<!-- CODE FOR TIME/DATE OPTION IN BACKEND -->
<div class="postbox" style="clear: both; margin: 10px 40px;">
<h3 class="hndle" style="padding: 10px 0 10px 20px;">
	<span style="font-size: 1.3em;">Date and Time Options</span>
</h3>

<div class="inside">
	<p>If you would only like to display the heatmap from and to certain dates you can set that here.  These dates will be used on all of the heatmaps you display on the site.</p>
	<hr />
	<form name="form_datepicker" method="post" action="options.php">
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('.datepicker').datepicker({
					dateFormat : 'yy-mm-dd'
				});
			});
		</script>
		
		<?php settings_fields('wp_super_heatmap_date_options'); ?>
		<?php $options = get_option('wp_super_heatmap_date_options'); ?>
		
		<input type="hidden" name="datepickerform_hidden" value="Y">
		
		<p class="on_off" style="margin: 5px 20px 5px 5px; padding: 10px; float: left;">
			<label style="font-size: 1.2em;" for="clicktrack_on_off">Use Dates for Heatmap Display</label>
			<input class="ibutton" name="wp_super_heatmap_date_options[wp_super_heatmap_use_dates]" type="checkbox" value="1"
				<?php if (isset($options['wp_super_heatmap_use_dates'])) { checked('1', $options['wp_super_heatmap_use_dates']); } ?> 
			/>
		</p>
		
		<div style="float:left">
			<p>
			<label><?php _e("Start Date:", 'menu-test' ); ?></label>
			<input class="datepicker" type="text" name="wp_super_heatmap_date_options[wp_super_heatmap_start_date]" value="<?php echo $options['wp_super_heatmap_start_date']; ?>" size="20" style="font-size: 1.3em;">
			</p>
			
			<p>
			<label><?php _e("End Date:", 'menu-test' ); ?></label>
			<input class="datepicker" type="text" name="wp_super_heatmap_date_options[wp_super_heatmap_end_date]" value="<?php echo $options['wp_super_heatmap_end_date']; ?>" size="20" style="font-size: 1.3em;">
			</p>
		</div>
		<div style="clear: both;"></div>
		<input style="margin-bottom: 20px;" type="submit" name="Submit" class="button-primary" id="save-options" value="<?php esc_attr_e('Save Dates') ?>" />
	</form>
</div> <!-- end .inside -->
</div> <!-- end .postbox -->