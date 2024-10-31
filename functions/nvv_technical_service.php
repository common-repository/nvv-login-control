<?php 

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action( 'init', function(){
  global $technical_service, $technical_service_text;
  $technical_service = get_option( '_technical_service' );
  $technical_service_text = get_option( '_technical_service_text_' . nvv_pll_login_control_current_language() );

  if( !$technical_service ){
  return;
}
} );


add_action( 'admin_bar_menu', 'nvv_admin_bar_menu', 100 );
function nvv_admin_bar_menu( $wp_admin_bar ) {

  global $technical_service, $technical_service_text;
  
   if( $technical_service && current_user_can( 'edit_posts' ) ){
    
    $wp_admin_bar->add_menu( array(
      'id'    => 'technical_service',
      'title' => esc_html__( 'Site closed for users', 'nvv-login-control' ),
      'href'  => admin_url( 'options-general.php?page=nvv_login_control_options' ),
    ) );
  }

}

// on backend area
add_action( 'admin_head', 'nvv_override_admin_bar_css' );
// on frontend area
add_action( 'wp_head', 'nvv_override_admin_bar_css' );
function nvv_override_admin_bar_css() { 

   if ( is_admin_bar_showing() ) { ?>

      <style type="text/css">
         #wp-admin-bar-technical_service a {
          background-color: red;
          color: white;
          opacity: 1;
        }
      </style>

   <?php }

}

add_action( 'init', 'nvv_technical_service' );
function nvv_technical_service(){
  
  global $technical_service, $technical_service_text;
  $allowed_ip = explode(";", get_option('_technical_service_ip'));

  foreach ($allowed_ip as $key => $value) {
    $allowed_ip[$key] = trim($value);
  }

  $client_ip = getenv("REMOTE_ADDR");

  if( $technical_service ){

    if( get_option( '_technical_service_by_ip' ) && in_array( $client_ip, $allowed_ip ) ) return;

    if( current_user_can( 'edit_posts' ) ) return;

    if( !is_admin() && !stristr( $_SERVER['REQUEST_URI'], '/wp-login.php' ) ){
     
      ?>
      
      <html <?php language_attributes(); ?>>
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta name="robots" content="noindex">
          <meta name="googlebot" content="noindex">

          <style>
            
            body {
              margin: 0;
              padding: 0;
            }
            
            .techical_service_block {
              width: 100%;
              height: 100vh;
              display: flex;
              flex-direction: column;
              justify-content: center;
              //align-items: center;
            }

            .techical_service_block img {
              //margin-bottom: 15px;
            }

            .techical_service_block h1,
            .techical_service_block p {
              margin: 0;
            }

            .techical_service_block {
              font-family: Roboto, sans-serif;
              text-align: center;
            }
          
          </style>
        </head>
      
        <div class="techical_service_block">
          <?php echo wpautop( $technical_service_text ) ?>
        </div>
        
      </html>
      
      <?php

     exit;

    }

  }

}