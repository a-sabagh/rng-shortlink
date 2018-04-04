<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
//delte options
$options = array(
    "rng_refresh_configure_dismiss",
    "refresh_general_setting_option"
);
foreach ($options as $option) {
    if (get_option($option)) {
        delete_option($option);
    }
}
// drop a metadata
global $wpdb;
$wpdb->query("DELETE * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'rngrf_is_refresh_active' OR meta_key='rngrf_is_refresh_active'");
