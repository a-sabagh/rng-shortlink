<?php

defined('ABSPATH') || exit;

class rngshl_setting {

    /**
     * constructor
     */
    public function __construct() {
        if (is_admin()) {
            add_action("admin_init", array($this, "general_settings_init"));
            add_action("admin_menu", array($this, "admin_menu"));
            add_action("admin_notices", array($this, "configuration_notices"));
            add_action("admin_init", array($this, "dismiss_configuration"));
            add_filter('plugin_action_links_' . RNGSHL_PRF, array($this, 'add_setting_link'));
        }
    }

    /**
     * implement section top of general settings panel.
     * @param Array $args
     */
    public function general_setting_section_top($args) {
        esc_html_e("Check Post types You Want to Show Shortlink Metabox in Edit Panels", "rng-shortlink");
    }

    /**
     * 
     * @param type $args
     */
    public function general_setting_active_post_type($args) {
        $option = get_option("rngshl_general_setting_option");
        if (isset($option)) {
            $values = (!empty($option[$args['name']])) ? $option[$args['name']] : array();
        } else {
            $values = array('page');
        }
        $pt_args = array('public' => TRUE);
        $post_types = get_post_types($pt_args, 'names');
        $key = array_search("attachment", $post_types);
        unset($post_types[$key]);
        foreach ($post_types as $post_type):
            ?>
            <label>
                <?php echo $post_type ?>&nbsp;<input type="checkbox" name="rngshl_general_setting_option[<?php echo $args['name']; ?>][]" <?php echo (in_array($post_type, $values)) ? "checked" : ""; ?> value="<?php echo $post_type; ?>" >
            </label>
            <br>
            <?php
        endforeach;
    }

    public function general_settings_init() {
        register_setting("rngshl_general_setting", "rngshl_general_setting_option");
        add_settings_section(
                "rngshl-general-settings-top", esc_html__("shortlink plugin settings", "rng-shortlink"), array($this, "general_setting_section_top"), "rngshl_general_setting"
        );
        add_settings_field(
                "rngshl-active-post-type", esc_html__("sholtlink permission", "rng-shortlink"), array($this, "general_setting_active_post_type"), "rngshl_general_setting", "rngshl-general-settings-top", array(
            "label_for" => "rngshl-active-post-type",
            "name" => "rngshl-active-post-type",
            "class" => "regular-text",
            "custom_data" => "rngshl-active-post-type"
                )
        );
    }

    public function admin_menu() {
        add_submenu_page('options-general.php', esc_html__("Shortlink Settings", "rng-shortlink"), esc_html__("Shortlink Settings", "rng-shortlink"), 'administrator', 'shortlink-settings', array($this, "shortlink_setting_panel"));
    }

    public function shortlink_setting_panel() {
        require_once RNGSHL_ADM . 'setting-panel.php';
    }

    public function configuration_notices() {
        $dismiss = get_option("rngshl_configure_dismiss");
        if (!$dismiss) {
            ?>
            <div class="updated"><p><?php esc_html_e('RNG_Shortlink is activated, you may need to configure it to work properly.', 'rng-shortlink'); ?> <a href="<?php echo admin_url('options-general.php?page=shortlink-settings') ?>"><?php esc_html_e("Go to Settings page", "rng-shortlink"); ?></a> &ndash; <a href="<?php echo add_query_arg('rngshl_dismis_notice', 'true'); ?>"><?php esc_html_e("Dismiss", "rng-shortlink"); ?></a></p></div>
            <?php
        }
    }

    public function dismiss_configuration() {
        if ((isset($_GET['rngshl_dismis_notice']) and $_GET['rngshl_dismis_notice'] == "true") or ( isset($_GET['page']) and $_GET['page'] == "shortlink-settings" )) {
            update_option("rngshl_configure_dismiss", 1);
        }
    }

    public function add_setting_link($links) {
        $mylinks = array(
            '<a href="' . admin_url('options-general.php?page=shortlink-settings') . '">' . esc_html__("Settings", "rng-shortlink") . '</a>',
        );
        return array_merge($links, $mylinks);
    }

}

$rngshl_settings = new rngshl_setting();
