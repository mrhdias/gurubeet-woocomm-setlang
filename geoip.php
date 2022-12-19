<?php

//
// Package Gurubeet Woocomm Popup Set language
// Last Modification: Mon Dec 19 05:08:22 PM WET 2022
//

// https://www.lordelo.com/wp-content/plugins/gurubeet-woocomm-setlang/geoip.php

define( 'ABSPATH', '../../../');
// defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require(ABSPATH . 'wp-load.php');
include_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    exit;
}

if ( ! class_exists('Gurubeet_WooCommGeoIP')) {
    class Gurubeet_WooCommGeoIP {
        private $version;
        private $lang_country_code;
        private $ip_country;
        private $external_ip_address;
        private $data;
        private $disabled;

        public function __construct() {
            if ( is_admin() ) return;
            $this->version = "";
            $this->lang_country_code = "";
            $this->ip_country = "";
            $this->external_ip_address = "";
            $this->data = array();
            $this->enabled = false;
        }

        public function init() {
            if (!$this->validate_parameters()) {
                return false;
            }
            if (!$this->validate_options()) {
                return false;
            }

            if ( ! isset(WC_Geolocation::geolocate_ip()['country']) ) {
                error_log( 'Error Gurubeet_WooCommGeoIP WC_Geolocation::geolocate_ip' );
                wp_die();
            }

            $this->ip_country = strtoupper(WC_Geolocation::geolocate_ip()['country']);
            $this->external_ip_address = WC_Geolocation::get_external_ip_address();
            error_log('IP Country: ' . $this->ip_country . ' External IP Address: ' . $this->external_ip_address);

            $this->enabled = true;
            return $this->enabled;
        }

        public function validate_options() {
            $options = get_option( 'gurubeet_woocomm_setlang_plugin_options' );

            if ( !isset($options['status']) || $options['status'] != 1 ) {
                // error_log( 'The Customer Popup Set Language is Disabled!' );
                $this->send_json_data(array('error' => 'The Customer Popup Set Language is Disabled!'), 400);
                return false;
            }

            if ( !isset($options['json_config']) || !is_string($options['json_config']) ) {
                // error_log( 'Error Gurubeet_WooCommGeoIP' );
                $this->send_json_data(array('error' => 'Error Gurubeet_WooCommGeoIP WC_Geolocation::geolocate_ip'), 400);
                return false;
            }

            $this->data = json_decode($options['json_config'], true);
            if (! is_array($this->data)) {
                // error_log( 'Error Gurubeet_WooCommGeoIP' );
                $this->send_json_data(array('error' => 'Error Gurubeet_WooCommGeoIP WC_Geolocation::geolocate_ip'), 400);
                return false;
            }

            return true;
        }

        public function validate_parameters() {
            $this->version = $_GET['version'];
            if ( !isset($this->version) || empty($this->version) ) {
                $this->send_json_data(array('error' => 'No version'), 400);
                return false;
            }

            $this->lang_country_code = $_GET['lang_country_code'];
            if ( !isset($this->lang_country_code) ) {
                $this->send_json_data(array('error' => 'No lang_country_code'), 400);
                return false;
            }
            return true;
        }

        public function check_customer_ip_country() {
            if (!$this->enabled) return;

            $ip_country = strtoupper($this->ip_country);

            if ( ! empty($ip_country) && array_key_exists($ip_country, $this->data) ) {
                $this->data[$ip_country]['ip'] = $this->external_ip_address;
                $this->data[$ip_country]['country_codes']['ip'] = $this->external_ip_address;
                if ($this->data[$ip_country]['country_codes']['lang'] === $this->lang_country_code) {
                    $this->send_json_data(array('skip' => true));
                    return;
                }
                $this->send_json_data($this->data[$ip_country]);
                return;
            }
            $this->send_json_data(array());
        }


        public function send_json_data( $data, $status = 200 ) {
            header('Content-Type: application/json; charset=utf-8');
            if ($status === 400) {
                header("Status: 400 Bad Request");
            }
            echo json_encode( $data );
            return;
        }

    }
}

if ( !function_exists('gurubeet_woocomm_geoip') ) {

    function gurubeet_woocomm_geoip() {
        // header('Content-Type: application/json; charset=utf-8');

        // $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        // error_log($actual_link);
        // error_log('SERVER NAME: ' . $_SERVER['SERVER_NAME']);

        // if ($_SERVER['HTTP_HOST'] !== $_SERVER['SERVER_NAME']) {
        //     header('HTTP/1.0 401 Unauthorized');
        //     echo json_encode(array('error' => 'Forbidden'));
        //     return;
        // }


        $geo_ip = new Gurubeet_WooCommGeoIP();

        if ( is_null( $geo_ip ) ) {
            // error_log( 'Error Gurubeet_WooCommGeoIP' );
            header('Content-Type: application/json; charset=utf-8');
            header("Status: 500 Internal Server Error");
            echo json_encode(array('error' => 'Error Gurubeet_WooCommGeoIP WC_Geolocation::geolocate_ip'));
            return;
        }

        $geo_ip->init();
        $geo_ip->check_customer_ip_country();
    }

    gurubeet_woocomm_geoip();
}

?>
