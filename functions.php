<?php

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