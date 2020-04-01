<?php
/*
Plugin Name: Spinup Cache Extensions
Plugin URI: https://delineator.media
Description: Extends user's control of the cache with SpinupWP.
Version: 0.1.1
Author: Delineator
Author URI: https://delineator.media
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Register the cache options page
function spinup_cache_ext_register_options_page() {
	if( function_exists('spinupwp') ) {
		add_options_page('Cache', 'Cache', 'manage_options', 'spinup_cache_ext', 'spinup_cache_ext_options_page');
	}
}

add_action('admin_menu', 'spinup_cache_ext_register_options_page');

// Build the cache options page
function spinup_cache_ext_options_page() { ?>
	<div class="wrap">
		<?php
		// Check if user can access
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.')    );
		}
		?>

		<h2>Cache Control</h2>

		<?php
		if( check_purge_cache_button() ) {
			spinupwp_purge_site();
		}
		?>

		<form method="post" action="options-general.php?page=spinup_cache_ext">
			<h3>Purge Cache</h3>
			<p>This button will purge the cache for the entire site. Typically, you would only
				press this button if you have made a change to the site and do not see it reflected on
				the front-end of the site.</p>
			<?php wp_nonce_field('purge_site_button'); ?>
			<?php  submit_button('Purge Site Cache'); ?>
		</form>
	</div>
	<?php
}

// Custom admin notice - cache is purged
function spinup_cache_ext_custom_admin_notice() {
	if( check_purge_cache_button() ) { ?>
		<div class="notice notice-success">
			<p><?php _e('The site cache has been purged.', 'spinup-cache-ext'); ?></p>
		</div>
	<?php }
}

add_action('admin_notices', 'spinup_cache_ext_custom_admin_notice');

// Check if purge cache button has been pushed
function check_purge_cache_button() {
	global $pagenow;
	if ( $pagenow == 'options-general.php' && $_GET['page'] == 'spinup_cache_ext' ) {
		if( $_POST['submit'] === 'Purge Site Cache' && check_admin_referer('purge_site_button') ) {
			return true;
		}
	}
}
