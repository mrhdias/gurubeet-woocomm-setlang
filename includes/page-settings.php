<?php defined( 'ABSPATH' ) || exit; ?>
<style>
.gurubeet_woocomm_setlang_title {
    /* border-bottom: 1px solid #aaa; */
    /* padding-bottom: 10px; */
    /* margin-right: 10px; */
}
#gurubeet_woocomm_setlang_form > table {
    /* width: calc(100% - 10px); */
    border-top: 1px solid #aaa;
    border-bottom: 1px solid #aaa;
    margin-bottom: 10px;
}
#gurubeet_woocomm_setlang_form > table > tbody {
    /* border-bottom: 1px solid #aaa; */
}
</style>
<div class="wrap">
    <h2 class="gurubeet_woocomm_setlang_title">Gurubeet Woocomm Set Language Popup Plugin Settings</h2>
    <h2 class="nav-tab-wrapper"><?php settings_errors(); ?></h2>
    <form id="gurubeet_woocomm_setlang_form" action="options.php" method="post">
        <?php
        settings_fields( 'gurubeet_woocomm_setlang_plugin_options' );
        do_settings_sections( 'gurubeet_woocomm_setlang_plugin' );
        ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
</div>
