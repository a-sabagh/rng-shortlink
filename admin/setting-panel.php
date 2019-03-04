<?php
if(!current_user_can("manage_options")){
    return;
}
if(isset($_GET['settings-updated']) and $_GET['settings-updated'] == TRUE){
    add_settings_error("rngshl_general_setting", "rngshl_general_setting" , __("Settings saved","rng-shortlink") , "updated");
}elseif(isset($_GET['settings-updated']) and $_GET['settings-updated'] == FALSE){
    add_settings_error("rngshl_general_setting", "rngshl_general_setting" , __("Error with saving","rng-shortlink"));
}
?>
<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form action="options.php" method="post">
        <?php 
        settings_fields("rngshl_general_setting");
        do_settings_sections("rngshl_general_setting");
        submit_button(__("save","rng-shortlink"));
        ?>
    </form>
</div>

