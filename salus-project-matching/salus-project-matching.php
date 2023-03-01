<?php
/**
 * Plugin Name: Project Matching
 * Plugin URI: https://voilaestudio.es
 * Description: A plugin to calculate the percentage match between a user and a project based on skills.
 * Version: 1.0.0
 * Author: Voilà Estudio Creativo
 * Author URI: https://voilaestudio.es
 */

 
// Add shortcode for project matching
add_shortcode('project_matching', 'project_matching_shortcode');

function project_matching_shortcode($atts)
{
    // Get current user ID and project ID
    $user_id = get_current_user_id();
    $project_id = get_the_ID();

    // Get skills of user and project
    $user_skills = get_user_meta($user_id, 'user-skills', true);
    $project_skills = get_field('project-skills', $project_id);

    // Calculate percentage match
    $common_skills = array_intersect($user_skills, $project_skills);
    $percentage_match = round(count($common_skills) / count($project_skills) * 100);

    // Load template file and pass in percentage match
    ob_start();
    include(plugin_dir_path(__FILE__) . 'project-matching-template.php');
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

// Override template for project page
add_filter('template_include', 'project_matching_template', 99);

function project_matching_template($template)
{
    if (is_singular('project')) {
        $new_template = plugin_dir_path(__FILE__) . 'project-matching-template.php';
        if ('' !== $new_template) {
            return $new_template;
        }
    }
    return $template;
}