<?php

class rngshl_controller {

    public function __construct() {
        if (is_admin()) {
            add_action('add_meta_boxes', array($this, 'metaboxes_init'));
        }
        add_action("init", array($this, "add_shortlink_rewrite_rule"));
        add_action("admin_notices", array($this, "first_flush_notice"));
        add_action("update_option_permalink_structure", array($this, "update_first_flush"));
        add_action("template_redirect", array($this, "shortlink_to_mainlink"));
        add_shortcode("rngshl_shortlink", array($this, "shortcode_shortlink"));
    }

    private function get_settings() {
        $option = get_option("rngshl_general_setting_option");
        $active_flag = FALSE;
        $post_types = array();
        if (isset($option)) {
            if (!empty($option['rngshl-active-post-type'])) {
                $post_types = $option['rngshl-active-post-type'];
                $active_flag = TRUE;
            }
        } else {
            $post_types = array('post');
            $active_flag = TRUE;
        }

        return array(
            'active_flag' => $active_flag,
            'post_types' => $post_types
        );
    }

    public function shortcode_shortlink($atts) {
        $array_atts = shortcode_atts(
                array(
            'wrapper_class' => '',
                ), $atts, 'rngshl_shortlink'
        );
        $params = $this->get_settings();
        extract($params);
        ob_start();
        global $post;
        if(in_array($post->post_type, $post_types)){
            require_once RNGSHL_TMP . 'shortcode-shortlink.php';
        }
        return ob_get_clean();
    }

    public function metaboxes_init() {
        $params = $this->get_settings();
        extract($params);
        if ($active_flag) {
            add_meta_box("shortlink_init", __("Shortlink", "rng-shortlink"), array($this, 'shortlink_metabox_input'), $post_types, "side", "low");
        }
    }

    public function shortlink_metabox_input($post) {
        require_once RNGSHL_ADM . 'metabox-shortlink.php';
    }

    public function add_shortlink_rewrite_rule() {
        add_rewrite_rule("^p([0-9]+)/?$", 'index.php?shl_id=$matches[1]', "top");
        add_rewrite_tag("%shl_id%", "([0-9]+)");
    }

    private function pop_max_id(&$array) {
        if (count($array) > 20) {
            while (count($array) > 20) {
                array_pop($array);
            }
        }
        return;
    }

    private function get_cookie($cookie_name) {
        $clicked_posts = $_COOKIE[$cookie_name];
        if (isset($clicked_posts) and ! empty($clicked_posts)) {
            return unserialize($clicked_posts);
        } else {
            return FALSE;
        }
    }

    private function set_cookie($cookie_name, $id) {
        if (is_array($id)) {
            $cookie_value = serialize(array_map("intval", $id));
        } else {
            $cookie_value = serialize(array_map("intval", array($id)));
        }
        $result = setcookie($cookie_name, $cookie_value, time() + YEAR_IN_SECONDS, "/");
    }

    private function update_cookie($cookie_name, $id) {
        $clicked_posts = $this->get_cookie($cookie_name);
        if (!is_array($clicked_posts))
            return FALSE;
        $result = array_unshift($clicked_posts, $id);
        if ($result) {
            $this->pop_max_id($clicked_posts);
            $this->remove_cookie($cookie_name);
            $this->set_cookie($cookie_name, $clicked_posts);
        } else {
            return FALSE;
        }
    }

    private function remove_cookie($cookie_name) {
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, '', time() - 3600, '/');
    }

    private function update_click_event($meta_key, $post_id) {
        $count_click = get_post_meta($post_id, $meta_key, TRUE);
        if (isset($count_click) && !empty($count_click)) {
            $new_count_click = intval($count_click) + 1;
            $new_count_click = strval($new_count_click);
            update_post_meta($post_id, $meta_key, $new_count_click);
        } else {
            delete_post_meta($post_id, $meta_key);
            update_post_meta($post_id, $meta_key, '1');
        }
    }

    /*
     * redirect shortlink to mainlink by post_id and shl_id query variable 
     * 1.(set/update) cookie
     * 2.click event set postmeta *shl_click_event
     * 3.redirect to main link
     */

    public function shortlink_to_mainlink() {
        $id = get_query_var("shl_id");
        if (!isset($id) || empty($id))
            return;
        $params = $this->get_settings();
        extract($params);
        $cookie_name = "shl_click_event";
        $meta_key = "shl_click_event";
        $permalink = get_the_permalink($id);
        $post_type = get_post_type($id);

        if (!in_array($post_type, $post_types)) {
            wp_redirect($permalink);
            return true;
        }

        $clicked_posts = $this->get_cookie($cookie_name);
        if ($clicked_posts) {
            if (in_array($id, $clicked_posts)) {
                wp_redirect($permalink);
            } else {
                $this->update_cookie($cookie_name, $id);
                $this->update_click_event($meta_key, $id);
                wp_redirect($permalink);
            }
        } else {
            $this->remove_cookie($cookie_name);
            $this->set_cookie($cookie_name, $id);
            $this->update_click_event($meta_key, $id);
            wp_redirect($permalink);
        }
    }

    public function update_first_flush() {
        update_option("rngshl_first_flush", "true");
    }

    private function first_flush_check() {
        return get_option("rngshl_first_flush");
    }

    public function first_flush_notice() {
        if ($this->first_flush_check())
            return;
        ?><div class="updated"><p><?php esc_html_e("To make the rng-shortlink plugin worked Please first "); ?><a href="<?php echo get_admin_url(); ?>/options-permalink.php" title="<?php esc_html_e("Permalink Settings", "rng-shortlink") ?>" ><?php esc_html_e("Flush rewrite rules", "rng-shortlink"); ?></a></p></div><?php
    }

}

new rngshl_controller();
