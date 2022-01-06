<?php
add_action( 'wp_enqueue_scripts', 'enqueue_vue_apps' );
function enqueue_vue_apps() {

  wp_register_script( 'vue', 'https://cdnjs.cloudflare.com/ajax/libs/vue/3.2.26/vue.cjs.js', array(), '3.2.26', true );

  if ( ! is_user_logged_in() && is_page_template( 'template-login.php' ) ) {
    wp_enqueue_script( 'vue-login-app', plugin_dir_path( __DIR__ ) . 'assets/vue-login-form.js', array( 'vue' ), '1.0', true );
  }

  wp_localize_script(
    'vue',
    'WordPress',
    array(
      'nonce' => wp_create_nonce( 'wp_rest' ), /* To authenticate rest calls */
    )
  );

}


/**
 * Redirect to home if login page is accessed while user is logged in
 *
 * @return void
 */
function redirect_login_page() {
  if ( is_page_template( 'template-login.php' ) && is_user_logged_in() ) {
    wp_safe_redirect( '/', 302, 'WordPress rest Login' );
    exit;
  }
}
add_action( 'template_redirect', 'redirect_login_page' );


/**
 * Redirect to login page after password reset
 *
 * @return void
 */
function wpse_lost_password_redirect() {
  wp_safe_redirect( '/connection' );
  exit;
}
add_action( 'password_reset', 'wpse_lost_password_redirect' );



/**
 * Activate the user if we are on the login page and user & key args are valid
 *
 * @return void
 */
function check_if_user_is_activated() {
  global $action_message;
  if ( is_page_template( 'template-login.php' ) ) {
    $user_id = filter_input( INPUT_GET, 'user', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
    if ( $user_id ) {
      // get user meta activation hash field
      $activation_code = get_user_meta( $user_id, 'has_to_be_activated', true );
      if ( filter_input( INPUT_GET, 'key' ) === $activation_code ) {
        delete_user_meta( $user_id, 'has_to_be_activated' );
        $action_message = _x( 'Email validated', 'rest-login', 'vtx' );
      } else {
        $action_message = _x( 'The activation code is not valid or the account is already activated', 'rest-login', 'vtx' );
      }
    }
  }
}
add_action( 'template_redirect', 'check_if_user_is_activated' );



/**
 * Add account activation check.
 * If the user have been registered by the rest api and is not yed activated, he will have a user meta with the activation code.
 * While this user meta exist, we block the access
 *
 * @param object $user
 * @param string $username
 * @return object
 */
function custom_wp_authenticate( $user, $username ) {

  $has_to_be_activated = get_user_meta( $user->ID, 'has_to_be_activated', true );

  if ( ! empty( $user ) && false !== $has_to_be_activated && '' !== $has_to_be_activated ) {
    $user = new WP_Error( 'activation_failed', _x( '<strong>ERROR</strong>: User is not activated.', 'rest-login', 'vtx' ) );
  }

  $ignore_codes = array( 'empty_username', 'empty_password' );

  if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes, true ) ) {
    do_action( 'wp_login_failed', $username );
  }

  return $user;
}
add_filter( 'authenticate', 'custom_wp_authenticate', 50, 9 );
