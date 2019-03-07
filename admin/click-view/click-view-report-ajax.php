<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!current_user_can("manage_options")) {
    return;
}

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
