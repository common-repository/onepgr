<?php

    // Prevent direct file access
	if( ! defined( 'ABSPATH' ) ) {
		die();
	}

	// Check that code was called from WordPress with
	// uninstallation constant declared
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

 	if ( get_option( 'onepgr_options' ) != false ) {
		delete_option( 'onepgr_options' );
	}
?>