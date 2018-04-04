<?php
/*
  Plugin Name: RNG_Shortlink
  Description: wordpress plugin that create shortlink for public post types in both of admin panel (with metabox) and front end (with shortcode) by usnig query variables . 
  Version: 1.0
  Author: abolfazl sabagh
  Author URI: http://asabagh.ir
  License: GPLv2 or later
  Text Domain: rng-shortlink
 */

define(RNGSHL_PRF, plugin_basename( __FILE__ ));//rng-shortlink/rng-shortlink.php
define(RNGSHL_PDU, plugin_dir_url(__FILE__));   //http://localhost:8888/rng-plugin/wp-content/plugins/rng-shortlink/
define(RNGSHL_PRT, basename(__DIR__));          //rng-refresh.php
define(RNGSHL_PDP, plugin_dir_path(__FILE__));  //Applications/MAMP/htdocs/rng-plugin/wp-content/plugins/rng-shortlink
define(RNGSHL_TMP, RNGSHL_PDP . "/public/");     // view OR templates System for public 
define(RNGSHL_ADM, RNGSHL_PDP . "/admin/");      // view OR templates System for admin panel

require_once 'includes/class.init.php';
$refresh_init = new rngshl_init(0.5,'rng-shortlink');