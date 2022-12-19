<?php

/*
 * Plugin Name:       Gurubeet Woocomm Popup Set language
 * Description:       Popup for the customer set the chosen language for the store
 * Author:            Henrique Dias
 * Version:           0.0.1
 * Requires at least: 5.8
 * Tested up to:      7.3
 * Requires PHP:      7.3
 * License:           GPL v2 or later
 * Text Domain:       gurubeet-woocomm-setlang
 * Domain Path:       /languages/
*/

// Last Modification: Mon Dec 19 05:04:24 PM WET 2022

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/* Init Hook */

include_once ABSPATH . 'wp-admin/includes/plugin.php';
foreach (array('woocommerce/woocommerce.php', 'woocommerce-multilingual/wpml-woocommerce.php') as $plugin) {
    if ( !is_plugin_active( $plugin ) ) {
        exit;
    }
}

function gurubeet_woocomm_setlang_load_textdomain() {
    load_plugin_textdomain( 'gurubeet-woocomm-setlang', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'gurubeet_woocomm_setlang_load_textdomain' );

require_once(sprintf("%s/%s", plugin_dir_path( __FILE__ ), 'includes/woocomm-setlang.php'));

?>
