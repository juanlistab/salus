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

// Register the AJAX endpoint for sending a connection request.
add_action( 'wp_ajax_nopriv_salus_user_matching_send_request', 'salus_user_matching_send_request' );
add_action( 'wp_ajax_salus_user_matching_send_request', 'salus_user_matching_send_request' );
