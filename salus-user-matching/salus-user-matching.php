<?php
/*
Plugin Name: Salus - User Matching
Plugin URI: https://voilaestudio.es
Description: A plugin to calculate the percentage match between a user and another user based on skills.
Version: 1.0.0
Author: Voilà Estudio Creativo
Author URI: https://voilaestudio.es
*/


// Define the plugin directory path.
define( 'SALUS_USER_MATCHING_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

function salus_user_matching_init() {
    if ( ! class_exists( 'BuddyPress' ) ) {
        return;
    }

    // Load the "shortcode-and-UI.php" file.
    require_once SALUS_USER_MATCHING_PLUGIN_DIR_PATH . 'includes/shortcode-and-UI.php';

    // Load the "salus-user-matching-send-request.php" file.
    require_once SALUS_USER_MATCHING_PLUGIN_DIR_PATH . 'includes/salus-user-matching-send-request.php';

    // Register the "skills_match_percentage" shortcode.
    add_shortcode( 'skills_match_percentage', 'calculate_skill_match' );

    // Register the AJAX action for sending connection requests.
    add_action( 'wp_ajax_salus_user_matching_send_request', 'salus_user_matching_send_request' );
    add_action( 'wp_ajax_nopriv_salus_user_matching_send_request', 'salus_user_matching_send_request' );

    // Register the AJAX action for accepting connection requests.
    add_action( 'wp_ajax_salus_connection_request_accept', 'salus_connection_request_accept' );
    add_action( 'wp_ajax_nopriv_salus_connection_request_accept', 'salus_connection_request_accept' );

    // Enqueue the CSS and JavaScript files.
    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_style( 'salus-connection-requests-style', plugin_dir_url( __FILE__ ) . 'css/salus-connection-requests.css', array(), '1.0.0', 'all' );
        wp_enqueue_script( 'salus-match-button', plugin_dir_url( __FILE__ ) . 'js/salus-match-button.js', array( 'jquery' ), '1.0.0', true );
    } );
}



add_action( 'init', 'salus_user_matching_init' );

// Enqueue the JavaScript file.
function salus_user_matching_enqueue_scripts() {
    wp_enqueue_script( 'salus-match-button', plugin_dir_url( __FILE__ ) . 'js/salus-match-button.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'salus_user_matching_enqueue_scripts' );

// Registers the "My Connections" shortcode
function salus_connection_requests_shortcode() {
    ob_start();
    include( plugin_dir_path( __FILE__ ) . 'templates/salus-connection-requests.php' );
    return ob_get_clean();
}
add_shortcode( 'salus_connection_requests', 'salus_connection_requests_shortcode' );

// Create DB Tables for User - User Connection Request 
function salus_user_matching_create_friend_requests_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'salus_friend_requests';

    // Create the table if it does not exist.
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
      id int(11) NOT NULL AUTO_INCREMENT,
      requester_id int(11) NOT NULL,
      recipient_id int(11) NOT NULL,
      match_percentage int(11) NOT NULL,
      status varchar(20) NOT NULL,
      created_at datetime NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    
    // Check for any errors returned by dbDelta.
    if ( ! empty( $wpdb->last_error ) ) {
        error_log( 'Error creating table: ' . $wpdb->last_error );
    }
}


add_action( 'wp_enqueue_scripts', 'salus_user_matching_enqueue_scripts' );
add_action( 'plugins_loaded', 'salus_user_matching_init' );
add_shortcode( 'salus_connection_requests', 'salus_connection_requests_shortcode' );
register_activation_hook( __FILE__, 'salus_user_matching_create_friend_requests_table' );
