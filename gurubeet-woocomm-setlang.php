<?php
/*
 * Plugin Name:          Gurubeet Woocomm Popup Set language
 * Plugin URI:           https://github.com/mrhdias/gurubeet-woocomm-setlang
 * Description:          Popup for the customer set the chosen language for the store
 * Author:               Henrique Dias
 * Author URI:           https://github.com/mrhdias
 * Version:              0.0.1
 * Requires at least:    7.2
 * Tested up to:         7.3
 * Requires PHP:         7.3
 * License:              MIT License
 * Text Domain:          gurubeet-woocomm-setlang
 * Domain Path:          /languages/
 * WC requires at least: 7.1
 * WC tested up to:      7.2
 */

// Last Modification: Thu Dec 22 21:59:27 WET 2022
// zip -r gurubeet-woocomm-setlang-0.00.zip gurubeet-woocomm-setlang

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
