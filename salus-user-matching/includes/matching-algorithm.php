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
    
    // If both users have skills, calculate the match percentage.
    if ( ! empty( $current_user_skills ) && ! empty( $profile_user_skills ) ) {
        // Find the skills that both users have.
        $matched_skills = array_intersect( $current_user_skills, $profile_user_skills );
        
        // Calculate the match percentage as a rounded percentage.
        $all_skills = array_unique( array_merge( $current_user_skills, $profile_user_skills ) );
        $match_percentage = round( count( $matched_skills ) / count( $all_skills ), 2 ) * 100;

        return $match_percentage;
    } else {
        // If either user has no skills, return "N/A".
        return 'N/A';
    }
}
