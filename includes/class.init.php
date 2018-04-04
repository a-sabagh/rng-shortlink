<?php

class rngshl_init {

    public $version;
    public $slug;

    public function __construct($version, $slug) {
        $this->version = $version;
        $this->slug = $slug;
        add_action('plugins_loaded', array($this, 'plugins_loaded'));
        $this->load_modules();
    }

    public function plugins_loaded() {
        load_plugin_textdomain( $this->slug , false, RNGSHL_PRT . "/languages" );
    }
    
    public function load_modules(){
        require_once 'class.controller.shortlink.php';
        require_once 'class.controller.settings.php';
    }

}
