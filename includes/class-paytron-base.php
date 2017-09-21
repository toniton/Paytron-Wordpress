<?php

class Paytron_Base_Class {
	static $instance;
	
	public function __construct() {
		add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_action( 'init', array( $this, 'plugin_init' ) );
    add_action( 'wp_ajax_process_paytron_payment', array( $this, 'process_paytron_payment' ) );
    add_action( 'wp_ajax_nopriv_process_paytron_payment', array( $this, 'process_paytron_payment' ) );
    register_setting( 'paytron-settings-group', 'paytron_options' );
    if ( false == get_option( 'paytron_options' ) ) {
      update_option( 'paytron_options', array() );
    }
	}
	
  public static function set_screen( $status, $option, $value ) {
  	return $value;
  }

  public function plugin_init() {
      include_once( PTR_DIR_PATH . 'includes/class-paytron-admin-settings.php' );
      global $admin_settings;
      $admin_settings = Paytron_Admin_Settings::get_instance();
      include_once( PTR_DIR_PATH . 'includes/class-paytron-shortcode.php' );
      new PTR_Paytron_Shortcode;
  }

  public function plugin_menu() {
      add_menu_page(
        __( 'Paytron Settings Page', 'ptr-payments' ),
        'Paytron',
        'manage_options',
        'ptr-payments-form',
        array( $this, 'admin_configuration_page' ),
        PTR_DIR_URL . 'assets/images/paytron-icon.png',
        50
      );
      
      add_submenu_page(
        'ptr-payments-form',
        __( 'Settings', 'ptr-payments' ),
        __( 'Settings', 'ptr-payments' ),
        'manage_options',
        'ptr-payments-form',
        array( $this, 'admin_configuration_page' )
      );
      
      $hook = add_submenu_page(
        'ptr-payments-form',
        __( 'Transactions', 'ptr-payments' ),
        __( 'Transactions', 'ptr-payments' ),
        'manage_options',
        'ptr-transactions-list',
        array( $this, 'plugin_transactions_page' )
      );
  	add_action( "load-$hook", array( $this, 'screen_option' ) );
  }
  
  public function admin_configuration_page() {
      include_once( PTR_DIR_PATH . 'views/admin-settings-page.php' );
  }
  
  public function plugin_transactions_page() {
      include_once( PTR_DIR_PATH . 'includes/class-paytron-transaction-list.php' );
      global $payment_list;
      $payment_list = PTR_Payment_List::get_instance();
      include_once( PTR_DIR_PATH . 'views/transaction-list-table.php' );
  }
  
  public function process_paytron_payment() {
    global $admin_settings;
    check_ajax_referer( 'ptr-paytron-nonce', 'ptr_sec_code' );
    $txn_ref = $_POST['txnRef'];
    $ptr_ref = $_POST['ptrRef'];
    $email = $_POST['email'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    if ( $ptr_ref && $txn_ref ) {
      $args   =  array(
        'post_type'   => 'paytron_payment_list',
        'post_status' => 'publish',
        'post_title'  => $txn_ref,
      );
      $payment_record_id = wp_insert_post( $args, true );
      if ( ! is_wp_error( $payment_record_id )) {
        $post_meta = array(
          '_ptr-paytron_payment_amount'   => $amount,
          '_ptr-paytron_payment_customer' => $email,
          '_ptr-paytron_payment_status'   => $status,
          '_ptr-paytron_payment_tx_ref'   => $txn_ref,
        );
        $this->_add_post_meta( $payment_record_id, $post_meta );
      }
      $redirect_url_key = $status === 'successful' ? 'success_redirect_url' : 'failed_redirect_url';
      wp_send_json_success(json_encode( array( 'status' => $status, 'redirect_url' => $admin_settings->get_option_value( $redirect_url_key ) ) ));
    }
    wp_send_json_error();
  }
      
  private function _add_post_meta( $post_id, $data ) {
    foreach ($data as $meta_key => $meta_value) {
      update_post_meta( $post_id, $meta_key, $meta_value );
    }
  }

  public function screen_option() {
  	$option = 'per_page';
  	$args   = array(
  		'label'   => 'Payments',
  		'default' => 5,
  		'option'  => 'payments_per_page'
  	);
  	add_screen_option( $option, $args );
  }
    
  public static function get_instance() {
  	if ( ! isset( self::$instance ) ) {
  		self::$instance = new self();
  	}
  	return self::$instance;
  }

}
?>