<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!current_user_can("manage_options")) {
    return;
}
?>
<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <span class="admin-seprator-line"></span>
    <div class="shl-preloader"><span class="shl-spinner"></span></div>
    <table class="shl-click-view-table">
        <thead>
            <tr>
                <th><?php esc_html_e("Post Title", "rng-shortlink"); ?></th>
                <th><?php esc_html_e("Post Date", "rng-shortlink"); ?></th>
                <th><?php esc_html_e("Shortlink", "rng-shortlink"); ?></th>
                <th><?php esc_html_e("All Click", "rng-shortlink"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $posts = get_posts($query_args);
            foreach ($posts as $post):
                ?>
                <tr>
                    <td><a href="<?php echo get_the_permalink($post->ID); ?>" target="_blank" title="<?php echo get_the_title($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a></td>
                    <td><?php echo get_the_date("F d,Y", $post->ID); ?></td>
                    <td><code><?php echo home_url() . "/p" . $post->ID; ?></code></td>
                    <td><?php echo get_post_meta($post->ID, "shl_click_event", TRUE); ?></td>
                </tr>
                <?php
            endforeach;
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table><!--.shl-click-view-table-->
</div><!--.wrap-->

