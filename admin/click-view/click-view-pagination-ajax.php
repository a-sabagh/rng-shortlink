<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$pages = ceil($posts_count / $posts_per_page);
$paginate_count_center = floor($paginate_count / 2);
if ($current <= $paginate_count_center + 1) {
    $start = 1;
    $end = ($paginate_count > $pages) ? $pages : $paginate_count;
    $prev_disable = ($current == $start) ? "rng-disable" : "prev";
    $next_disable = ($current == $end) ? "rng-disable" : "next";
} else {
    $start = ($current - $paginate_count_center) - max(($current + $paginate_count_center) - $pages, 0);
    $end = ($current + $paginate_count_center) - max(($current + $paginate_count_center) - $pages, 0);
    $prev_disable = ($current == $start) ? "rng-disable" : "prev";
    $next_disable = ($current == $end) ? "rng-disable" : "next";
}

/*
  echo "current: " . $current . "<br>";
  echo "posts_count: " . $posts_count . "<br>";
  echo "posts_per_pages: " . $posts_per_page . "<br>";
  echo "pages: " . $pages . "<br>";
  echo "paginate_count: " . $paginate_count . "<br>";
  echo "paginate_count_center: " . $paginate_count_center . "<br>";
  echo "start: " . $start . "<br>";
  echo "end: " . $end . "<br>";
  echo "" . $next_disable . "<br>";
  echo "" . $prev_disable . "<br>";
 */
?>

<li><a href="#" class="<?php echo $prev_disable; ?>" title="<?php esc_html_e("Prev", "rng-shortlink"); ?>"><?php esc_html_e("Prev", "rng-shortlink"); ?></a></li>
<?php
$i = $start;
while ($i <= $end):
    $list_class = ($i == $current) ? "rng-disable current" : "paginate";
    ?>
    <li><a href="#" class="<?php echo $list_class; ?>" data-paginate="<?php echo $i; ?>" title="<?php echo $i; ?>"><?php echo $i; ?></a></li>
    <?php
    $i++;
endwhile;
?>
<li><a href="#" class="<?php echo $next_disable; ?>" title="<?php esc_html_e("Next", "rng-shortlink"); ?>"><?php esc_html_e("Next", "rng-shortlink"); ?></a></li>
