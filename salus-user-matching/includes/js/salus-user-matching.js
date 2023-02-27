jQuery(document).ready(function($) {
    $(".connect-button").click(function(event) {
        event.preventDefault();
        var connectButton = $(this);
        connectButton.addClass("loading");
        connectButton.html("Sending request...");
        var profile_id = connectButton.data("profile-id");
        var data = {
            action: "salus_user_matching_send_request",
            profile_id: profile_id,
            match_percentage: ' . $match_percentage . ',
        };
        $.post(ajaxurl, data, function(response) {
            if (response === "success") {
                connectButton.removeClass("loading");
                connectButton.html("Connection Request Sent");
            } else {
                connectButton.removeClass("loading");
                connectButton.html("Error Sending Request");
            }
        });
        });
        });