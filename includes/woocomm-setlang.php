<?php

//
// Package Gurubeet Woocomm Popup Set language
// Last Modification: Mon Dec 19 22:26:15 WET 2022
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

                $this->version_styles = '2022121902';
                $this->version_scripts = '2022121909';

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
?>
<style>
#gurubeet_woocomm_setlang_plugin_setting_json_config {
    width: 100%;
    font-size: initial;
    font-family: monospace;
    white-space: nowrap;
    height: 464px;
}
#gurubeet_woocomm_setlang_copy {
    margin: 0 4px;
}
#gurubeet_woocomm_setlang_copy > .dashicons {
    vertical-align: middle;
}
</style>
<?php
            echo sprintf('<textarea type="text" id="%s" name="%s" cols="50" rows="6">%s</textarea><br /><button type="button" id="gurubeet_woocomm_setlang_add_example" class="button">Add Example</button><button type="button" id="gurubeet_woocomm_setlang_copy" class="button"><span class="dashicons dashicons-clipboard"></span> Copy</button>',
                "gurubeet_woocomm_setlang_plugin_setting_json_config",
                "gurubeet_woocomm_setlang_plugin_options[json_config]",
                esc_attr( $options['json_config'] ) );
?>
<script>
/* <![CDATA[ */
document.addEventListener('DOMContentLoaded', () => {

    function file_get_contents(filename, destination) {
        fetch(filename).then((resp) => resp.text()).then(data => {
            // console.log('Data: ' + data);
            if (data.length > 0 && typeof(destination) != 'undefined' && destination != null) {
                destination.value = data;
            }
        }).catch(function (err) {
            // There was an error
            console.warn('Something went wrong.', err);
        });
    }

    const buttonAddExample = document.getElementById('gurubeet_woocomm_setlang_add_example');
    if(typeof(buttonAddExample) != 'undefined' && buttonAddExample != null) {
        buttonAddExample.onclick = function(event) {
            // console.log('click button...');
            let url = new URL('wp-content/plugins/gurubeet-woocomm-setlang/example/popup-config.json', document.location.origin);
            url.searchParams.append('version', '2022121901');
            // console.log('URL: ' + url.href);
            file_get_contents(url, event.currentTarget.parentNode.children[1]);
        }
    }

    const buttonCopy = document.getElementById('gurubeet_woocomm_setlang_copy');
    if(typeof(buttonCopy) != 'undefined' && buttonCopy != null) {
        buttonCopy.onclick = function(event) {
            // console.log('click button...');
            // event.currentTarget.parentNode.children[1].value;
            // event.currentTarget.parentNode.children[1].select();

            const textarea = event.currentTarget.parentNode.children[1];
            textarea.focus();
            navigator.clipboard.writeText(textarea.value);
            setTimeout(() => {
                textarea.blur();
            }, 1000);
        }
    }
});
/* ]]> */
</script>
<?php

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

        private function json_validator( &$data_string ) {
            // https://developer.wordpress.org/reference/functions/add_settings_error/
            if ( empty($data_string) || !is_string($data_string) ) {
                return false;
            }
            $data = json_decode($data_string, true);
            if (! is_array($data)) {
                return false;
            }
            $data_string = json_encode($data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return true;
        }

        public function woocomm_setlang_plugin_options_validate( $input ) {
            if ( empty($input['json_config']) ) {
                $input['json_config'] = '{}';
            }

            if ( !$this->json_validator($input['json_config']) ) {
                add_settings_error( 'json_config', esc_attr( 'json_configuration_updated' ), __('Invalid JSON string', 'gurubeet-woocomm-setlang'), 'error' );
                $input['status'] = 0; // disable the popup
            }

            // $newinput['api_key'] = trim( $input['api_key'] );
            // if ( ! preg_match( '/^[a-z0-9]{32}$/i', $newinput['api_key'] ) ) {
            //     $newinput['api_key'] = '';
            // }
            // return $newinput;

            // foreach( $input as $key => $value ) {
            //     error_log('Key: ' . $key . ' Value: ' . $value);
            // }

            return $input;
        }

        public function gurubeet_woocomm_setlang_settings_page_callback() {
            // https://deliciousbrains.com/create-wordpress-plugin-settings-page/
            // require('settings.php');

?>
<style>
.gurubeet_woocomm_setlang_title {
    border-bottom: 1px solid #aaa;
    padding-bottom: 10px;
    margin-right: 10px;
}
.gurubeet_woocomm_setlang_form > table {
    width: calc(100% - 10px);
    border-top: 1px solid #aaa;
    border-bottom: 1px solid #aaa;
    margin-bottom: 10px;
}
.gurubeet_woocomm_setlang_form > table > tbody {
    border-bottom: 1px solid #aaa;
}
</style>
<h2 class="gurubeet_woocomm_setlang_title">Gurubeet Woocomm Set Language Popup Plugin Settings</h2>
<?php settings_errors(); ?>
<form class="gurubeet_woocomm_setlang_form" action="options.php" method="post">
    <?php
    settings_fields( 'gurubeet_woocomm_setlang_plugin_options' );
    do_settings_sections( 'gurubeet_woocomm_setlang_plugin' );
    ?>
    <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
</form>
<?php
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
