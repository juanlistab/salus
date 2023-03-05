// Add an event listener to the "Connect" button to send an AJAX request.
jQuery( document ).ready( function( $ ) {
    $( '.salus-match-button' ).on( 'click', function( event ) {
        event.preventDefault();

        var $button = $( this );
        var profile_id = $button.data( 'profile-id' );
        var match_percentage = $button.data( 'match-percentage' );

        $.ajax( {
            url: salus_user_matching_params.ajax_url,
            type: 'POST',
            data: {
                action: 'salus_user_matching_send_request',
                profile_id: profile_id,
                match_percentage: match_percentage,
            },
            beforeSend: function() {
                $button.addClass( 'loading' );
            },
            success: function( response ) {
                $button.removeClass( 'loading' );

                if ( response === 'existing' ) {
                    alert( 'You already sent a request to this user.' );
                } else if ( response === 'success' ) {
                    $button.text( 'Request Sent' );
                    $button.addClass( 'sent' );
                }
            },
            error: function( xhr, status, error ) {
                $button.removeClass( 'loading' );
                console.log( xhr );
                console.log( status );
                console.log( error );
            },
        } );
    } );
} );

