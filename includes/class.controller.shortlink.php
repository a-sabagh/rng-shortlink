<?php

class rngshl_controller {

    public function __construct() {
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'metaboxes_init'));
        }
        add_shortcode("rngshl_shortlink", array($this, "shortcode_shortlink"));
    }

    public function shortcode_shortlink($atts) {
        $array_atts = shortcode_atts(
            array(
            'wrapper_class' => '',
                ), $atts, 'rngshl_shortlink'
        );
        ob_start();
        global $post;
        require_once RNGSHL_TMP . 'shortcode-shortlink.php';
        return ob_get_clean();
    }

    public function metaboxes_init() {
        $option = get_option("rngshl_general_setting_option");
        $active_flag = FALSE;
        if (isset($option)) {
            if (!empty($option['rngshl-active-post-type'])) {
                $post_types = $option['rngshl-active-post-type'];
                $active_flag = TRUE;
            }
        } else {
            $post_types = array('page');
            $active_flag = TRUE;
        }
        if ($active_flag) {
            add_meta_box("shortlink_init", __("Shortlink", "rng-shortlink"), array($this, 'shortlink_metabox_input'), $post_types, "side", "low");
        }
    }

    public function shortlink_metabox_input($post) {
        require_once RNGSHL_ADM . 'metabox-shortlink.php';
    }

}

$shortlink_controller = new rngshl_controller();
