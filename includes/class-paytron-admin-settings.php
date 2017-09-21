<?php
  /**
   * Paytron Admin Settings Page Class
   */

  if ( ! defined( 'ABSPATH' ) ) {
    exit;
  }
  if ( ! class_exists( 'Paytron_Admin_Settings' ) ) {
    
    class Paytron_Admin_Settings {
      public static $instance = null;
      protected $options;
      
      private function __construct() {
        add_action( 'admin_init', array( $this, 'paytron_register_settings' ) );
        $this->init_settings();
      }
      
      public function paytron_register_settings() {
        register_setting( 'paytron-settings-group', 'paytron_options' );
      }

      private function init_settings() {
        if ( false == get_option( 'paytron_options' ) ) {
          update_option( 'paytron_options', array() );
        }
      }
      
      public function get_option_value( $attr ) {
        $options = get_option( 'paytron_options' );
        if ( array_key_exists($attr, $options) ) {
          return $options[$attr];
        }
        return '';
      }
      
      public static function get_instance() {
        if ( null == self::$instance ) {
          self::$instance = new self;
        }
        return self::$instance;
      }
    }

  }

?>
