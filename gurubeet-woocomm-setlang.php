<?php
/*
 * Plugin Name:          Gurubeet Woocomm Popup Set language
 * Plugin URI:           https://github.com/mrhdias/gurubeet-woocomm-setlang
 * Description:          Popup for the customer set the chosen language for the store
 * Author:               Henrique Dias
 * Author URI:           https://github.com/mrhdias
 * Version:              0.0.1
 * Requires at least:    6.0
 * Tested up to:         6.1.1
 * Requires PHP:         7.3
 * License:              MIT License
 * Text Domain:          gurubeet-woocomm-setlang
 * Domain Path:          /languages/
 * WC requires at least: 7.1
 * WC tested up to:      7.2
 */

// Last Modification: Tue Jan 03 04:58:44 PM WET 2023
// zip -r gurubeet-woocomm-setlang-0.00.zip gurubeet-woocomm-setlang

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/* Init Hook */

function gurubeet_woocomm_setlang_load_textdomain() {
    load_plugin_textdomain( 'gurubeet-woocomm-setlang', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'gurubeet_woocomm_setlang_load_textdomain' );

require_once(sprintf("%s/%s", plugin_dir_path( __FILE__ ), 'includes/woocomm-setlang.php'));

function gurubeet_woocomm_setlang_plugin_activate() {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    $plugins = array(
        'woocommerce/woocommerce.php' => 'Woocommerce',
        'woocommerce-multilingual/wpml-woocommerce.php' => 'Woocommerce-Multilingual',
    );

    foreach ($plugins as $plugin => $name) {
        if ( !is_plugin_active( $plugin ) ) {
            echo __(sprintf('<div>The dependency <strong>"%s"</strong> is not installed</div>', $name), 'gurubeet-woocomm-setlang');
            //Adding @ before will prevent XDebug output
            @trigger_error(__('Please update all dependencies before activating.', 'gurubeet-woocomm-setlang'), E_USER_ERROR);
        }
    }
}
register_activation_hook( __FILE__, 'gurubeet_woocomm_setlang_plugin_activate' );

?>
