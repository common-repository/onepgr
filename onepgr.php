<?php
/**
 * @package Onepgr
 */
/*
Plugin Name: OnePgr Lobby
Plugin URI: https://onepgr.com/
Description: Onepgr's powerful customer lobby now on your Wordpress powered website.
Version: 1.6
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
			$new_options['onepgr_setup'] == "sc";
			$new_options['onepgr_general_question_queue'] = "qpmfe9kh5vyavla7g6pl";
			$new_options['onepgr_product_support_queue'] = "pc5bon9de9z46iikpqex";
			$new_options['onepgr_customer_service_queue'] = "phivs1le6dlzvobtk3u4";
			$new_options['onepgr_learn_more_url'] = "https://onepgr.com/pgr_customercare.html";
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
		<h2>Onepgr Lobby Configuration</h2>
		<?php if ( isset( $_GET['message'] )&& $_GET['message'] == '1' ) { ?>
		<div id='message' class='updated fade'><p><strong>Your settings have been saved.</strong></p></div>
		<?php } ?>
		<p>Select a method to set up your onepgr lobby chat</p>
		<table id="opepgr-option">
			<form method="post" action="admin-post.php">

				<input type="hidden" name="action" value="save_onepgr_options" />
				<?php wp_nonce_field( 'onepgr' ); 	?>
				<tr><td><label><input type="radio" name="onepgr_setup" value="sc" <?php if($options['onepgr_setup'] == 'sc') {echo 'checked' ;}  ?>> Set up by entering tokens and other information</label></td></tr>
				<tbody class="onepgr-fields" <?php if( $options['onepgr_setup']  == 'sc') {echo 'style="display: block;"'; } else { echo 'style="display: none;" ';} ?>>
					<tr><td>General Question Queue	:</td> <td><input type="text" name="onepgr_general_question_queue" value="<?php echo esc_html( $options['onepgr_general_question_queue'] ); ?>" required /></td></tr>
					<tr><td>Product Support Queue	:</td> <td><input type="text" name="onepgr_product_support_queue" value="<?php echo esc_html( $options['onepgr_product_support_queue'] ); ?>" required /></td></tr>
					<tr><td>Customer Service Queue	:</td> <td><input type="text" name="onepgr_customer_service_queue" value="<?php echo esc_html( $options['onepgr_customer_service_queue'] ); ?>" required /></td></tr>
					<tr><td>Learn More URL	:</td> <td><input type="text" name="onepgr_learn_more_url" value="<?php echo esc_html( $options['onepgr_learn_more_url'] ); ?>"  /></td></tr>
					
					<tr><td>Logo URL:</td> <td><input type="text" name="onepgr_logo" value="<?php echo esc_html( $options['onepgr_logo'] ); ?>" required /></td></tr>
					
				</tbody>
				<tr><td><label><input type="radio" name="onepgr_setup" value="sv" <?php if($options['onepgr_setup'] != 'sc') {echo 'checked'  ;} ?>> Set up by directly copying and pasting Javascript snippets</label></td></tr>
				<tbody class="onepgr-snip" <?php if($options['onepgr_setup']!= 'sc') {echo 'style="display: block;"'; } else { echo 'style="display: none;" ' ;} ?>>
					<tr><td>Please enter onepgr code Javascript snippets	:</td></tr> <tr><td>

							<textarea rows="8" cols="80" name="onepgr_code_snippet"><?php echo $options['onepgr_code_snippet'] ; ?></textarea>
					</td></tr>
				</tbody>
				<tr><td><input type="submit" value="Save" class="button-primary"/></td></tr>
			</form>
		</table>

	</div>
	<?php }

	
	add_action( 'admin_init', 'onepgr_admin_init' );
	
	function onepgr_admin_init() {
		add_action( 'admin_post_save_onepgr_options', 'process_onpgr_options' );
	}

	
	function wpdocs_selectively_enqueue_admin_script( $hook ) {
	  
	    wp_enqueue_script( 'onepgr_custom_script', plugin_dir_url( __FILE__ ) . 'js/custom.js',  array( 'jquery' ) , '1.0' );
	}

	add_action( 'admin_enqueue_scripts', 'wpdocs_selectively_enqueue_admin_script' );


	function process_onpgr_options() {
		if ( !current_user_can( 'manage_options' ) )
			wp_die( 'Not allowed' );
	
		check_admin_referer( 'onepgr' );
		$options = get_option( 'onepgr_options' );

		foreach ( array( 'onepgr_general_question_queue' ) as $onepgr_general_question_queue ) {
			if ( isset( $_POST[$onepgr_general_question_queue] ) ) {
				$options[$onepgr_general_question_queue] = sanitize_text_field( $_POST[$onepgr_general_question_queue] );
			}
		}
		
		foreach ( array( 'onepgr_product_support_queue' ) as $onepgr_product_support_queue ) {
			if ( isset( $_POST[$onepgr_product_support_queue] ) ) {
				$options[$onepgr_product_support_queue] = sanitize_text_field( $_POST[$onepgr_product_support_queue] );
			}
		}
		
		foreach ( array( 'onepgr_customer_service_queue' ) as $onepgr_customer_service_queue ) {
			if ( isset( $_POST[$onepgr_customer_service_queue] ) ) {
				$options[$onepgr_customer_service_queue] = sanitize_text_field( $_POST[$onepgr_customer_service_queue] );
			}
		}
		
		foreach ( array( 'onepgr_learn_more_url' ) as $onepgr_learn_more_url ) {
			if ( isset( $_POST[$onepgr_learn_more_url] ) ) {
				$options[$onepgr_learn_more_url] = sanitize_text_field( $_POST[$onepgr_learn_more_url] );
			}
		}
		
		
		
		foreach ( array( 'onepgr_logo' ) as $option_logo ) {
			if ( isset( $_POST[$option_logo] ) ) {
				$options[$option_logo] = sanitize_text_field( $_POST[$option_logo] );
			}
		}

		foreach ( array( 'onepgr_setup' ) as $onepgr_setup ) {
			if ( isset( $_POST[$onepgr_setup] ) ) {
				$options[$onepgr_setup] = sanitize_text_field( $_POST[$onepgr_setup] );
			}
		}


		foreach ( array( 'onepgr_code_snippet' ) as $onepgr_code_snippet ) {
			if ( isset( $_POST[$onepgr_code_snippet] ) ) {
				$options[$onepgr_code_snippet] = htmlentities(stripslashes($_REQUEST['onepgr_code_snippet']))  ;
			}
		}



        $options['onepgr_activate'] = true;
		update_option( 'onepgr_options', $options ) ;

		wp_redirect( add_query_arg(array( 'page' => 'onepgr_settings', 'message' => '1' ), admin_url( 'options-general.php' ) ) );

		exit;
	}
	
 

	function onepgr_add_script_footer() {
		$options = get_option( 'onepgr_options' ); 
	    wp_enqueue_script( 'onepgr_prlobby_code', '//onepgr.com/apps/lobby_client/js/lobby.js', array(), '1.0' );
		  
		if($options['onepgr_setup'] == "sc") {
			 wp_add_inline_script( 'onepgr_prlobby_code', 'ONEPGRCHAT.init([{general_question_queue:"'.esc_html( $options['onepgr_general_question_queue'] ).'",product_support_queue:"'.esc_html( $options['onepgr_product_support_queue'] ).'",customer_service_queue:"'.esc_html( $options['onepgr_customer_service_queue'] ).'"},{logo_url:\''.esc_html( $options['onepgr_logo'] ). '\',learn_more_url: \''. esc_html( $options['onepgr_learn_more_url'] ). '\'}]);' );
		} else {
			 wp_add_inline_script( 'onepgr_prlobby_code',  html_entity_decode($options['onepgr_code_snippet'])  );
		}
	   

		
		// wp_add_inline_script( 'onepgr_prlobby_code', "ONEPGRCHAT.init({queue_token:'".  esc_html( $options['onepgr_token'] ). "',logo_url:'". esc_html( $options['onepgr_logo'] )."'});" );

	}
	add_action( 'wp_footer', 'onepgr_add_script_footer' );

?>