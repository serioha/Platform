<?php
/*
Plugin Name: DiSC Assessment Plugin
Plugin URI: https://your-website.com/
Description: A WordPress plugin for conducting DiSC personality assessments.
Version: 1.0.0
Author: Your Name
Author URI: https://your-website.com/
License: GPLv2 or later
Text Domain: disc-assessment
*/

// Define plugin constants
define( 'DISC_ASSESSMENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DISC_ASSESSMENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include plugin files
require_once DISC_ASSESSMENT_PLUGIN_DIR . 'disc-assessment-settings.php';
require_once DISC_ASSESSMENT_PLUGIN_DIR . 'disc-assessment-admin.php';
require_once DISC_ASSESSMENT_PLUGIN_DIR . 'disc-assessment-frontend.php';
require_once DISC_ASSESSMENT_PLUGIN_DIR . 'disc-assessment-logic.php';
require_once DISC_ASSESSMENT_PLUGIN_DIR . 'disc-assessment-report.php';

// Register activation hook
register_activation_hook( __FILE__, 'disc_assessment_activate' );

// Register deactivation hook
register_deactivation_hook( __FILE__, 'disc_assessment_deactivate' );

// Activation function
function disc_assessment_activate() {
    // Create database tables
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Questions table
    $table_name = $wpdb->prefix . 'disc_questions';
    $sql = "CREATE TABLE $table_name (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        question TEXT NOT NULL,
        answer_options TEXT NOT NULL,
        category VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    // Results table
    $table_name = $wpdb->prefix . 'disc_results';
    $sql = "CREATE TABLE $table_name (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        test_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        scores TEXT NOT NULL,
        styles TEXT NOT NULL,
        interpretations TEXT NOT NULL
    ) $charset_collate;";
    dbDelta( $sql );

    // Users table
    $table_name = $wpdb->prefix . 'disc_users';
    $sql = "CREATE TABLE $table_name (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    dbDelta( $sql );
}

// Deactivation function
function disc_assessment_deactivate() {
    // Remove database tables (optional)
    // You can use the same SQL statements as in the activation function, but with DROP TABLE instead of CREATE TABLE.
}
