<?php

if (!defined('ABSPATH')) {
  exit;
}

$trusted_users_id = carbon_get_theme_option( 'nvv_trusted_users' );

if( !in_array( get_current_user_id(), $trusted_users_id ) ){
  return 0;
}

add_action( 'admin_bar_menu', 'nvv_user_check_admin_bar_menu', 110 );
function nvv_user_check_admin_bar_menu( $wp_admin_bar ){

  $wp_admin_bar->add_menu( array(
    'id'    => 'check_user_menu_id',
    'title' => esc_html__( 'Login from user', 'nvv-login-control' ),
  ) );

  $wp_admin_bar->add_menu( array(
    'parent' => 'check_user_menu_id',
    'id'     => 'check_user_menu_select_id',
    'title'  => nvv_user_check_select(),
  ) );
}

function nvv_user_check_select() {

  $users = get_users();

  ob_start();
  ?>
  <form action="" method="post">
    <select name="user_check_select" id="user_check_select" onchange='this.form.submit()' style="padding: 0 30px 0 10px;">
      <option value=""><?php esc_html_e( 'Select user', 'nvv-login-control' ) ?></option>
      <?php foreach ( $users as $key => $value ): ?>
        <option value="<?php echo esc_attr( $value->ID ) ?>"><?php echo esc_attr( $value->display_name ) ?> (<?php echo esc_attr( $value->roles[0] ) . ' / ID=' . $value->ID ?>)</option>
      <?php endforeach; ?>
    </select>
  </form>
  <?php

 $select_out = ob_get_contents();
 ob_end_clean();

 return $select_out;
  
}

/**
* User test-check
*/
if( isset( $_POST['user_check_select'] ) ) {
  
  $ID = sanitize_key( ( $_POST['user_check_select'] ) );

  nocache_headers();
  wp_clear_auth_cookie();
  wp_set_auth_cookie( $ID );

  $url = home_url();

  wp_safe_redirect( $url );
  exit;
}