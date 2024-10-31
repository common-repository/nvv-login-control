<?php 

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'nvv_login_control_options' );
function nvv_login_control_options() {

  Container::make( 'theme_options', 'nvv_login_control_options', esc_html__( 'NVV Login Control options', 'nvv-login-control' ) )
    ->set_page_menu_position( 100 )
    ->set_icon('dashicons-admin-settings')
    ->set_page_file( 'nvv_login_control_options' )
    ->set_page_parent( 'options-general.php' )

    ->add_tab( esc_html__( 'Authorization page', 'nvv-login-control' ), array(  
      Field::make( 'checkbox', 'nvv_enable_custom_authorization_page', esc_html__( 'Enable custom authorization page', 'nvv-login-control' ) ),
      Field::make( 'image', 'login_logo', esc_html__( 'Logo', 'nvv-login-control' ) )->set_value_type( 'url' ),
      Field::make( 'checkbox', 'redirect_after_login', esc_html__( 'Go to previous page after login.', 'nvv-login-control' ) ),
      Field::make( 'checkbox', 'redirect_after_logout', __( 'Don\'t leave current page after logout.', 'nvv-login-control' ) ),   
      Field::make( 'select', 'nvv_profile_page', esc_html__( 'Profile Page', 'nvv-login-control' ) )
        ->add_options( 'nvv_login_control_profile_page_list' ) // Call function "nvv_login_control_profile_page_list()"
        ->set_help_text( esc_html__( 'Select an alternate user profile page.', 'nvv-login-control' ) ),
      
      Field::make( 'color', 'nvv_login_color_font_page', esc_html__( 'Page font color', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(30),
      Field::make( 'color', 'nvv_login_background_color_page', esc_html__( 'Page background color', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(30),
      Field::make( 'image', 'nvv_login_background_image_page', esc_html__( 'Page background image', 'nvv-login-control' ) )
        ->set_value_type( 'url' )->set_width(30),

      Field::make( 'color', 'nvv_login_color_font_form', esc_html__( 'Form font color', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(30),
      Field::make( 'color', 'nvv_login_background_color_form', esc_html__( 'Form background color', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(30), 
      Field::make( 'image', 'nvv_login_background_image_form', esc_html__( 'Form background image', 'nvv-login-control' ) )
        ->set_value_type( 'url' )->set_width(30),
      
      Field::make( 'color', 'nvv_login_background_color_button_form', esc_html__( 'Color submit button', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(25),
      Field::make( 'color', 'nvv_login_color_button_form', esc_html__( 'Color submit button font', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(25),  
      Field::make( 'color', 'nvv_login_background_color_button_form_hover', esc_html__( 'Color submit button hover', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(25),
      Field::make( 'color', 'nvv_login_color_button_form_hover', esc_html__( 'Color submit button font hover', 'nvv-login-control' ) )
        ->set_alpha_enabled( true )->set_width(25), 

      Field::make( 'textarea', 'nvv_login_styles', esc_html__( 'Authorization page CSS styles', 'nvv-login-control' ) )
        ->set_rows( 10 )
        ->set_help_text( esc_html__( 'These styles CSS will be applied on the authorization page.', 'nvv-login-control' ) . ' ' . esc_html__( 'For example', 'nvv-login-control' ) . ': #loginform { border-radius: 20px; }', 'nvv-login-control' ),
        
    ) )

    ->add_tab( esc_html__( 'Login as any user', 'nvv-login-control' ), array(
    Field::make( 'checkbox', 'enable_login_as_user', esc_html__( 'Enable Login As User', 'nvv-login-control' ) ),
    Field::make( 'multiselect', 'nvv_trusted_users', esc_html__( 'Trusted Users', 'nvv-login-control' ) )
      ->add_options( 'nvv_login_as_user' )
      ->set_help_text( esc_html__( 'Only selected users will be allowed to login on behalf of other users.', 'nvv-login-control' ) )
      )
    )

    ->add_tab( esc_html__( 'Recaptcha V3', 'nvv-login-control' ), array(
      Field::make( 'checkbox', 'recaptcha_enable', esc_html__( 'Enable Recaptcha', 'nvv-login-control' ) ),
      Field::make( 'checkbox', 'recaptcha_login_form', esc_html__( 'Enable Recaptcha on Authorization Page', 'nvv-login-control' ) )
        ->set_default_value( 'yes' ),
      Field::make( 'checkbox', 'hide_recaptcha_logo', esc_html__( 'Hide Recaptcha Logo', 'nvv-login-control' ) ),
      Field::make( 'text', 'recaptcha_site_key', esc_html__( 'Recaptcha Site Key', 'nvv-login-control' ) ),
      Field::make( 'text', 'recaptcha_secret_key', esc_html__( 'Recaptcha Secret Key', 'nvv-login-control' ) ), 
    ) )

    ->add_tab( esc_html__( 'Technical service', 'nvv-login-control' ), nvv_technical_service_fileds() )

    ->add_tab( esc_html__( 'SEO', 'nvv-login-control' ), nvv_meta_api_filds() )

    ->add_tab( esc_html__( 'Other', 'nvv-login-control' ), array(
      Field::make( 'checkbox', 'nvv_hide_toolbar', esc_html__( 'Hide toolbar from users', 'nvv-login-control' ) )
      ->set_default_value( 'yes' ),
    ) )
   
   ;
}

function nvv_login_control_profile_page_list(){
  $pages = get_pages( [
    'sort_order'   => 'ASC',
    'sort_column'  => 'post_title',
    'hierarchical' => 1,
    'exclude'      => '',
    'include'      => '',
    'meta_key'     => '',
    'meta_value'   => '',
    'authors'      => '',
    'child_of'     => 0,
    'parent'       => -1,
    'exclude_tree' => '',
    'number'       => '',
    'offset'       => 0,
    'post_type'    => 'page',
    'post_status'  => 'publish',
  ] );

  $pages_list = get_pages( $pages );

  $profile_page_list[0] = esc_html__( 'Select from pages...', 'nvv-login-control' );

  foreach ( $pages_list as $value ) {
    $profile_page_list[$value->post_name] = $value->post_title;
  }

  return $profile_page_list;
}

function nvv_meta_api_filds(){
  $kyeword_filds = array();
  $fields = array(
    Field::make( 'checkbox', 'public_ga', esc_html__( 'Public Google Analytics', 'nvv-login-control' ) ),
    Field::make( 'textarea', 'google_analytics', esc_html__( 'Google Analytics', 'nvv-login-control' ) )
      ->set_rows( 9 ),
    Field::make( 'checkbox', 'public_keywords', esc_html__( 'Public Keywords', 'nvv-login-control' ) ),
    );

    foreach ( nvv_pll_login_control_languages_list() as $key => $value ) {
      $kyeword_filds[] = Field::make( 'text', 'meta_keywords_' . $value, esc_html__( 'Meta Keywords ' . $value, 'nvv-login-control' ) );
    };

  $fields = array_merge( $fields , $kyeword_filds );
  
  return $fields;
}

function nvv_technical_service_fileds(){
  $kyeword_filds = array();
  $fields = array(
      Field::make( 'checkbox', 'technical_service', esc_html__( 'Close the site for maintenance for users', 'nvv-login-control' ) ), 
      Field::make( 'checkbox', 'technical_service_by_ip', esc_html__( 'Access for allowed addresses only', 'nvv-login-control' ) ),
      Field::make( 'html', 'nvv_ukrstore_core_your_ip' )
        ->set_html( "<span>".esc_html__( 'Your IP', 'nvv-login-control' ).": ". getenv('REMOTE_ADDR' ) ."</span>" ),
      Field::make( 'textarea', 'technical_service_ip', esc_html__( 'Allowed IP-addresses (white list)', 'nvv-login-control' ) )
        ->set_help_text( esc_html__( 'List of IP-addresses for accessing a closed site. IP-addresses need to be written through ";"', 'nvv-login-control' ) ),
    );

  foreach ( nvv_pll_login_control_languages_list() as $key => $value ) {
      $kyeword_filds[] = Field::make( 'rich_text', 'technical_service_text_' . $value, esc_html__( 'Text on Maintenance Page ' . $value, 'nvv-login-control' ) );
    };

  $fields = array_merge( $fields , $kyeword_filds );

  return $fields;
}

function nvv_login_as_user(){

  $users = get_users();
  $trusted_users = [];

  foreach ( $users as $key => $value ) {
    $trusted_users[$value->ID] = $value->display_name . ' (email: ' . $value->user_email . ' | ID-' . $value->ID . ')';
  }

   return $trusted_users;
}