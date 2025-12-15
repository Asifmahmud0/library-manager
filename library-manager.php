<?php
/**
 * Plugin Name: Library Manager
 * Description: A React-based book management system with custom DB tables.
 * Version: 1.0.0
 * Author: MD ASIF IKBAL
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'LIBRARY_MANAGER_PATH', plugin_dir_path( __FILE__ ) );
define( 'LIBRARY_MANAGER_URL', plugin_dir_url( __FILE__ ) );

// Include Classes
require_once LIBRARY_MANAGER_PATH . 'includes/class-library-db.php';
require_once LIBRARY_MANAGER_PATH . 'includes/class-library-api.php';
require_once LIBRARY_MANAGER_PATH . 'includes/class-library-admin.php';

// Activation Hook: Create Database
register_activation_hook( __FILE__, [ 'Library_DB', 'create_table' ] );

// Initialize Classes
function library_manager_init() {
    new Library_API();
    new Library_Admin();
}
add_action( 'plugins_loaded', 'library_manager_init' );