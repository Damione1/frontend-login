<?php

function rest_login_api_init() {
    register_rest_route(
      'rest_login',
      'login',
      array(
        'methods'             => 'POST',
        'callback'            => 'rest_wp_login',
        'permission_callback' => function ( WP_REST_Request $request ) {
          return true;
        },
      )
    );

    register_rest_route(
      'rest_login',
      'register',
      array(
        'methods'             => 'POST',
        'callback'            => 'rest_wp_register',
        'permission_callback' => function ( WP_REST_Request $request ) {
          return true;
        },
      )
    );

    register_rest_route(
      'rest_login',
      'reset_password',
      array(
        'methods'             => 'POST',
        'callback'            => 'rest_wp_reset_password',
        'permission_callback' => function ( WP_REST_Request $request ) {
          return true;
        },
      )
    );

};
add_action( 'rest_api_init', 'rest_login_api_init', 10 );


/**
 * Login an user
 *
 * @param WP_REST_Request $request
 * @return void
 */
function rest_wp_login( WP_REST_Request $request ) {

  wp_set_current_user( 1 );
  wp_set_auth_cookie( 1 );

  $login_response = array(
    'data'    => array(),
    'code'    => 500,
    'message' => 'Undefined error',
  );

  $parameters = $request->get_json_params();

  check_required_fields( $parameters, array( 'username', 'password' ) );

  $parameters = apply_filters( 'before_custom_rest_login', $parameters );

  $signon_args = array(
    'user_login'    => sanitize_text_field( $parameters['username'] ?? '' ),
    'user_password' => sanitize_text_field( $parameters['password'] ?? '' ),
    'remember'      => true,
  );

  /* Allow sign-in with email or username */
  $username_exists = username_exists( $signon_args['user_login'] );
  if ( false === $username_exists ) {
    $user = get_user_by( 'email',  $signon_args['user_login'] );
    if ( false !== $user ) {
      $signon_args['user_login'] = $user->user_login;
    }
  }

  $user = wp_signon( $signon_args, false );

  $user = apply_filters( 'after_custom_rest_login', $user, $parameters );

  if ( ! is_wp_error( $user ) && ! empty( $user->data ) ) {
    unset( $user->data->user_pass );
    $login_response['data']    = $user->data;
    $login_response['code']    = 200;
    $login_response['message'] = _x( 'Login Successful. Redirecting...', 'rest-login', 'vtx' );
  } else {
    $login_response['code']    = 406;
    $login_response['message'] = join( '<br/>', $user->get_error_messages() );
  }

  $login_response = apply_filters( 'end_custom_rest_login', $login_response, $parameters, $user );
  return new WP_REST_Response( $login_response, 200 );

}


/**
 * Register an user
 *
 * @param WP_REST_Request $request
 * @return void
 */
