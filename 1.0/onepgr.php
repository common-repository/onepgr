<?php
/**
 * @package Onepgr
 */
/*
Plugin Name: OnePgr Lobby
Plugin URI: https://onepgr.com/
Description: Onepgr's powerful customer lobby now on your Wordpress powered website.
Version: 1.0
Author: Onepgr
Author URI: https://onepgr.com/
License: GPLv2 or later
Text Domain: onepgr
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016 Onepgr, LLC.
*/

	 // Prevent direct file access
	if( ! defined( 'ABSPATH' ) ) {
		die();
	}

	register_activation_hook( __FILE__, 'onepgr_default_options' );

	// default settings 
	function onepgr_default_options() {
		if ( get_option( 'onepgr_options' ) === false ) {
			$new_options['onepgr_token'] = "mvlshsahb44vxmp1w2ti";
			$new_options['onepgr_logo'] = plugins_url( 'images/default.png', __FILE__ );
			$new_options['onepgr_version'] = "1.0";
			$new_options['onepgr_activate'] = false;
			add_option( 'onepgr_options', $new_options );
		}
	}
	
	// admin notice to insert onepgr's TOKEN and logo URL
	add_action('admin_notices', 'onepgr_plugin_admin_notices');
	function onepgr_plugin_admin_notices() {
		$options = get_option( 'onepgr_options' ); 

		if (!$options['onepgr_activate'] && is_plugin_active('onepgr/onepgr.php')) {
			echo "<div class='updated'><p>One step away from integrating onepgr's customer lobby on to your website. Please fill up <b>Token</b> and <b>Logo URL</b> in onepgr settings page <a href='options-general.php?page=onepgr_settings'>here</a>.</p></div>";
		}
	}


	
	add_action( 'admin_menu', 'onepgr_menu' );
	function onepgr_menu() {
		add_options_page( 'Onepgr Settings Page', 'Onepgr', 'manage_options', 'onepgr_settings', 'onepgr_settings_page' );
	}
	
	function onepgr_settings_page() {
	$options = get_option( 'onepgr_options' ); ?>
	
	<div id="onepgr-general" class="wrap">
		<?php echo ' <img src="' .plugins_url( 'images/logo.png' , __FILE__ ). '" > ';?>
		<h2>Customer Lobby Settings</h2>
		<?php if ( isset( $_GET['message'] )&& $_GET['message'] == '1' ) { ?>
		<div id='message' class='updated fade'><p><strong>Your settings have been saved.</strong></p></div>
		<?php } ?>
		<p>Please enter your onepgr token and your custom logo.</p>
		<table>
			<form method="post" action="admin-post.php">
			
				<input type="hidden" name="action" value="save_onepgr_options" />
				<?php wp_nonce_field( 'onepgr' ); 	?>
				<tbody>
					<tr><td>Token	:</td> <td><input type="text" name="onepgr_token" value="<?php echo esc_html( $options['onepgr_token'] ); ?>" required /></td></tr>
					
					<tr><td>Logo URL:</td> <td><input type="text" name="onepgr_logo" value="<?php echo esc_html( $options['onepgr_logo'] ); ?>" required /></td></tr>
					
					<tr><td><input type="submit" value="Submit" class="button-primary"/></td></tr>
				</tbody>
			</form>
		</table>
	</div>
	<?php }

	
	add_action( 'admin_init', 'onepgr_admin_init' );
	
	function onepgr_admin_init() {
		add_action( 'admin_post_save_onepgr_options', 'process_onpgr_options' );
	}

	
	function process_onpgr_options() {
		if ( !current_user_can( 'manage_options' ) )
			wp_die( 'Not allowed' );
	
		check_admin_referer( 'onepgr' );
		$options = get_option( 'onepgr_options' );

		foreach ( array( 'onepgr_token' ) as $option_token ) {
			if ( isset( $_POST[$option_token] ) ) {
				$options[$option_token] = sanitize_text_field( $_POST[$option_token] );
			}
		}
		
		foreach ( array( 'onepgr_logo' ) as $option_logo ) {
			if ( isset( $_POST[$option_logo] ) ) {
				$options[$option_logo] = sanitize_text_field( $_POST[$option_logo] );
			}
		}
        $options['onepgr_activate'] = true;
		update_option( 'onepgr_options', $options ) ;

		wp_redirect( add_query_arg(array( 'page' => 'onepgr_settings', 'message' => '1' ), admin_url( 'options-general.php' ) ) );

		exit;
	}
	
 

	function onepgr_add_script_footer() {
		$options = get_option( 'onepgr_options' ); 
	    wp_enqueue_script( 'onepgr_prlobby_code', '//app.onepgr.com/chat-client/lobby.js', array(), '1.0' );
		wp_add_inline_script( 'onepgr_prlobby_code', "ONEPGRCHAT.init({queue_token:'".  esc_html( $options['onepgr_token'] ). "',logo_url:'". esc_html( $options['onepgr_logo'] )."'});" );

	}
	add_action( 'wp_footer', 'onepgr_add_script_footer' );

?>