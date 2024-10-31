<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
* Local variables
**/
global $nvv_site_key;
global $nvv_secret_key;
global $recaptcha_google_politics;

$recaptcha_enable = null !== get_option( '_recaptcha_enable' ) ? get_option( '_recaptcha_enable' ) : false;
$recaptcha_login_form = null !== get_option( '_recaptcha_login_form' ) ? get_option( '_recaptcha_login_form' ) : false;
$hide_recaptcha_logo = null !== get_option( '_hide_recaptcha_logo' ) ? get_option( '_hide_recaptcha_logo' ) : false;
$nvv_site_key = null !== get_option( '_recaptcha_site_key' ) ? get_option( '_recaptcha_site_key' ) : '';
$nvv_secret_key = null !== get_option( '_recaptcha_secret_key' ) ? get_option( '_recaptcha_secret_key' ) : '';

$recaptcha_google_politics = '';

if( !$recaptcha_enable ){
  return;
}

//Disable recaptcha module
if( !$recaptcha_enable || is_user_logged_in() ) return;

//Hide recaptcha logo
if( $hide_recaptcha_logo ){

    add_action( 'wp_head', 'nvv_hide_recaptcha_logo' );
    function nvv_hide_recaptcha_logo(){
        global $recaptcha_google_politics;

        $recaptcha_google_politics = "<div style='font-size: 12px;'>" . sprintf( esc_html__( "This site is protected by reCAPTCHA and the Google %s and %s apply.", 'nvv_tmplgen_core' ), "<a href='https://policies.google.com/privacy'>Privacy Policy</a>", "<a href='https://policies.google.com/terms'>Terms of Service</a>" ) . "</div>";
        ?>
            <style type="text/css">
                .grecaptcha-badge{
                    visibility: hidden;
                }
            </style>
                
        <?php
    }
    
}

function nvv_recaptcha_field(){

    global $recaptcha_google_politics;
    
    return '<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" >' . $recaptcha_google_politics . '<br>';

}

//Add hidden filed <input> to all comment forms
add_filter( 'comment_form_fields', 'nvv_add_filed_to_comment_form' );
function nvv_add_filed_to_comment_form( $comment_fields ){

    $comment_fields[ 'recaptcha' ] = nvv_recaptcha_field();

    return $comment_fields;
}

/**
* Recaptcha on login form
**/
if( $recaptcha_login_form ){
    
    add_action( 'login_head', 'nvv_login_head_add_script' );
    function nvv_login_head_add_script() {
    global $nvv_site_key;

        wp_footer(); 
        
        wp_enqueue_script( 'recaptcha_script', 'https://www.google.com/recaptcha/api.js?render=' . $nvv_site_key, array(), null );

        ?>
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo $nvv_site_key;?>', { action: 'homepage' }).then( function( token ) {
                    document.getElementById( 'g-recaptcha-response' ).value = token;
                });
            });
        </script>
        <?php
    }

    add_action( 'login_form', 'nvv_add_field_to_login_form' );
    function nvv_add_field_to_login_form(){
        echo nvv_recaptcha_field();
    }

    add_action( 'register_form', 'nvv_add_field_to_register_form' );
    function nvv_add_field_to_register_form(){
        echo nvv_recaptcha_field();
    }
}
/**
* /Recaptcha on login form
**/

//Add recaptcha script 
add_action( 'wp_footer', 'nvv_recaptcha_script' );
function nvv_recaptcha_script(){
    global $nvv_site_key;

    wp_enqueue_script( 'recaptcha_script', 'https://www.google.com/recaptcha/api.js?render=' . $nvv_site_key, array(), null );

    ?>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo $nvv_site_key;?>', { action: 'homepage' }).then( function( token ) {
                document.getElementById( 'g-recaptcha-response' ).value = token;
            });
        });
    </script>
    <?php
}

/*PROCESSING REQUEST*/
if( isset( $_POST[ 'g-recaptcha-response' ] ) ){
    /*WE CREATE A FUNCTION WHICH MAKES A REQUEST FOR GOOGLE SERVICE*/
    function nvv_getCaptcha( $token ) {
        global $nvv_secret_key;

        $Response = wp_remote_retrieve_body( wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=".$nvv_secret_key."&response={$token}" ) );
        $Return = json_decode( $Response );

        return $Return;
    }
    
    /*MAKE A REQUEST FOR GOOGLE SERVICE AND WRITE A RESPONSE*/
    $Return = nvv_getCaptcha( $_POST['g-recaptcha-response'] );
    
    /*IF THE REQUEST IS SUCCESSFULLY SENT AND VALUE score MORE THAN 0.5 PERFORM THE CODE*/
    if( $Return->success != '' && $Return->success == false && $Return->score < 0.5 ){
        wp_die( 'No entry for robots!' );
    }
}
