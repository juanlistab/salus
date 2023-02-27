jQuery(document).ready(function($) {
    // Handle the click event on the "Connect" button.
    $('body').on('click', '.connect-button:not(.disabled)', function(e) {
        e.preventDefault();

        // Disable the button to prevent further clicks.
        $(this).addClass('disabled');

        // Show a "loading" message on the button.
        $(this).text('Sending request...');

        // Get the ID of the profile being requested.
        var profile_id = $(this).data('profile-id');

        // Get the matching percentage.
        var match_percentage = $(this).text().match(/(\d+)/)[0];

        // Send the AJAX request to send the connection request email.
        $.ajax({
            type: 'POST',
            url: salus_user_matching_ajax.ajax_url,
            data: {
                action: 'salus_user_matching_send_request',
                profile_id: profile_id,
                match_percentage: match_percentage
            },
            success: function(response) {
                if (response === 'success') {
                    // Update the button text to show that the request was sent successfully.
                    $(this).text('Request sent!');

                    // Disable the button to prevent further clicks.
                    $(this).addClass('disabled');
                } else {
                    // Show an error message on the button.
                    $(this).text('Error!');
                }
            }.bind(this),
            error: function() {
                // Show an error message on the button.
                $(this).text('Error!');
            }.bind(this)
        });
    });
});
