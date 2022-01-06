<?php

/**
 *
 * @link              https://damiengoehrig.ca
 * @since             1.0.0
 * @package           Frontend_Login
 *
 * @wordpress-plugin
 * Plugin Name:       Frontend Login
 * Plugin URI:        https://damiengoehrig.ca
 * Description:       This plugin generate a login page build with Vue.js.
 * Version:           1.0.0
 * Author:            Damien Goehrig
 * Author URI:        https://damiengoehrig.ca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       frontend-login
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FRONTEND_LOGIN_VERSION', '1.0.0' );


// load templates name in page attributes
function vive_ship_add_page_template ($templates) {
    $templates['vm-acc.php'] = 'Vive Membership Account Page';
    return $templates;
    }
add_filter ('theme_page_templates', 'vive_ship_add_page_template', 10, 1);

// load page templates
function vive_ship_load_plugin_template( $template ) {

    if(  get_page_template_slug() === 'template-login.php' ) {

        if ( $theme_file = locate_template( array( 'template-login.php' ) ) ) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path( __DIR__ ) . 'frontend-login.php';
        }
    }

    if($template == '') {
        throw new \Exception('No template found');
    }

    return $template;
}

/* include hook file */
require plugin_dir_path( __FILE__ ) . 'hooks.php';
require plugin_dir_path( __FILE__ ) . 'rest.php';
