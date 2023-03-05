<?php

/**
 * Calculates the percentage match between the logged-in user and the profile being viewed
 * based on their shared skills.
 *
 * @return string The match percentage followed by a percentage sign or "N/A" if no skills match.
 */
function calculate_skill_match() {
    // Get the IDs of the logged-in user and the profile being viewed.
    $current_user_id = get_current_user_id();
    $profile_user_id = bp_displayed_user_id();

    // Get the skills of the logged-in user and the profile being viewed.
    $current_user_skills = get_field( 'user_skills', 'user_' . $current_user_id );
    $profile_user_skills = get_field( 'user_skills', 'user_' . $profile_user_id );

    // If either user has no skills, return "N/A".
    if ( empty( $current_user_skills ) || empty( $profile_user_skills ) ) {
        return 'N/A';
    }

    // Find the skills that both users have.
    $matched_skills = array_intersect( $current_user_skills, $profile_user_skills );

    // Calculate the match percentage as a rounded percentage.
    $all_skills = array_unique( array_merge( $current_user_skills, $profile_user_skills ) );
    $match_percentage = round( count( $matched_skills ) / count( $all_skills ), 2 ) * 100;

    // Get the profile name and url.
    $profile_user = get_user_by( 'id', $profile_user_id );
    $profile_name = $profile_user->display_name;
    $profile_url = bp_core_get_user_domain( $profile_user_id );

    // Create the "Connect" button with the matching percentage and a spinner.
    $button_html = '';
    // Define missing_skills_html and message_html outside of the if statements.
    $missing_skills_html = '';
    $message_html = '';

    // If the match percentage is below 75%, show the missing skills message instead.
    // If the match percentage is above 75%, show the button to connect
    if ( $match_percentage >= 75 ) {
        $missing_skills = array_diff( $profile_user_skills, $current_user_skills );
        $missing_skills_html = implode( ', ', $missing_skills );
        $button_html = '<a class="connect-button" data-profile-id="' . $profile_user_id . '" data-match-percentage="' . $match_percentage . '">' . $match_percentage . '% Match! | Connect with ' . $profile_name . '<span class="spinner"></span></a>';

    } else {
        $missing_skills = array_diff( $profile_user_skills, $current_user_skills );
        $missing_skills_html = implode( ', ', $missing_skills );
        $message_html = 'Your matching percentage is ' . $match_percentage . '%, seems like you\'re missing ' . $missing_skills_html . '. You can get them <a href="https://www.salusplay.com/">here!</a>';
    }

    return $button_html . $message_html;
}
