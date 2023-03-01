<?php

// Send a connection request email to the user being requested.
function salus_user_matching_send_request() {
    // Get the ID of the user being requested.
    $requested_user_id = isset( $_POST['profile_id'] ) ? intval( $_POST['profile_id'] ) : 0;

    // Get the percentage match between the two users.
    $matching_percentage = isset( $_POST['match_percentage'] ) ? intval( $_POST['match_percentage'] ) : 0;

    if ( $requested_user_id > 0 && $matching_percentage > 0 ) {
        // Get the user being requested.
        $requested_user = get_user_by( 'id', $requested_user_id );

        // Get the user requesting the connection.
        $requesting_user = wp_get_current_user();

        // Set the email subject.
        $subject = 'Connection request from ' . $requesting_user->display_name;

        // Set the email message.
        $message = '<html><body>';
        $message .= '<img src="https://zealous-sammet.107-152-32-141.plesk.page/wp-content/uploads/2022/12/Screenshot-2022-12-04-at-21.23.48.jpg" alt="SalusPlay Logo" style="max-width: 200;">';
        $message .= '<p>Hello ' . $requested_user->display_name . ',</p>';
        $message .= '<p>You have received a connection request from ' . $requesting_user->display_name . '. Their profile is a ' . $matching_percentage . '% match with yours.</p>';
        $message .= '<p>You can accept or decline the request by logging into your account on the website.</p>';
        $message .= '<p>Best regards,</p>';
        $message .= '<p>The SalusPlay Team</p>';
        $message .= '</body></html>';

        // Set the email headers.
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: SalusPlay Team <no-reply@salusplay.com>',
        );

        // Send the email to the user being requested.
        $sent = wp_mail( $requested_user->user_email, $subject, $message, $headers );

        // Send a success or error response.
        if ( $sent ) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }

    wp_die();
}
add_action( 'wp_ajax_salus_user_matching_send_request', 'salus_user_matching_send_request' );
add_action( 'wp_ajax_nopriv_salus_user_matching_send_request', 'salus_user_matching_send_request' );

/**
 * Calculates the percentage match between the logged-in user and the profile being viewed
 * based on their shared skills.
 *
 * @return string The match percentage followed by a percentage sign or "N/A" if no skills match.
 */

 function calculate_skill_match() {
    // Get the IDs of the logged-in user and the profile being viewed.
    $current_user_id = get_current_user_id();
    $profile_user_id = bp_displayed_user_id();

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

    // Get the profile name and url.
    $profile_user = get_user_by( 'id', $profile_user_id );
    $profile_name = $profile_user->display_name;
    $profile_url = bp_core_get_user_domain( $profile_user_id );

    // Create the "Connect" button with the matching percentage and a spinner.
    $button_html = '<a class="connect-button" data-profile-id="' . $profile_user_id . '">' . $match_percentage . '% Match! | Connect with ' . $profile_name . '<span class="spinner"></span></a>';

    // If the match percentage is below 75%, show the missing skills message instead.
    if ( $match_percentage < 75 ) {
        $missing_skills = array_diff( $profile_user_skills, $current_user_skills );
        $missing_skills_html = implode( ', ', $missing_skills );
        $message_html = 'Your matching percentage is ' . $match_percentage . '%, seems like you\'re missing ' . $missing_skills_html . '. You can get them <a href="https://www.salusplay.com/">here!</a>';
        $button_html = '<button class="connect-button" disabled>' . $button_html . '</button>';
    } else {
        $message_html = '';
    }

    // Add an event listener to the "Connect" button to send an AJAX request.
    $script_html = '<script>
        jQuery(document).ready(function($) {
            $(".connect-button").click(function(event) {
                event.preventDefault();
                var connectButton = $(this);
                connectButton.addClass("loading");
                connectButton.html("Sending request...");
                var profile_id = connectButton.data("profile-id");
                var data = {
                    action: "salus_user_matching_send_request",
                    profile_id: profile_id,
                    match_percentage: ' . $match_percentage . ',
                };
                $.post(ajaxurl, data, function(response) {
                    if (response === "success") {
                        connectButton.removeClass("loading");
                        connectButton.html("Connection Request Sent");
                    } else {
                        connectButton.removeClass("loading");
                        connectButton.html("Error Sending Request");
                    }
                });
                });
                });
                </script>';

                return $button_html . $message_html . $script_html;
                }