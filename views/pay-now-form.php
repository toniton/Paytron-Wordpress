<?php
  if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div>
  <form id="<?php echo $atts['form_id'] ?>" class="paytron-payments-form" <?php echo $data_attr; ?> >
    <div id="notice"></div>

    <?php if ( empty( $atts['firstname'] ) ) : ?>

      <label for="ptr-firstname"><?php _e( 'Firstname', 'ptr-payments' ); ?></label>
      <input id="ptr-firstname" type="text" placeholder="<?php _e( 'Firstname', 'ptr-payments' ); ?>" required /><br>

    <?php endif; ?>

    <?php if ( empty( $atts['lastname'] ) ) : ?>

      <label for="ptr-lastname"><?php _e( 'Lastname', 'ptr-payments' ); ?></label>
      <input id="ptr-lastname" type="text" placeholder="<?php _e( 'Lastname', 'ptr-payments' ); ?>" required /><br>

    <?php endif; ?>
    <?php if ( empty( $atts['email'] ) ) : ?>

      <label for="ptr-email"><?php _e( 'Email', 'ptr-payments' ) ?></label>
      <input id="ptr-email" type="email" placeholder="<?php _e( 'Email', 'ptr-payments' ) ?>" required /><br>

    <?php endif; ?>
    <?php if ( !empty( $atts['payment_types'] ) ) : ?>

      <label for="ptr-payment-type"><?php _e( 'Payment Type', 'ptr-payments' ) ?></label>
      <select id="ptr-payment-type">
        <option value=""></option>;
        <?php 
          $payment_types = explode( ',' , $atts['payment_types'] );
          foreach ($payment_types as $payment_type) {
        ?>
            <option value="<?php echo trim($payment_type); ?>"><?php echo $payment_type; ?></option>;
        <?php 
          }
        ?>
      </select>
    <?php endif; ?>

    <?php if ( empty( $atts['amount'] ) ) : ?>

      <label for="ptr-amount"><?php _e( 'Amount', 'ptr-payments' ); ?> (<?php echo  $atts['currency']; ?>)</label>
      <input id="ptr-amount" type="text" placeholder="<?php _e( 'Amount', 'ptr-payments' ); ?>" required /><br>

    <?php endif; ?>
    <?php wp_nonce_field( 'ptr-paytron-nonce', 'ptr_sec_code' ); ?>
    <button type="submit"><?php _e( $btn_text, 'ptr-payments' ) ?></button>
  </form>
</div>
