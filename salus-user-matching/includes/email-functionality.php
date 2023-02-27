<?php

/**
 * Sends a connection request email to the user being requested.
 *
 * @param int $requested_user_id The ID of the user being requested.
 * @param int $matching_percentage The percentage match between the two users.
 */
function send_connection_request_email( $requested_user_id, $matching_percentage ) {
    // Get the user being requested.
    $requested_user = get_user_by( 'id', $requested_user_id );
    
    // Get the user requesting the connection.
    $requesting_user = wp_get_current_user();
    
    // Set the email subject.
    $subject = 'Connection request from ' . $requesting_user->display_name;

    // Set the email message.
    $message = '<html><body><img src="https://zealous-sammet.107-152-32-141.plesk.page/wp-content/uploads/2022/12/Screenshot-2022-12-04-at-21.23.48.jpg" width="200" height="auto" /><br /><br />' .
    'Hello ' . $requested_user->display_name . ',<br /><br /> 

    You have received a connection request from ' . $requesting_user->display_name . '. Their profile is a ' . $matching_percentage . '% match with yours. <br /><br />

    You can accept or decline the request by logging into your account on the website.<br /><br />

    Best regards,<br />
    The SalusPlay Team<br /><br /></body></html>';

    // Set the email headers.
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send the email to the user being requested.
    wp_mail( $requested_user->user_email, $subject, $message, $headers );

}
