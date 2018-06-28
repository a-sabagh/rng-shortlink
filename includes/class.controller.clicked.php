<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class rngshl_click_view {

    public $posts_per_page;
    
    public function __construct($number) {
        $this->posts_per_page = $number;
        add_action("admin_menu", array($this, "click_view_menu"));
    }

    public function click_view_menu() {
        add_submenu_page("tools.php", __("Click View Report", "rng-shortlink"), __("Click View"), "manage_options", "shl_click_view", array($this, "click_view_report"));
    }

    public function click_view_report() {
        $query_args = array(
            'meta_query' => array(
                array(
                    'key' => 'shl_click_event',
                    'type' => 'NUMERIC',
                    'value' => 0,
                    'compare' => '>'
                ),
            'posts_per_page' => $this->posts_per_page,
            )
        );
        require_once RNGSHL_ADM . 'click-view/click-view-report.php';
    }

}

new rngshl_click_view(15);
