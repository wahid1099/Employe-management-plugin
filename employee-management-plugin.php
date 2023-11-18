<?php
/*
Plugin Name: Employee Management
Description: Employee appointment and payment management plugin.
Version: 1.0.1
Author: MD WAHID
*/

// Activation Hook: This function will be executed when the plugin is activated.
register_activation_hook(__FILE__, 'employee_management_activate');

// Enqueue styles and scripts
function enqueue_styles_and_scripts() {
    wp_enqueue_style('employee-management-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('employee-management-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_styles_and_scripts');

// Include necessary files
require_once(plugin_dir_path(__FILE__) . 'includes/admin/admin-functions.php');
require_once(plugin_dir_path(__FILE__) . 'includes/public/public-functions.php');

// Activation function to create the database table
function employee_management_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'employee_appointments';
$table_version = '1.0';

$sql = "CREATE TABLE $table_name (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    employee_email VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    time_interval VARCHAR(50) NOT NULL,
    PRIMARY KEY (id)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
update_option('employee_management_version', $table_version);

}


register_uninstall_hook(__FILE__, 'employee_management_uninstall');

function employee_management_uninstall() {
    // Drop the table on plugin uninstall
    global $wpdb;
    $table_name = $wpdb->prefix . 'employee_appointments';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}


// Enqueue admin styles
function enqueue_admin_styles() {
    wp_enqueue_style('admin-styles', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'enqueue_admin_styles');


// employee-mngmt-plugin.php

function enqueue_datatables() {
    // Enqueue locally hosted DataTables CSS and JS
    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', array(), '3.5.1');

    wp_enqueue_style('datatables-css', plugin_dir_url(__FILE__) . 'assets/DataTables/datatables.min.css');
    wp_enqueue_script('datatables-js', plugin_dir_url(__FILE__) . 'assets/DataTables/datatables.min.js', array('jquery'), '1.13.7', true);
}
add_action('wp_enqueue_scripts', 'enqueue_datatables');

function enqueue_jquery() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'enqueue_jquery');
