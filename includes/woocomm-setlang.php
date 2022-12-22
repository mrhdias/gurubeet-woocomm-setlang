<?php

//
// Package Gurubeet Woocomm Popup Set language
// Last Modification: Thu Dec 22 17:59:14 WET 2022
//

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists('Gurubeet_WooCommSetLang')) {

    class Gurubeet_WooCommSetLang {
        // private $debug = false;
        private $version_styles;
        private $version_scripts;
        public $options_page_hook;

        public function __construct() {

            if ( is_admin() ) {
                add_action( 'admin_menu', array($this, 'register_woocommerce_submenu'), 999 );
                add_action( 'admin_init', array($this, 'woocomm_setlang_register_settings') );

            } else {

                $this->version_styles = '2022122001';
                $this->version_scripts = '2022122110';

                add_action( 'wp_enqueue_scripts', array( $this, 'wp_styles_gurubeet_woocomm_setlang' ), 9999 );
                add_action( 'wp_enqueue_scripts', array( $this, 'wp_scripts_gurubeet_woocomm_setlang' ) );

                add_action( 'wp_body_open', array( $this, 'popup_set_language') );
            }
        }

        public function wp_styles_gurubeet_woocomm_setlang() {
            wp_register_style('gurubeet-woocomm-setlang-styles',
                plugins_url('../assets/css/styles.css', __FILE__),
                array(),
                $this->version_styles,
               'all');
               wp_enqueue_style('gurubeet-woocomm-setlang-styles');
        }

        public function wp_scripts_gurubeet_woocomm_setlang() {
            wp_register_script( 'gurubeet-woocomm-setlang-script',
                plugins_url('../assets/js/scripts.js', __FILE__),
                array(),
                $this->version_scripts,
                true);
            wp_enqueue_script( 'gurubeet-woocomm-setlang-script' );
        }

        public function register_woocommerce_submenu() {
            // https://techglimpse.com/add-submenu-woocommerce-wp-plugin/
            // https://developer.wordpress.org/reference/functions/add_menu_page/
            // https://developer.wordpress.org/reference/functions/add_submenu_page/

            $parent_slug = 'woocommerce';

            $this->options_page_hook = add_submenu_page(
                $parent_slug,
                esc_html__('Customer Set Language Popup', 'gurubeet-woocomm-setlang'),
                esc_html__('Customer Set Language Popup', 'gurubeet-woocomm-setlang'),
                'manage_woocommerce',
                'gurubeet_woocomm_setlang_submenu',
                array( $this, 'gurubeet_woocomm_setlang_settings_page_callback' )
            );
        }

        public function woocomm_setlang_register_settings() {

            register_setting(
                'gurubeet_woocomm_setlang_plugin_options',
                'gurubeet_woocomm_setlang_plugin_options',
                array( $this, 'woocomm_setlang_plugin_options_validate' )
            );

            add_settings_section(
                'gurubeet_woocomm_setlang_plugin_config',              // Slug-name to identify the section
                'Customer Popup Set Language Configuration',           // Formatted title of the section
                array( $this, 'woocomm_setlang_plugin_section_text' ), // Function that echos out any content at the top of the section
                'gurubeet_woocomm_setlang_plugin'                      // The slug-name of the settings page on which to show the section
            );

            add_settings_field(
                'gurubeet_woocomm_setlang_plugin_setting_json_config',        // Slug-name to identify the field
                'JSON Configuration',                                         // Formatted title of the field
                array( $this, 'woocomm_setlang_plugin_setting_json_config' ), // Function that fills the field with the desired form inputs
                'gurubeet_woocomm_setlang_plugin',                            // The slug-name of the settings page
                'gurubeet_woocomm_setlang_plugin_config'                      // The slug-name of the section of the settings page in which to show the box
            );

            add_settings_field(
                'gurubeet_woocomm_setlang_plugin_setting_status',
                'Status',
                array( $this, 'woocomm_setlang_plugin_setting_status'),
                'gurubeet_woocomm_setlang_plugin',
                'gurubeet_woocomm_setlang_plugin_config'
            );

        }


        public function woocomm_setlang_plugin_section_text() {
            echo '<p>Here you can configure the popup</p>';
        }

        public function woocomm_setlang_plugin_setting_json_config() {
            $options = get_option( 'gurubeet_woocomm_setlang_plugin_options' );
            if (empty($options['json_config'])) {
                $options['json_config'] = '{}';
            }
            // $this->json_config = $options['json_config'];
            $cached_json_file = sprintf('%s/cache/%s/%s', WP_CONTENT_DIR, 'gurubeet-woocomm-setlang', 'popup-config.json');
            include( 'section-textarea.php' );
        }

        public function woocomm_setlang_plugin_setting_status() {
            $options = get_option( 'gurubeet_woocomm_setlang_plugin_options' );
            if(!isset($options['status'])) {
                $options['status'] = 0;
            }

            echo sprintf('<input type="checkbox" id="%s" name="%s" value="1" %s /><label for="%s">The Popup is %s</label>',
                "gurubeet_woocomm_setlang_plugin_setting_status",
                "gurubeet_woocomm_setlang_plugin_options[status]",
                checked( 1, $options['status'], false ),
                "gurubeet_woocomm_setlang_plugin_setting_status",
                ($options['status'] == 1) ? "Enabled" : "Disabled");

        }


        private function key_comparison($default_data, $data) {

            foreach ( array_keys($default_data) as $key ) {

                if ( is_array($default_data[$key]) ) {
                    if ( is_array($data[$key]) ) {
                        $result = $this->key_comparison($default_data[$key], $data[$key]);
                        if ( count($result) > 0 ) {
                            array_unshift($result[0], $key);
                            return $result;
                        }
                    } else {
                        return array(array($key), __('missing keyword', 'gurubeet-woocomm-setlang'));
                    }
                } elseif ( !array_key_exists($key, $data) ) {
                    return array(array($key), __('missing keyword', 'gurubeet-woocomm-setlang'));
                } elseif ( gettype($default_data[$key]) !== gettype($data[$key]) ) {
                    return array(array($key), __('wrong type', 'gurubeet-woocomm-setlang'));
                }
            }

            return array();
        }

        private function json_validator( &$data_string ) {
            // https://developer.wordpress.org/reference/functions/add_settings_error/
            if ( empty($data_string) || !is_string($data_string) ) {
                return __('Empty JSON string', 'gurubeet-woocomm-setlang');
            }
            $data = json_decode($data_string, true);
            if (! is_array($data)) {
                return __('Invalid JSON string', 'gurubeet-woocomm-setlang');
            }

            if ( count($data) == 0 ) {
                return __('Empty JSON structure', 'gurubeet-woocomm-setlang');
            }

            $default_data = array(
                'max_hours' => 0,
                'country_codes' => array(
                    'lang'    => '',
                    'page'    => '',
                    'default' => ''
                ),
                'texts' => array(
                    'header' => '',
                    'body'   => array(
                        'information' => '',
                        'button'      => array(
                            'text' => '',
                            'flag' => ''
                        )
                    ),
                    'footer' => ''
                )
            );

            foreach( $data as $key => $data_value ) {
                // error_log('Country Code Key: ' . $key . ' Value: '. $data_value);

                $result = $this->key_comparison($default_data, $data_value);
                // error_log('Country Code Key: ' . $key);
                if ( count($result) > 0 ) {
                    array_unshift($result[0], $key);
                    // error_log('Error: ' . join(' > ', $result[0]) . ' Msg: ' . $result[1]);
                    return sprintf(__('The key "%s" has an error (%s) in json configuration for "%s"', 'gurubeet-woocomm-setlang'),
                        end($result[0]),
                        $result[1],
                        join(' > ', $result[0]));
                }
            }

            $data_string = json_encode($data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return '';
        }

        public function woocomm_setlang_plugin_options_validate( $input ) {
            if ( empty($input['json_config']) ) {
                $input['json_config'] = '{}';
            }

            $result = $this->json_validator($input['json_config']);
            if ( $result === '' ) {
                // error_log('save to backup file to: ' . getcwd());

                $wp_cache_path = sprintf('%s/cache', WP_CONTENT_DIR);
                if (file_exists($wp_cache_path) ) {
                    $cache_path = sprintf('%s/%s', $wp_cache_path, 'gurubeet-woocomm-setlang');
                    if (!file_exists($cache_path) ) {
                        mkdir($cache_path, 0755, true);
                    }
                    file_put_contents(sprintf('%s/%s', $cache_path, 'popup-config.json'), $input['json_config']);
                } else {
                    add_settings_error(
                        'json_config',
                        esc_attr( 'json_configuration_updated' ),
                        sprintf(__('The directory "%s" not exist!', 'gurubeet-woocomm-setlang'), 'wp-content/cache'),
                        'error'
                    );
                    $input['status'] = 0; // disable the popup
                }
            } else {
                add_settings_error(
                    'json_config',
                    esc_attr( 'json_configuration_updated' ),
                    $result,
                    'error'
                );
                $input['status'] = 0; // disable the popup
            }

            return $input;
        }

        public function gurubeet_woocomm_setlang_settings_page_callback() {
            // https://deliciousbrains.com/create-wordpress-plugin-settings-page/
            // require('settings.php');
            include( 'page-settings.php' );
        }

        public function popup_set_language() {
            $options = get_option( 'gurubeet_woocomm_setlang_plugin_options' );
            if (empty($options['status']) || $options['status'] != 1) {
                error_log('The Customer Popup Set Language is Disabled!');
                return;
            }

            // if the url of request uri is different of ip language show a popup to change the language
            // http://www.example.com/pt-pt and the country is not PT show the popup
            echo '<div id="modal-set-language" style="display:none;"></div>';
        }
    }

}

if ( is_null( new Gurubeet_WooCommSetLang() ) ) {
    error_log( 'Error Gurubeet_WooCommSetLang' );
    wp_die();
}

?>
