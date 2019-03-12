<?php
defined('ABSPATH') || exit;

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

    /**
     * get settings from admin panel and action based on this
     * @return Array
     */
    private function get_settings() {
        $option = get_option("rngshl_general_setting_option");
        $post_types = (isset($option) and ! empty($option)) ? (array) $option['rngshl-active-post-type'] : array('post');

        return $post_types;
    }

    /**
     * callback function of shortlink shortcode
     * @global Object $post
     * @param Array $atts
     * @return String
     */
    public function shortcode_shortlink($atts) {
        $array_atts = shortcode_atts(
                array(
            'wrapper_class' => '',
                ), $atts, 'rngshl_shortlink'
        );
        $post_types = $this->get_settings();
        ob_start();
        global $post;
        if (in_array($post->post_type, $post_types)) {
            require_once RNGSHL_TMP . 'shortcode-shortlink.php';
        }
        return ob_get_clean();
    }

    /**
     * add shortlink metabox based on admin settings
     */
    public function metaboxes_init() {
        $post_id = intval($_GET['post']);
        $post_type = get_post_type($post_id);
        $post_types = $this->get_settings();
        if (in_array($post_type, $post_types)) {
            add_meta_box("shortlink_init", esc_html__("Shortlink", "rng-shortlink"), array($this, 'shortlink_metabox_input'), $post_types, "side", "low");
        }
    }

    /**
     * callback function of metabox init
     * @param Object $post
     */
    public function shortlink_metabox_input($post) {
        require_once RNGSHL_ADM . 'metabox-shortlink.php';
    }

    /**
     * add sortlink rewrite rule HOME_URL/p[PAGE_ID]
     */
    public function add_shortlink_rewrite_rule() {
        add_rewrite_rule("^p([0-9]+)/?$", 'index.php?shl_id=$matches[1]', "top");
        add_rewrite_tag("%shl_id%", "([0-9]+)");
    }

    /**
     * if array member count grather than 20 pop member since member count equals 20 
     * @param Array $array
     * @return Array
     */
    private function pop_max_id(&$array) {
        while (count($array) > 20) {
            array_pop($array);
        }
    }

    /**
     * get cookie and unserialize it
     * @param String $cookie_name
     * @return boolean
     */
    private function get_cookie($cookie_name) {
        $clicked_posts = $_COOKIE[$cookie_name];
        return (!empty($clicked_posts)) ? unserialize($clicked_posts) : false;
    }

    /**
     * set cookie in special format that be used in plugin
     * @param String $cookie_name
     * @param Integer $id
     */
    private function set_cookie($cookie_name, $id) {
        $cookie_value = serialize(array_map("intval", (array)$id));
        setcookie($cookie_name, $cookie_value, time() + YEAR_IN_SECONDS, "/");
    }

    /**
     * update cookie when click action is fire for visitor
     * @param String $cookie_name
     * @param Integer $id
     * @return boolean
     */
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

    /**
     * complatly remove cookie
     * @param String $cookie_name
     */
    private function remove_cookie($cookie_name) {
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, '', time() - 3600, '/');
    }

    /**
     * update clicked meta for each post
     * @param String $meta_key
     * @param Integer $post_id
     */
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
        $id = (int) get_query_var("shl_id");
        if (!isset($id) || empty($id))
            return;
        $post_types = $this->get_settings();
        $cookie_name = "shl_click_event";
        $meta_key = "shl_click_event";
        $permalink = get_the_permalink($id);
        $post_type = get_post_type($id);

        $clicked_posts = (array) $this->get_cookie($cookie_name);
        if (!in_array($post_type, $post_types) || in_array($id, $clicked_posts)) {
            wp_redirect($permalink);
            return;
        }

        if (array_filter($clicked_posts)) {
            //cookie exist then update cookie
            $this->update_cookie($cookie_name, $id);
            $this->update_click_event($meta_key, $id);
            wp_redirect($permalink);
        } else {
            //cookie not exist create cookie
            $this->remove_cookie($cookie_name);
            $this->set_cookie($cookie_name, $id);
            $this->update_click_event($meta_key, $id);
            wp_redirect($permalink);
        }
    }

    /**
     * check first flush in options
     */
    public function update_first_flush() {
        update_option("rngshl_first_flush", "true");
    }

    /**
     * check is first flush is run or not
     * @return String
     */
    private function first_flush_check() {
        return get_option("rngshl_first_flush");
    }

    /**
     * show first flush notice in admin panel on plugin install
     * @return type
     */
    public function first_flush_notice() {
        if ($this->first_flush_check()) {
            return;
        }
        ?><div class="updated"><p><?php esc_html_e("To make the rng-shortlink plugin worked Please first ", "rng-shortlink"); ?><a href="<?php echo get_admin_url(); ?>/options-permalink.php" title="<?php esc_attr_e("Permalink Settings", "rng-shortlink") ?>" ><?php esc_html_e("Flush rewrite rules", "rng-shortlink"); ?></a></p></div><?php
    }

}

new rngshl_controller();
