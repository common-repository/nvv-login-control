<?php 

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Replace login url
 */
add_filter( 'login_headerurl', 'nvv_custom_login_logo_url' );
function nvv_custom_login_logo_url() {

  return nvv_login_control_home_url();
}

/**
 * Add login logo and styles
 */
add_action( 'login_head', 'nvv_login_head_add_css' );
function nvv_login_head_add_css() {

  ?>
  <style>
    .login {
      background-color: <?php echo get_option( '_nvv_login_background_color_page' ) ?>;
      background-image: url( <?php echo get_option( '_nvv_login_background_image_page' ) ?> );
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
     }
    .login form {
      background-color: <?php echo get_option( '_nvv_login_background_color_form' ) ?>;
      color: <?php echo get_option( '_nvv_login_color_font_form' ) ?>;
      background-image: url( <?php echo get_option( '_nvv_login_background_image_form' ) ?> );
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
     }
    .login #backtoblog a, .login #nav a {
      color: <?php echo get_option( '_nvv_login_color_font_page' ) ?>;
    }

    .login .submit input[type=submit] {
      background-color: <?php echo get_option( '_nvv_login_background_color_button_form' ) ?>;
      color: <?php echo get_option( '_nvv_login_color_button_form' ) ?>;
    }

    .login .submit input[type=submit]:hover {
      background-color: <?php echo get_option( '_nvv_login_background_color_button_form_hover' ) ?>;
      color: <?php echo get_option( '_nvv_login_color_button_form_hover' ) ?>;
    }

    <?php if( get_option( '_login_logo' ) ): ?>
      .login h1 a {
        background-image: url( <?php echo get_option( '_login_logo' ) ?> );
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        width: 100px;
        height: 100px;
       }
    <?php endif; ?>

    <?php if( get_option( '_nvv_login_styles' ) != '' ): ?>
      <?php echo get_option( '_nvv_login_styles' ) ?>
    <?php endif; ?>

  </style>
  <?php
}

/**
 * Add login title
 */
add_filter( 'login_title', 'nvv_login_title', 10, 2 );
function nvv_login_title( $login_title, $title ){
  
  $login_title =  $title . ' – ' . get_bloginfo( 'name' );

  return $login_title;
}

/**
 * Redirect after login
 */
add_filter('login_redirect', 'nvv_login_redirect', 10, 3 );
function nvv_login_redirect( $url, $request, $user ){

  if( get_option( '_redirect_after_login' ) ) {
    if( $request == '' && isset( $_SERVER['HTTP_REFERER'] ) ) $request = esc_url( $_SERVER['HTTP_REFERER'] );
    $url = $request;
    $uri = explode( "?", $url );

    if( $uri[0] == wp_login_url() ) {
      $url = nvv_login_control_home_url(); 
    };
  }
  return $url;
}

/**
 * Redirect after logout
 */
add_filter('logout_redirect', 'nvv_logout_redirect', 10, 3 );
function nvv_logout_redirect( $url, $request, $user ){

  if( get_option( '_redirect_after_logout' ) ) {
    $url = esc_url( $_SERVER['HTTP_REFERER'] );
  }

 return $url;
}

/**
 * Redirect profile
 */
add_filter( 'edit_profile_url', 'nvv_profile_redirect', 10, 3 );
function nvv_profile_redirect( $url, $user_id, $scheme ){

    $profile_page = get_option( '_nvv_profile_page' );

    if( $profile_page == 0 ) return $url;

    $custom_url = nvv_login_control_home_url( $profile_page );
    
    if( !is_admin() ) {
      return $custom_url;
    }
    else return $url;
}