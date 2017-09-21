<?php

  if ( ! defined( 'ABSPATH' ) ) { exit; }

?>
<?php 
  global $admin_settings;
  $banks = [];
  $response = wp_remote_post( 'http://moneywave.herokuapp.com/banks', null);
  if(!is_wp_error($response)){
    $json_body = json_decode($response['body']);
    $banks = (array) $json_body->data;
  } 
?>

  <div class="wrap">
    <h1>Paytron Forms Settings</h1>
    <form id="paytron-payments-form" action="options.php" method="post" enctype="multipart/form-data" class="validate" novalidate="novalidate">
      <?php settings_fields( 'paytron-settings-group' ); ?>
      <?php do_settings_sections( 'paytron-settings-group' ); ?>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row">
              <label for="paytron_options[account_number]"><?php _e( 'Account Number', 'ptr-payments' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="paytron_options[account_number]" value="<?php echo esc_attr( $admin_settings->get_option_value( 'account_number' ) ); ?>" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
              <p class="description">Your Account Number</p>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <label for="paytron_options[bank]"><?php _e( 'Bank', 'ptr-payments' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <select class="regular-text code" name="paytron_options[bank]">
                <?php 
                  $currency = esc_attr( $admin_settings->get_option_value( 'bank' ) );
                  foreach ($banks as $bank_code => $bank_name) {
                ?>
                    <option value="<?php echo $bank_code; ?>" <?php selected( $currency, $bank_code ); ?>><?php echo $bank_name; ?></option>;
                <?php 
                  }
                ?>
              </select>
              <p class="description">Choose your bank</p>
            </td>
          </tr>
      		<tr valign="top">
      			<th scope="row" class="titledesc"><?php _e( 'Logo', 'ptr-payments' ); ?>:</th>
      			<td class="forminp">
      				<div class='image-preview-wrapper'>
          			<img id='image-preview' src='<?php echo wp_get_attachment_url( $admin_settings->get_option_value( 'logo' ) ); ?>' height='100'>
          		</div>
          		<input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
          		<input type='hidden' name='paytron_options[logo]' id='image_attachment_id' value='<?php echo $admin_settings->get_option_value( 'logo' ); ?>'>
          		<p class="description">Preferred image width = 512px and height = 512px</p>
          		<?php
    	          wp_enqueue_media();
            	?>
      				<script type="text/javascript">
      				  jQuery( document ).ready( function( $ ) {
            			var file_frame;
            			jQuery('#upload_image_button').on('click', function( event ){
            				event.preventDefault();
            				if ( file_frame ) {
            					file_frame.open();
            					return;
            				}
            				file_frame = wp.media.frames.file_frame = wp.media({
            					title: 'Select a image to upload',
            					button: {
            						text: 'Use this image',
            					},
            					multiple: false
            				});
            				file_frame.on( 'select', function() {
            					attachment = file_frame.state().get('selection').first().toJSON();
            					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
            					$( '#image_attachment_id' ).val( attachment.id );
            				});
            				file_frame.open();
            			});
      				  });
      				</script>
      			</td>
      		</tr>
          <tr valign="top">
            <th scope="row">
              <label for="paytron_options[company]"><?php _e( 'Company', 'ptr-payments' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="paytron_options[company]" value="<?php echo esc_attr( $admin_settings->get_option_value( 'company' ) ); ?>" />
              <p class="description">Required - The title shown to the customer on the payment modal</p>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <label for="paytron_options[description]"><?php _e( 'Description', 'ptr-payments' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="paytron_options[description]" value="<?php echo esc_attr( $admin_settings->get_option_value( 'description' ) ); ?>" />
              <p class="description">Required - The title shown to the customer on the payment modal</p>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">
              <label for="paytron_options[currency]"><?php _e( 'Charge Currency', 'ptr-payments' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <select class="regular-text code" name="paytron_options[currency]">
                <?php $currency = esc_attr( $admin_settings->get_option_value( 'currency' ) ); ?>
                <option value="NGN" <?php selected( $currency, 'NGN' ) ?>>NGN</option>
                <option value="USD" <?php selected( $currency, 'USD' ) ?>>USD</option>
              </select>
              <p class="description">(Optional) default: NGN</p>
            </td>
          </tr>
          
          <!-- Successful Redirect URL -->
          <tr valign="top">
            <th scope="row">
              <label for="paytron_options[success_redirect_url]"><?php _e( 'Success Redirect URL', 'rave-pay' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="paytron_options[success_redirect_url]" value="<?php echo esc_attr( $admin_settings->get_option_value( 'success_redirect_url' ) ); ?>" />
              <p class="description">(Optional) Full URL (with 'http') to redirect to for successful transactions. default: ""</p>
            </td>
          </tr>
          <!-- Failed Redirect URL -->
          <tr valign="top">
            <th scope="row">
              <label for="paytron_options[failed_redirect_url]"><?php _e( 'Failed Redirect URL', 'rave-pay' ); ?></label>
            </th>
            <td class="forminp forminp-text">
              <input class="regular-text code" type="text" name="paytron_options[failed_redirect_url]" value="<?php echo esc_attr( $admin_settings->get_option_value( 'failed_redirect_url' ) ); ?>" />
              <p class="description">(Optional) Full URL (with 'http') to redirect to for failed transactions. default: ""</p>
            </td>
          </tr>
        </tbody>
      </table>
      <?php submit_button(); ?>
    </form>

  </div>
