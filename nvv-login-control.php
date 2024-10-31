<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://nvvdesign.top/
 * @since             1.0.0
 * @package           NVV_Login_Control
 *
 * @wordpress-plugin
 * Plugin Name:       NVV Login Control
 * Plugin URI:        https://nvvdesign.top/nvv-login-control/
 * Description:       Customizable authorization page. Authorization on the site on behalf of any user. Recaptcha V3. Site closure for maintenance. SEO.
 * Version:           1.0.0
 * Author:            NVV Design
 * Author URI:        https://nvvdesign.top/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nvv-login-control
 * Domain Path:       /languages
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

if ( ! defined( 'NVV_LOGIN_CONTROL_VERSION' ) ){
  // Replace the version number of the theme on each release.
  define( 'NVV_LOGIN_CONTROL_VERSION', '1.0.0' );
}

add_action( 'plugins_loaded', function(){
  load_plugin_textdomain( 'nvv-login-control', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );  
  
  require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
  require_once plugin_dir_path( __FILE__ ) . 'options/carbonfields/includes-carbon-custom-files.php';

  if( get_option( '_technical_service' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'functions/nvv_technical_service.php';
  }

  if( get_option( '_nvv_enable_custom_authorization_page' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'functions/nvv_login_functions.php';
  }
  
  if( get_option( '_recaptcha_enable' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'functions/nvv_recaptcha.php'; 
  }

} );  

add_action( 'init', function(){
  require_once plugin_dir_path( __FILE__ ) . 'functions.php';
  
  if( get_option( '_enable_login_as_user' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'functions/nvv_login_as_user.php';
  }
} );

add_action( 'wp_head', function(){
  if( get_option( '_public_ga' ) ){
    echo get_option( '_google_analytics' );
  }

  if( get_option( '_public_keywords' ) ){
    ?>
      <meta name="keywords" content="<?php echo get_option( '_meta_keywords_' . nvv_pll_login_control_current_language() ) ?>">
    <?php
  }
    
} );

/* Activate, Deactivate */
function activate_nvv_login_control(){
 
}

function deactivate_nvv_login_control(){

}

register_activation_hook( __FILE__, 'activate_nvv_login_control' );
register_deactivation_hook( __FILE__, 'deactivate_nvv_login_control' );
/* /Activate, Deactivate */

/* Polylang compatibility functions */
function nvv_pll_login_control_current_language(){
  if( function_exists( 'pll_current_language' ) ){
    return pll_current_language();
  }
  else{
    return '';
  }
}

function nvv_pll_login_control_languages_list( $args = null ){
  if( function_exists( 'pll_languages_list' ) ){
    return pll_languages_list( $args );
  }
  else{
    return array('');
  }
}

function nvv_login_control_home_url( $slug = null ){

  if( function_exists( 'pll_home_url' ) ){
    if( isset( $slug ) ){
      $id_p = get_page_by_path( $slug )->ID;
      $url = pll_home_url() . get_page_uri( pll_get_post( $id_p ) );
    }
    else $url = pll_home_url();
  }
  else{
    $url = home_url( $slug );
  }
  
  return $url;
}
/* /Polylang compatibility functions */

//Custom action links
add_filter( 'plugin_action_links', 'nvv_plugin_action_links', 10, 2 );
function nvv_plugin_action_links( $actions, $plugin_file ){
  if( false === strpos( $plugin_file, basename(__FILE__) ) )
    return $actions;

  $settings_link = '<a href="options-general.php?page=nvv_login_control_options' .'">'.esc_html__( 'Settings', 'nvv-login-control' ).'</a>'; 
  $clear_options_link = '<a href="plugins.php?clear_all_carbonfields_options=1' .'" title="'.esc_html__( 'Clearing all user options from the database', 'nvv-login-control' ).'" onclick="return confirm(\''. esc_html__( 'Warning! All user options will be removed forever!', 'nvv-login-control' ) .'\')">'.esc_html__( 'Clear options', 'nvv-login-control' ).'</a>'; 
  
  array_unshift( $actions, $settings_link, $clear_options_link ); 
  
  return $actions; 
}
