<?php
// Make sure the user is logged in.
if ( ! is_user_logged_in() ) {
    wp_die( 'You must be logged in to perform this action.' );
}

// Get the ID of the user who sent the request.
$requester_id = intval( $_POST['requester_id'] );

// Get the ID of the current user.
$user_id = get_current_user_id();

// Remove the friend request.
global $wpdb;
$table_name = $wpdb->prefix . 'salus_friend_requests';
$wpdb->delete(
    $table_name,
    array(
        'requester_id' => $requester_id,
        'recipient_id' => $user_id
    ),
    array( '%d', '%d' )
);

// Send a notification to the requester.
bp_notifications_add_notification( array(
    'user_id'           => $requester_id,
    'item_id'           => $user_id,
    'component_name'    => 'salus_user_matching',
    'component_action'  => 'rejected_connection',
    'date_notified'     => bp_core_current_time(),
    'is_new'            => 1
) );

// Return a success message.
echo 'success';
