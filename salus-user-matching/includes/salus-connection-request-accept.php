<?php
// Make sure the user is logged in.
if ( ! is_user_logged_in() ) {
    wp_die( 'You must be logged in to perform this action.' );
}

// Get the ID of the user who sent the request.
if ( isset( $_POST['requester_id'] ) ) {
    $requester_id = intval( $_POST['requester_id'] );
} else {
    wp_die( 'Requester ID not found.' );
}

error_log('Requester ID: ' . $requester_id);

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

// Add the connection to the user's connections.
$connections = get_user_meta( $user_id, 'salus_connections', true );
if ( ! is_array( $connections ) ) {
    $connections = array();
}
if ( ! in_array( $requester_id, $connections ) ) {
    $connections[] = $requester_id;
    update_user_meta( $user_id, 'salus_connections', $connections );
}

// Add the connection to the requester's connections.
$requester_connections = get_user_meta( $requester_id, 'salus_connections', true );
if ( ! is_array( $requester_connections ) ) {
    $requester_connections = array();
}
if ( ! in_array( $user_id, $requester_connections ) ) {
    $requester_connections[] = $user_id;
    update_user_meta( $requester_id, 'salus_connections', $requester_connections );
}

// Send a notification to the requester.
bp_notifications_add_notification( array(
    'user_id'           => $requester_id,
    'item_id'           => $user_id,
    'component_name'    => 'salus_user_matching',
    'component_action'  => 'new_connection',
    'date_notified'     => bp_core_current_time(),
    'is_new'            => 1
) );

// Return a success message.
echo 'success';

error_log('salus-connection-request-accept.php called');
