<?php

/*
  Plugin Name: rng-shortlink
  Description: WordPress plugin that create a short link for public post types in both admin panel (with meta box) and front end (with shortcode) by using query variables.
  Version: 1.0
  Author: Abolfazl Sabagh
  Author URI: http://asabagh.ir
  License: GPLv2 or later
  Text Domain: rng-shortlink
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define("RNGSHL_FILE", __FILE__);
define("RNGSHL_PRF", plugin_basename(__FILE__)); //rng-shortlink/rng-shortlink.php
define("RNGSHL_PDU", plugin_dir_url(__FILE__));   //http://localhost:8888/rng-plugin/wp-content/plugins/rng-shortlink/
define("RNGSHL_PRT", basename(__DIR__));          //rng-refresh.php
define("RNGSHL_PDP", plugin_dir_path(__FILE__));  //Applications/MAMP/htdocs/rng-plugin/wp-content/plugins/rng-shortlink/
define("RNGSHL_TMP", RNGSHL_PDP . "public/");     // view OR templates System for public 
define("RNGSHL_ADM", RNGSHL_PDP . "admin/");      // view OR templates System for admin panel

require_once 'includes/class.init.php';
$refresh_init = new rngshl_init(0.5, 'rng-shortlink');
