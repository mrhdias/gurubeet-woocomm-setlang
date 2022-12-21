<?php defined( 'ABSPATH' ) || exit; ?>
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
<textarea type="text" id="gurubeet_woocomm_setlang_plugin_setting_json_config" name="gurubeet_woocomm_setlang_plugin_options[json_config]" cols="50" rows="6">
<?php
    echo esc_attr( $options['json_config'] );
?>
</textarea><br />
<button type="button" id="gurubeet_woocomm_setlang_add_example" class="button">Add Example</button>
<button type="button" id="gurubeet_woocomm_setlang_copy" class="button"><span class="dashicons dashicons-clipboard"></span> Copy</button>
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
            url.searchParams.append('version', '2022122101');
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
