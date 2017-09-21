<?php
  /**
   * Shortcode Class
   */

  if ( ! defined( 'ABSPATH' ) ) {
    exit;
  }

  if ( ! class_exists( 'PTR_Paytron_Shortcode' ) ) {

    class PTR_Paytron_Shortcode {
      protected static $instance = null;

      function __construct() {
        $this->base_url = 'https://paytron.com.ng';
        add_shortcode( 'ptr-payments-form', array( $this, 'paytron_form_shortcode' ) );
      }
      
      public function paytron_form_shortcode( $attr, $content="" ) {
        global $admin_settings;
        $btn_text = empty( $content ) ? $this->pay_button_text() : $content;
        $email = $this->use_current_user_email( $attr ) ? wp_get_current_user()->user_email : '';
        $atts = shortcode_atts( array(
          'amount'    => '',
          'payment_types' => '',
          'email'     => $email,
          'currency'     => $admin_settings->get_option_value( 'currency' ),
        ), $attr );
        $this->load_js_files();
        ob_start();
        $this->render_payment_form( $atts, $btn_text );
        $form = ob_get_contents();
        ob_end_clean();
        return $form;
      }

      public function render_payment_form( $atts, $btn_text ) {
        $data_attr = '';
        foreach ($atts as $att_key => $att_value) {
          $data_attr .= ' data-' . $att_key . '="' . $att_value . '"';
        }
        $atts['form_id'] = $this->gen_rand_string();
        include( PTR_DIR_PATH . 'views/pay-now-form.php' );
      }
      
      private function gen_rand_string( $len = 4 ) {
        if ( version_compare( PHP_VERSION, '5.3.0' ) <= 0 ) {
            return substr( md5( rand() ), 0, $len );
        }
        return bin2hex( openssl_random_pseudo_bytes( $len/2 ) );
      }
      
      public function load_js_files() {
        global $admin_settings;
        $payment_args = array(
          'cb_url'    => admin_url( 'admin-ajax.php' ),
          'currency'  => $admin_settings->get_option_value( 'currency' ),
          'description'      => $admin_settings->get_option_value( 'description' ),
          'company'      => $admin_settings->get_option_value( 'company' ),
          'companyLogo'      => wp_get_attachment_url($admin_settings->get_option_value( 'logo' )),
          'accountNumber'     => $admin_settings->get_option_value( 'account_number' ),
          'bankCode'     => $admin_settings->get_option_value( 'bank' )
        );
        wp_enqueue_script( 'paytron_inline_js', $this->base_url . '/assets/js/iframe.paytron.js', array(), '1.0.0', true );
        wp_enqueue_script( 'paytron_js', PTR_DIR_URL.'assets/js/paytron.js', array( 'jquery', 'paytron_inline_js' ), '1.0.0', true );
        wp_localize_script( 'paytron_js', 'ptr_payment_args', $payment_args );
      }
      
      private function pay_button_text() {
        global $admin_settings;
        $text = $admin_settings->get_option_value( 'btn_text' );
        if ( empty( $text ) ) {
          $text = 'PAY NOW';
        }
        return $text;
      }
      
      private function use_current_user_email( $attr ) {
        return isset( $attr['use_current_user_email'] ) && $attr['use_current_user_email'] === 'yes';
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
