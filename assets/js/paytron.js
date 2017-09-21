'use strict';
/*global PaytronModal*/
/*global Paytron*/
/*global ptr_payment_args*/
/*global jQuery*/
/*global location*/

var cbUrl = ptr_payment_args.cb_url,
  form = jQuery('.paytron-payments-form'),
  redirect_url;

if (form) {
  form.on('submit', function(evt) {
    evt.preventDefault();
    processPayment(getFormData(this));
  });
}

var getFormData = function(form) {
  var formData = jQuery(form).data();
  var firstname = formData.firstname || jQuery(form).find('#ptr-firstname').val();
  var lastname = formData.lastname || jQuery(form).find('#ptr-lastname').val();
  var email = formData.email || jQuery(form).find('#ptr-email').val();
  var amount = formData.amount || jQuery(form).find('#ptr-amount').val();
  var paymentType = jQuery(form).find('ptr-payment-type').val() || '';
  var txnref = 'WP_' + form.id.toUpperCase() + '_' + new Date().valueOf();

  return {
    recipientAccountNumber: ptr_payment_args.accountNumber,
    recipientBankCode: ptr_payment_args.bankCode,
    amount: amount,
    currency: formData.charge_currency || ptr_payment_args.currency,
    companyLogo: ptr_payment_args.companyLogo,
    company: ptr_payment_args.company,
    description: ptr_payment_args.description,
    firstname: firstname,
    lastname: lastname,
    email: email,
    txnref: txnref,
  };
};

var processPayment = function(data) {
  var paytron = PaytronModal.showModal(data)
    .setCallback(function(e) {
      var req = {
        txnRef: data.txnref,
        amount: data.amount,
        email: data.email,
        ptrRef: e.data.reference
      }
      if (e.code === Paytron.SUCCESS) {
        req.status = 'successful';
      } else if (e.code === Paytron.FAILURE) {
        req.status = 'failed';
      }
      sendPaymentResponse(req);
    });
};

var sendPaymentResponse = function(data) {
  var args  = {
    action: 'process_paytron_payment',
    ptr_sec_code: jQuery( form ).find( '#ptr_sec_code' ).val(),
  };
  var req = Object.assign( {}, args, data );
  jQuery
    .post(cbUrl, req)
    .success(function(response) {
      var res = JSON.parse(response.data);
      if (res.redirect_url) {
        redirect_url = res.redirect_url;
        setTimeout(redirectTo, 1000, redirect_url);
      }
    });
};

var redirectTo = function(url) {
  location.href = url;
};
