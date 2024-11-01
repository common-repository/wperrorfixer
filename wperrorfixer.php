<?php
/*
 * Plugin Name: WPerrorFixer
 * Version: 1.4
 * 
 * Description: This is a tool to fix errors from your wordpress (.maintenance file and database errors)
 * Author: Julio Morales Rosales
 * Author URI: https://www.wperrorfixer.com
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wperrorfixer
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Julio Morales Rosales
 * @since 1.4.0
 * Note: You don't need to access this link to use this plugin but I will do some funtionalities there.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-wperrorfixer.php' );
require_once( 'includes/class-wperrorfixer-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-wperrorfixer-admin-api.php' );
require_once( 'includes/lib/class-wperrorfixer-post-type.php' );
require_once( 'includes/lib/class-wperrorfixer-taxonomy.php' );

/**
 * Returns the main instance of WPerrorFixer to prevent the need to use globals.
 *
 * @since  1.4.0
 * @return object WPerrorFixer
 */
function WPerrorFixer () {
	$instance = WPerrorFixer::instance( __FILE__, '1.4.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WPerrorFixer_Settings::instance( $instance );
	}

	return $instance;
}

WPerrorFixer();
