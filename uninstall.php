<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
//delte options
$options = array(
    "rngshl_configure_dismiss",
    "rngshl_general_setting_option",
    "rngshl_first_flush"
    
);
foreach ($options as $option) {
    if (get_option($option)) {
        delete_option($option);
    }
}
// drop a metadata
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = 'shl_click_event'");
// flush rewrite rules
flush_rewrite_rules();
