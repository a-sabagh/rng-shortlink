<?php

defined('ABSPATH') || exit;

class rngshl_init {

    /**
     * plugin version
     * @var Integer 
     */
    public $version;

    /**
     * plugin slug
     * @var String
     */
    public $slug;

    /**
     * constructor
     * @param Int $version
     * @param String  $slug
     */
    public function __construct($version, $slug) {
        $this->version = $version;
        $this->slug = $slug;
        add_action('plugins_loaded', array($this, 'plugins_loaded'));
        add_action("admin_enqueue_scripts", array($this, "admin_enqueue_scripts"));
        $this->load_modules();
    }

    /**
     * load text domain and include common string tranlate
     */
    public function plugins_loaded() {
        load_plugin_textdomain($this->slug, false, RNGSHL_PRT . "/languages");
        require_once trailingslashit(__DIR__) . "translate.php";
    }

    /**
     * enqueue scripts and styles in wordpress
     * @param String $hook
     */
    public function admin_enqueue_scripts($hook) {
        if ($hook == "tools_page_shl_click_view") {
            wp_enqueue_style("shl-click-view-style", RNGSHL_PDU . "admin/assets/css/style.css");
            wp_enqueue_script("shl-click-view-scripts", RNGSHL_PDU . "admin/assets/js/script.js");
        }
    }

    /**
     * load class files as moduels
     */
    public function load_modules() {
        require_once 'class.controller.shortlink.php';
        require_once 'class.controller.settings.php';
        require_once 'class.controller.clicked.php';
    }

}