function rest_wp_register( WP_REST_Request $request ) {

  $register_response = array(
    'data'    => array(),
    'code'    => 500,
    'message' => 'Undefined error',
  );

  $parameters = $request->get_json_params();

    check_required_fields( $parameters, array( 'username', 'password', 'email', 'first_name', 'last_name', 'birth_date' ) );


  $user_args = array(
    'user_login'  => sanitize_text_field( $parameters['username'] ?? '' ),
    'nickname'    => sanitize_text_field( $parameters['username'] ?? '' ),
    'user_email'  => sanitize_text_field( $parameters['email'] ?? '' ),
    'first_name'  => sanitize_text_field( $parameters['first_name'] ?? '' ),
    'last_name'   => sanitize_text_field( $parameters['last_name'] ?? '' ),
    'birth_date'  => sanitize_text_field( $parameters['birth_date'] ?? '' ),
    'user_pass'   => sanitize_text_field( $parameters['password'] ?? '' ),
    'user_status' => 0,
    'role'        => 'subscriber',
  );

  $user_args = apply_filters( 'before_insert_custom_rest_register', $user_args, $parameters );

  $username_exists = username_exists( $user_args['username'] );

  if ( ! $username_exists && email_exists( $user_args['user_email'] ) === false ) {

    $insert_user = wp_insert_user( wp_slash( $user_args ) );

    if ( ! is_wp_error( $insert_user ) ) {

      $user_args['ID'] = $insert_user;

      $activation_code = sha1( $user_args['ID'] . time() );
      $activation_link = add_query_arg(
        array(
          'key'  => $activation_code,
          'user' => $user_args['ID'],
        ),
        get_home_url() . '/connection'
      );
      add_user_meta( $user_args['ID'], 'has_to_be_activated', $activation_code, true );
      
      wp_mail( $user_args['user_email'], 'Confirmez votre compte', 'Bonjour! Veuillez confirmer votre compte en cliquant sur ce lien: ' . $activation_link );

      // WooCommerce specific code
      if ( class_exists( 'WooCommerce' ) ) {
        $user = get_user_by( 'id', $user_args['ID'] );
        $user->set_role( 'customer' );
      }

      $register_response['code']    = 200;
     /* Translators: %s is the username */
      $register_response['message'] = sprintf( _x( 'User %s registration was Successful. Please click on the email we sent you in order to generate your password', 'rest-login', 'vtx' ), $user_args['username'] );

    } else {
      $register_response['code']    = 400;
      $register_response['message'] = join( '<br/>', $insert_user->get_error_messages() );
    }
  } else {

    $register_response['code']    = 406;
    $register_response['message'] = _x( "Email already exists, please try 'Reset Password'", 'rest-login', 'vtx' );

  }

  return new WP_REST_Response( $register_response, $register_response['code'] );
}



/**
 * Send the reset password
 *
 * @param WP_REST_Request $request
 * @return void
 */
function rest_wp_reset_password( WP_REST_Request $request ) {

  $parameters              = $request->get_json_params();
  check_required_fields( $parameters, array( 'username' ) );
  $username_or_email = sanitize_text_field( $parameters['username'] ?? '' );
  $reset_password_response = array(
    'data'    => array(),
    'code'    => 500,
    'message' => 'Undefined error',
  );

  if ( empty( $username_or_email ) ) {
    $reset_password_response['code']    = 400;
    $reset_password_response['message'] = 'Username field is required';
    return new WP_REST_Response( $reset_password_response, $reset_password_response['code'] );
  }


  /* Allow sign-in with email or username */
  $username_exists = username_exists( $username_or_email );
  if ( false !== $username_exists ) {
    $username = $username_or_email;
  } else {
    $user = get_user_by( 'email',  $username_or_email );
    if ( false !== $user ) {
      $username = $user->user_login;
    }
  }

  if ( ! empty( $username ) ) {

    $retreive_password = retrieve_password( $username );

    if ( ! is_wp_error( $retreive_password ) && true === $retreive_password ) {

      $reset_password_response['code']    = 200;
      $reset_password_response['message'] = _x( 'Reset password sent. Please check your emails and folow the procedure.', 'rest-login', 'vtx' );

    } else {

      $reset_password_response['code']    = 400;
      $reset_password_response['message'] = join( '<br/>', $retreive_password->get_error_messages() );

    }
  } else {

    $reset_password_response['code']    = 406;
    $reset_password_response['message'] = 'Unable to find this username. Please try again';

  }

  return new WP_REST_Response( $reset_password_response, $reset_password_response['code'] );
}



 /**
  * Check if all required fields are provided. Die and
  *
  * @param array $parameters
  * @param array $required_fields
  * @param bool $die_if_missing
  * @return void|array|bool
  */
function check_required_fields( $parameters, $required_fields, $die_if_missing = true ) {
  $register_response = array();
  array_filter( $parameters );
  foreach ( $required_fields as $required_field ) {
    if ( ! in_array( $required_field, array_keys( $parameters ), true ) ) {
      $field_name = str_replace( '_', ' ', $required_field );
      /* Translators: required field check translation */
      $register_response['message'] = sprintf( _x( 'The %s field is required', 'rest-login', 'vtx' ), $field_name );
      $register_response['code']    = 400;
      if ( true === $die_if_missing ) {
        wp_send_json( $register_response ); /* Echo json then die */
      }
    }
  }

  if ( empty( $register_response ) ) {
    $register_response = true;
  }

  return $register_response;
}
