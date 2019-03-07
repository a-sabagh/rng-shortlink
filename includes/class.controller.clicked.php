<?php

defined('ABSPATH') || exit;

class rngshl_click_view {

    /**
     * posts per page in clicked list in admin panel.
     * used for pagination
     * @var Integer
     */
    private $posts_per_page;

    /**
     * pagination count in clicked list in admin panel.
     * used for pagination
     * @var Integer
     */
    private $paginate_count;

    /**
     * constructor
     * @param Integer $posts_per_page
     * @param Integer $paginate_count
     */
    public function __construct($posts_per_page, $paginate_count) {
        $this->posts_per_page = $posts_per_page;
        $this->paginate_count = $paginate_count;
        add_action("admin_menu", array($this, "click_view_menu"));
        add_action("admin_enqueue_scripts", array($this, "admin_localize_script"));
        add_action("wp_ajax_click_view_paginate", array($this, "click_view_paginate"));
        add_action("wp_ajax_click_view_next", array($this, "click_view_paginate"));
        add_action("wp_ajax_click_view_prev", array($this, "click_view_paginate"));
    }

    /**
     * localize admin script for admin ajax pagination
     */
    public function admin_localize_script() {
        $data = array("admin_url" => admin_url("admin-ajax.php"));
        wp_localize_script("shl-click-view-scripts", "SHL_OBJ", $data);
    }

    /**
     * posts_per_page getter
     * @return Integer
     */
    public function get_posts_per_page() {
        return $this->posts_per_page;
    }

    /**
     * paginate_count getter
     * @return Integer
     */
    public function get_paginate_count() {
        return $this->paginate_count;
    }

    /**
     * get all posts clicked count.
     * this function is used for pagination
     * @return Integer
     */
    public function posts_count_report() {
        $query_args = array(
            'meta_query' => array(
                array(
                    'key' => 'shl_click_event',
                    'type' => 'NUMERIC',
                    'value' => 0,
                    'compare' => '>'
                )
            ),
            'posts_per_page' => -1
        );
        return count(get_posts($query_args));
    }

    /**
     * add submenu in tool menu in admin panel for posts was clicked report
     */
    public function click_view_menu() {
        add_submenu_page("tools.php", esc_html__("Click View Report", "rng-shortlink"), esc_html__("Click View", "rng-shortlink"), "manage_options", "shl_click_view", array($this, "click_view_report"));
    }

    /**
     * get all posts clicked and call clicked posts views
     */
    public function click_view_report() {
        $current = 1;
        $posts_per_page = $this->get_posts_per_page();
        $paginate_count = $this->get_paginate_count();
        $posts_count = $this->posts_count_report();

        $query_args = array(
            'meta_query' => array(
                array(
                    'key' => 'shl_click_event',
                    'type' => 'NUMERIC',
                    'value' => 0,
                    'compare' => '>'
                )
            ),
            'posts_per_page' => $this->posts_per_page
        );
        require_once RNGSHL_ADM . 'click-view/click-view-report.php';
        require_once RNGSHL_ADM . 'click-view/click-view-pagination.php';
    }

    /**
     * prepare query after paginate execute
     */
    public function click_view_paginate() {
        $current = (int) $_POST['page'];
        $posts_per_page = $this->get_posts_per_page();
        $paginate_count = $this->get_paginate_count();
        $posts_count = $this->posts_count_report();
        $offset = ($current - 1) * $posts_per_page;
        $query_args = array(
            'meta_query' => array(
                array(
                    'key' => 'shl_click_event',
                    'type' => 'NUMERIC',
                    'value' => 0,
                    'compare' => '>'
                )
            ),
            'offset' => $offset,
            'posts_per_page' => $this->posts_per_page
        );
        ob_start();
        require_once RNGSHL_ADM . 'click-view/click-view-report-ajax.php';
        $report = ob_get_clean();
        ob_start();
        require_once RNGSHL_ADM . 'click-view/click-view-pagination-ajax.php';
        $pagination = ob_get_clean();
        $respons = array(
            'report' => $report,
            'pagination' => $pagination
        );
        echo wp_send_json($respons);
        wp_die();
    }

}

//$paginate_count must be odd
new rngshl_click_view(15, 7);
