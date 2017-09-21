<?php
  /**
   * Visual Composer element for a simple PAY NOW button
   */
  if ( ! defined( 'ABSPATH' ) ) { exit; }
  
  class PTR_VC_Simple_Form {
    
    function __construct() {
      add_action( 'init', array( $this, 'ptr_simple_form_mapping' ) );
    }
    
    public function ptr_simple_form_mapping() {
      if ( !defined( 'WPB_VC_VERSION' ) ) {
        return;
      }
      
      vc_map(
        array(
          'name' => __('Paytron Payment Form', 'ptr-payments'),
          'base' => 'ptr-payments-form',
          'description' => __('A payment form that allows you receive money powered by Paytron', 'ptr-payments'),
          'category' => __('Paytron Payments', 'ptr-payments'),
          'icon' => PTR_DIR_URL . 'assets/images/paytron-icon.png',
          'params' => array(
            array(
              'type' => 'textfield',
              'class' => 'title-class',
              'holder' => 'p',
              'heading' => __( 'Amount', 'ptr-payments' ),
              'param_name' => 'amount',
              'value' => __( '', 'ptr-payments' ),
              'description' => __( 'If left blank, user will be asked to enter the amount to complete the payment.', 'ptr-payments' ),
              'admin_label' => false,
              'weight' => 0,
              'group' => 'Form Attributes',
            ),

            array(
              'type' => 'checkbox',
              'heading' => __( "Use logged-in user's email?", 'ptr-payments' ),
              'description' => __( "Check this if you want the logged-in user's email to be used. If unchecked or user is not logged in, they will be asked to fill in their email address to complete payment.", 'ptr-payments' ),
              'param_name' => 'use_current_user_email',
              'std' => '',
              'value' => array(
                __( 'Yes', 'ptr-payments' ) => 'yes'
              ),
              'group' => 'Form Attributes'
            ),
            array(
              'type' => 'exploded_textarea',
              'heading' => __( "Payment Types", 'ptr-payments' ),
              'param_name' => "payment_types",
              'value' => '', 
              'description' => __( "Separate different available payment types, leave blank if not required.", "ptr-payments" ),
              'group' => 'Form Attributes'
            ),
            array(
              'type'        => 'dropdown',
              'heading'     => __('Charge Currency'),
              'param_name'  => 'charge_currency',
              'admin_label' => true,
              'value'       => array(
                'NGN'   => 'NGN (Nigerian Naira)',
                'USD'   => 'USD (American Dollars)'
              ),
              'std'         => 'NGN', // Your default value
              'description' => __('The currency which your customers would be charged by.')
            ),
      
            array(
              'type' => 'textfield',
              'heading' => __( 'Button Text', 'ptr-payments' ),
              'param_name' => 'content',
              'value' => __( '', 'ptr-payments' ),
              'description' => __( '(Optional) The text on the PAY NOW button. Default: "PAY NOW"', 'ptr-payments' ),
              'admin_label' => false,
              'weight' => 0,
              'group' => 'Form Attributes',
            ),

          )
        )
      );

    }

  }
  
  new PTR_VC_Simple_Form();
?>
