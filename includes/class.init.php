<?php

class rngshl_init {

    public $version;
    public $slug;

    public function __construct($version, $slug) {
        $this->version = $version;
        $this->slug = $slug;
        add_action('plugins_loaded', array($this, 'plugins_loaded'));
        add_action("admin_enqueue_scripts",array($this,"admin_enqueue_scripts"));
        $this->load_modules();
    }

    public function plugins_loaded() {
        load_plugin_textdomain( $this->slug , false, RNGSHL_PRT . "/languages" );
        require_once trailingslashit(__DIR__) . "translate.php";
    }
    
    public function admin_enqueue_scripts($hook){
        if($hook == "tools_page_shl_click_view"){
            wp_enqueue_style("shl-click-view-style", RNGSHL_PDU . "admin/assets/css/style.css");
            wp_enqueue_script("shl-click-view-scripts", RNGSHL_PDU . "admin/assets/js/script.js");
        }
    }
    
    public function load_modules(){
        require_once 'class.controller.shortlink.php';
        require_once 'class.controller.settings.php';
        require_once 'class.controller.clicked.php';
    }

}
