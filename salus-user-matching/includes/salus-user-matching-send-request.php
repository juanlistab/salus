<?php

function salus_user_matching_send_request() {
    // Get the current user ID and profile ID.
    $current_user_id = get_current_user_id();
    $profile_id = $_POST['profile_id'];
    $matching_percentage = $_POST['match_percentage'];

    // Output the variables to the error log for debugging.
    error_log('current_user_id: ' . $current_user_id);
    error_log('profile_id: ' . $profile_id);
    error_log('matching_percentage: ' . $matching_percentage);

    // Check if a request already exists for this user.
    global $wpdb;
    $table_name = $wpdb->prefix . 'salus_friend_requests';
    $existing_request = $wpdb->get_var( $wpdb->prepare( "
        SELECT status
        FROM $table_name
        WHERE requester_id = %d AND recipient_id = %d
    ", $current_user_id, $profile_id ) );

    if ( $existing_request ) {
        echo 'existing';
        wp_die();
    }

    // Insert a new friend request into the database.
    $wpdb->insert(
        $table_name,
        array(
            'requester_id' => $current_user_id,
            'recipient_id' => $profile_id,
            'match_percentage' => $matching_percentage,
            'status' => 'pending',
            'created_at' => current_time( 'mysql' ),
        ),
        array(
            '%d',
            '%d',
            '%d',
            '%s',
            '%s',
        )
    );

    // Send an email to the profile user.
    $to = get_the_author_meta( 'user_email', $profile_id );
    $subject = 'Friend Request from ' . bp_core_get_user_displayname( $current_user_id );
    $message = '<html><body>';
    $message .= '<img src="https://zealous-sammet.107-152-32-141.plesk.page/wp-content/uploads/2022/12/Screenshot-2022-12-04-at-21.23.48.jpg" alt="SalusPlay Logo" style="width: 200px;">';
    $message .= '<p>Hello ' . get_the_author_meta( 'display_name', $profile_id ) . ',</p>';
    $message .= '<p>You have received a friend request from ' . bp_core_get_user_displayname( $current_user_id ) . '. Their profile is a ' . $matching_percentage . '% match with yours.</p>';
    $message .= '<p>You can accept or reject the request by logging into your account on the website.</p>';
    $message .= '<p>Best regards,</p>';
    $message .= '<p>The SalusPlay Team</p>';
    $message .= '</body></html>';

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail( $to, $subject, $message, $headers );

    // Return a success response.
    echo 'success';
    wp_die();
}

