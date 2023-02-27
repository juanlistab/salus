<?php

/**
 * Plugin Name: Salus
 * Plugin URI: https://voilaestudio.es
 * Description: A plugin to calculate the percentage match between a user and a project based on skills.
 * Version: 1.0.0
 * Author: Voilà Estudio Creativo
 * Author URI: https://voilaestudio.es
 */

/**
 * Displays the match percentage and "Connect" button.
 *
 * @return string The match percentage followed by a percentage sign or "N/A" if no skills match.
 */
function display_skill_match_percentage() {
    // Get the IDs of the logged-in user and the profile being viewed.
    $current_user_id = get_current_user_id();
    $profile_user_id = bp_displayed_user_id();

    // Get the email address of the user being viewed.
    $user_email = get_the_author_meta( 'user_email', $profile_user_id );

    // Get the skills of the logged-in user and the profile being viewed.
    $current_user_skills = get_field( 'user_skills', 'user_' . $current_user_id );
    $profile_user_skills = get_field( 'user_skills', 'user_' . $profile_user_id );

    // If either user has no skills, return "N/A".
    if ( empty( $current_user_skills ) || empty( $profile_user_skills ) ) {
        return 'N/A';
    }

    // Find the skills that both users have.
    $matched_skills = array_intersect( $current_user_skills, $profile_user_skills );

    // Calculate the match percentage as a rounded percentage.
    $all_skills = array_unique( array_merge( $current_user_skills, $profile_user_skills ) );
    $match_percentage = round( count( $matched_skills ) / count( $all_skills ), 2 ) * 100;

    // If the match percentage is above 75%, show the "Connect" button.
    if ( $match_percentage > 75 ) {
        // Get the profile name and url.
        $profile_user = get_user_by( 'id', $profile_user_id );
        $profile_name = $profile_user->display_name;
        $profile_url = bp_core_get_user_domain( $profile_user_id );

        // Get the subject and body of the email from the HTML template file.
        $subject = 'Connection Request';
        $body = file_get_contents( dirname( __FILE__ ) . '/email_templates/connection_request_email_template.php' );

        // Replace placeholders in the email body with dynamic content.
        $body = str_replace( '{first_name}', get_user_meta( $profile_user_id, 'first_name', true ), $body );
        $body = str_replace( '{requester_first_name}', bp_core_get_user_displayname( $current_user_id ), $body );
        $body = str_replace( '{percentage}', $match_percentage, $body );
        $body = str_replace( '{requester_profile_url}', bp_core_get_user_domain( $current_user_id ), $body );

        // Send the email.
    $to = $user_email;
    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    $sent = wp_mail( $to, $subject, $body, $headers );

    // Log the results of the email sending attempt.
    error_log( 'Email sent to ' . $to . ': ' . ( $sent ? 'success' : 'failure' ) );
}

    /**
     * Sends a connection request when the "Connect" button is clicked.
     */
    function send_connection_request() {
        // Get the ID of the user being viewed.
        $profile_user_id = bp_displayed_user_id();

        // Send the connection request email.
        send_connection_request_email( $profile_user_id );

        // Redirect the user to the profile page with a success message.
        wp_redirect( esc_url( $_GET['profile_url'] ) . '?connect_request_sent=1' );
        exit;
    }

    // Add the "Connect" button to the profile page.
    add_action( 'bp_profile_header_meta', 'display_skill_match_percentage' );

    // Handle the "Connect" button click.
    add_action( 'init', function() {
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'connect' ) {
            send_connection_request();
        }
    } );
