<?php

// Send the connection request email to the user being requested.
function send_connection_request_email( $requested_user_id, $matching_percentage ) {
    // Get the user being requested.
    $requested_user = get_user_by( 'id', $requested_user_id );
    
    // Get the user requesting the connection.
    $requesting_user = wp_get_current_user();
    
    // Set the email subject.
    $subject = 'Connection request from ' . $requesting_user->display_name;

    // Set the email message.
    $message = '<img src="https://zealous-sammet.107-152-32-141.plesk.page/wp-content/uploads/2022/12/Screenshot-2022-12-04-at-21.23.48.jpg" alt="SalusPlay Logo" style="display: block; width: 200px; margin: 0 auto;">

    Hello ' . $requested_user->display_name . ', 

    You have received a connection request from ' . $requesting_user->display_name . '. Their profile is a ' . $matching_percentage . '% match with yours. 

    You can accept or decline the request by logging into your account on the website.

    Best regards,
    The SalusPlay Team';

    // Set the email headers.
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send the email to the user being requested.
    wp_mail( $requested_user->user_email, $subject, $message, $headers );
}

// Handle the AJAX request.
add_action( 'wp_ajax_salus_user_matching_send_request', 'salus_user_matching_send_request' );
function salus_user_matching_send_request() {
    // Get the ID of the user being requested.
    $requested_user_id = intval( $_POST['profile_id'] );

    // Get the percentage match between the two users.
    $matching_percentage = sanitize_text_field( $_POST['match_percentage'] );

    // Send the connection request email.
    send_connection_request_email( $requested_user_id, $matching_percentage );

    // Return a success message to the client.
    echo 'success';

    // Always exit to avoid further processing.
    wp_die();
}
