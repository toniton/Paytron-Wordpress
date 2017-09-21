<?php
  /*
    Plugin Name: Paytron Payment Forms
    Plugin URI: https://paytron.com.ng.com/
    Description: Paytron payment gateway forms for wordpress.
    Version: 1.0.0
    Author: Akinjiola Toni
    Author URI: https://github.com/toniton
    Copyright: © 2017 Paytron.
    License: MIT License
  */

  if ( ! defined( 'ABSPATH' ) ) {
    exit;
  }

  if ( ! defined( 'PTR_PAY_PLUGIN_FILE' ) ) {
    define( 'PTR_PAY_PLUGIN_FILE', __FILE__ );
  }

  if ( ! defined( 'PTR_DIR_PATH' ) ) {
    define( 'PTR_DIR_PATH', plugin_dir_path( __FILE__ ) );
  }
  
  if ( ! defined( 'PTR_DIR_URL' ) ) {
    define( 'PTR_DIR_URL', plugin_dir_url( __FILE__ ) );
  }

  require_once( PTR_DIR_PATH . 'includes/class-paytron-base.php' );
  
  add_action( 'plugins_loaded', function () {
  	Paytron_Base_Class::get_instance();
  });

?>
