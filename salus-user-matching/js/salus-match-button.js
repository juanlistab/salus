// Add an event listener to the "Connect" button to send an AJAX request.
jQuery( document ).ready( function( $ ) {
    $( '.connect-button' ).click( function() {
        console.log( 'Connect button clicked!' );

        var profile_id = $( this ).data( 'profile-id' );
        console.log( 'Profile ID: ' + profile_id );

        var match_percentage = $( this ).data( 'match-percentage' );
        console.log( 'Match percentage: ' + match_percentage );

        $( this ).addClass( 'connecting' );
        console.log( 'Adding "connecting" class to button.' );

        var data = {
            'action': 'salus_user_matching_send_request',
            'profile_id': profile_id,
            'match_percentage': match_percentage,
            'security': $( this ).data( 'nonce' )
        };
        console.log( 'Sending AJAX request with data:', data );

        var button = $( this );

        $.post( ajaxurl, data, function( response ) {
            console.log( 'AJAX response:', response );

            button.removeClass( 'connecting' );
            console.log( 'Removing "connecting" class from button.' );

            if ( response == 'success' ) {
                console.log( 'Connection request sent successfully!' );

                button.text( 'Request Sent' );
                console.log( 'Changing button text to "Request Sent".' );

            } else if ( response == 'already_sent' ) {
                console.log( 'Connection request already sent!' );

                button.text( 'Request Already Sent' );
                console.log( 'Changing button text to "Request Already Sent".' );

            } else {
                console.log( 'Error sending connection request!' );

                button.text( 'Error Sending Request' );
                console.log( 'Changing button text to "Error Sending Request".' );

                button.removeClass( 'connecting' );
                console.log( 'Removing "connecting" class from button.' );
            }
        });
    });

    $( '.salus-connection-request-card-accept' ).click( function() {
        console.log( 'Accept button clicked!' );

        var requester_id = $( this ).data( 'requester' );
        console.log( 'Requester ID: ' + requester_id );

        var data = {
            'action': 'salus_connection_request_accept',
            'requester_id': requester_id,
            'security': $( this ).data( 'nonce' )
        };
        console.log( 'Sending AJAX request with data:', data );

        var card = $( this ).closest( '.salus-connection-request-card' );

        $.post( ajaxurl, data, function( response ) {
            console.log( 'AJAX response:', response );

            if ( response == 'success' ) {
                console.log( 'Connection request accepted successfully!' );

                card.fadeOut( function() {
                    card.remove();
                });

            } else {
                console.log( 'Error accepting connection request!' );
            }
        });
    });

    $( '.salus-connection-request-card-reject' ).click( function() {
        console.log( 'Reject button clicked!' );

        var requester_id = $( this ).data( 'requester' );
        console.log( 'Requester ID: ' + requester_id );

        var data = {
            'action': 'salus_connection_request_reject',
            'requester_id': requester_id,
            'security': $( this ).data( 'nonce' )
        };
        console.log( 'Sending AJAX request with data:', data );
    
        var card = $( this ).closest( '.salus-connection-request-card' );
    
        $.post( ajaxurl, data, function( response ) {
            console.log( 'AJAX response:', response );
    
            if ( response == 'success' ) {
                console.log( 'Connection request rejected successfully!' );
    
                card.fadeOut( function() {
                    card.remove();
                });
    
            } else {
                console.log( 'Error rejecting connection request!' );
            }
        });
    });
});    