<?php

class rngshl_controller {

    public function __construct() {
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'metaboxes_init'));
        }
        register_activation_hook(RNGSHL_FILE, array($this,"add_shortlink_rewrite_rule"));
        add_action("template_redirect","shortlink_to_mainlink");
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
    
    public function add_shortlink_rewrite_rule(){
        add_rewrite_rule("^p([0-9]+)/?$", 'index.php?shl_id=$matches[1]',"top");
        add_rewrite_tag("%shl_id%","([0-9]+)");
        flush_rewrite_rules();
    }

    private function get_cookie($cookie_name){}

    private function set_cookie($cookie_name,$id){}

    private function update_cookie($cookie_name,$id){}

    private function remove_cookie($cookie_name){}

    private function update_click_event($meta_key,$post_id){}

    /*
     * redirect shortlink to mainlink by post_id and shl_id query variable 
     * 1.(set/update) cookie
     * 2.click event set postmeta *shl_click_event
     * 3.redirect to main link
     */
    
    public function shortlink_to_mainlink(){
        $id = get_query_var("shl_id");
        if(!isset($id) || empty($id))
            return;
        $cookie_name = "shl_click_event";
        $meta_key = "shl_click_event";
        $permalink = get_the_permalink($id);
        $cookie_value = $this->get_cookie($cookie_name);
        if($cookie_value){
            if(in_array($id, $cookie_value)){
                wp_redirect($permalink);
            }else{
                $this->update_cookie($cookie_name,$id);
                $this->update_click_event($meta_key,$id);
                wp_redirect($permalink);
            }
        }else{
            $this->set_cookie($cookie_name,$id);
            $this->update_click_event($meta_key,$id);
            wp_redirect($permalink);
        }
    }

}

$shortlink_controller = new rngshl_controller();
