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

    // Load the "matching-algorithm.php" file.
    //require_once SALUS_USER_MATCHING_PLUGIN_DIR_PATH . 'includes/matching-algorithm.php';

    // Load the "email-functionality.php" file.
    // require_once SALUS_USER_MATCHING_PLUGIN_DIR_PATH . 'includes/email-functionality.php';

    // Load the "shortcode-and-UI.php" file.
    require_once SALUS_USER_MATCHING_PLUGIN_DIR_PATH . 'includes/shortcode-and-UI.php';

    // Register the "skills_match_percentage" shortcode.
    add_shortcode( 'skills_match_percentage', 'calculate_skill_match' );
}
add_action( 'plugins_loaded', 'salus_user_matching_init' );
