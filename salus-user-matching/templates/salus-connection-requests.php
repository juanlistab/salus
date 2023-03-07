<div class="salus-connection-requests-container">
    <?php
    global $wpdb;

    $current_user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'salus_friend_requests';
    $friend_requests = $wpdb->get_results( $wpdb->prepare( "
        SELECT *
        FROM $table_name
        WHERE recipient_id = %d
    ", $current_user_id ) );

    if ( $friend_requests ) {
        foreach ( $friend_requests as $friend_request ) {
            $requester_id = $friend_request->requester_id;
            $match_percentage = $friend_request->match_percentage;

            // Get the name of the requester.
            $requester_name = bp_core_get_user_displayname( $requester_id );

            // Get the list of shared skills.
            $current_user_skills = get_user_meta( $current_user_id, 'user_skills', true );
            $requester_skills = get_user_meta( $requester_id, 'user_skills', true );

            if ( is_array( $current_user_skills ) && is_array( $requester_skills ) ) {
                $shared_skills = array_intersect( $current_user_skills, $requester_skills );
            } else {
                $shared_skills = array();
            }
            ?>

            <div class="salus-connection-request-card">
                <div class="salus-connection-request-card-content">
                    <div class="salus-connection-request-card-header">
                        <?php echo get_avatar( $requester_id, 50, '', '', array( 'class' => 'salus-connection-request-card-avatar' ) ); ?>
                        <h3 class="salus-connection-request-card-title"><?php echo $requester_name; ?></h3>
                    </div>
                    <div class="salus-connection-request-card-body">
                        <p><?php printf( __( '%s is a %d%% match for you with these skills:', 'salus-user-matching' ), $requester_name, $match_percentage ); ?></p>
                        <?php if ( ! empty( $shared_skills ) ) : ?>
                            <ul class="salus-connection-request-card-skills">
                                <?php if ( $shared_skills ) : ?>
                                    <?php $halfway = ceil( count( $shared_skills ) / 2 ); ?>
                                    <div class="column">
                                        <?php foreach ( array_slice( $shared_skills, 0, $halfway ) as $skill ) : ?>
                                            <li><?php echo $skill; ?></li>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="column">
                                        <?php foreach ( array_slice( $shared_skills, $halfway ) as $skill ) : ?>
                                            <li><?php echo $skill; ?></li>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <li><?php _e( 'No shared skills', 'salus-user-matching' ); ?></li>
                                <?php endif; ?>
                            </ul>

                        <?php else : ?>
                            <p><?php _e( 'No shared skills', 'salus-user-matching' ); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="salus-connection-request-card-footer">
                        <button class="salus-connection-request-card-accept" data-requester="<?php echo $requester_id; ?>" data-request-id="<?php echo $friend_request->id; ?>"><?php _e( 'Accept', 'salus-user-matching' ); ?></button>
                        <button class="salus-connection-request-card-reject" data-requester="<?php echo $requester_id; ?>" data-request-id="<?php echo $friend_request->id; ?>"><?php _e( 'Reject', 'salus-user-matching' ); ?></button>
                    </div>
                </div>
            </div>

            <?php
        }
    } else {
        echo '<p>' . __( 'You have no connection requests at this time.', 'salus-user-matching' ) . '</p>';
    }
    ?>
</div>
