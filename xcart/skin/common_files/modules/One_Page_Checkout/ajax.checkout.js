/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Checkout widget for One Page Checkout module
 * 
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    18c042677f0bb079073b29812d103fe9dd320ee6, v54 (xcart_4_6_1), 2013-09-16 07:27:56, ajax.checkout.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

ajax.widgets.checkout = function(elm) {

  if (!elm) {
    elm = $('.opc-container').get(0);

  } else {
    elm = $(elm);
  }

  if (!elm.checkoutWidget) {
     new ajax.widgets.checkout.obj(elm);
  }

  return true;
}

// Class
ajax.widgets.checkout.obj = function(elm) {
  this.elm = elm;
  this.elm$ = $(elm);

  elm.checkoutWidget = this;

  var s = this;

  $(ajax.messages).bind(
    'opcUpdateCall',
    function(e, data) {
      return !s._callbackUpdateCommon(data);
    }
  );

  s._callbackUpdateCommon = function(data) {

    if (data && data.action && data.action != '') {

      switch (data.action) {

        case 'profileUpdate':
          return s._callbackUpdateProfile(data);
          break;

        case 'selectAddress':
          return s._callbackSelectAddress(data);
          break;

        case 'updateTotals':
          return s._callbackUpdateTotals(data);
          break;
        
        case 'updateCoupon':
          return s._callbackUpdateCoupon(data);
          break;

        case 'updateGC':
          return s._callbackUpdateGC(data);
          break;
 
        case 'shippingChanged':
          return s._callbackShippingChanged(data);
          break;
        
        case 'paymentChanged':
          return s._callbackPaymentChanged(data);
          break;

        case 'paymentMethodListChanged':
          return s._callbackPaymentMethodListChanged(data);
          break;
 
        default:
          return false;
          break;
      }
    }
  }

  s._doUpdateProfile = function(e) {
    return !s.doUpdateProfile(this, e);
  }
  
  s._changeProfile = function(e) {
    return !s.changeProfile(this, e);
  }

  s._selectAddress = function(e) {
    return !s.selectAddress(this, e);
  }

  s._selectShipping = function(e) {
    return !s.selectShipping(this, e);
  }

  s._updateShipping = function(e) {
    return !s.updateShipping(this, e);
  }

  s._updateTotals = function(e) {
    return !s.updateTotals(this, e);
  }

  s._updatePayment = function(e) {
    return !s.updatePayment(this, e);
  }

  s._changePMethod = function(e) {
    return !s.selectPMethod(this, e);
  }
  
  s._applyCoupon = function(e) {
    return !s.applyCoupon(this, e);
  }
  
  s._unsetCoupon = function(e) {
    return !s.unsetCoupon(this, e);
  }
 
  s._unsetCert = function(e) {
    return !s.unsetCert(this, e);
  }

  s._applyGC = function(e) {
    return !s.applyGC(this, e);
  }

  s._unsetGC = function(e) {
    return !s.unsetGC(this, e);
  }
  
  s._onProceedCheckout = function(e) {
    return s.onProceedCheckout(this, e);
  }

  s._prepareCheckout();
}

// Properties
ajax.widgets.checkout.obj.prototype.errorTTL      = 3000;
ajax.widgets.checkout.obj.prototype.paymentid     = paymentid;
ajax.widgets.checkout.obj.prototype.shippingid    = shippingid;

ajax.widgets.checkout.obj.prototype.elm           = false;
ajax.widgets.checkout.obj.prototype.profile       = false;
ajax.widgets.checkout.obj.prototype.payment       = false;
ajax.widgets.checkout.obj.prototype.shipping      = false;
ajax.widgets.checkout.obj.prototype.coupon        = false;
ajax.widgets.checkout.obj.prototype.totals        = false;
ajax.widgets.checkout.obj.prototype.authbox       = false;
ajax.widgets.checkout.obj.prototype.timer_id_check_button   = false;

// Widget :: check widget status
ajax.widgets.checkout.obj.prototype.isReady = function() {
  return this.elm$.length > 0 && this.checkElement();
}

// Widget :: ajax callback
ajax.widgets.checkout.obj.prototype.callback = function(state, a, b, c, d) {

  if (!this.isReady())
    return false;

  var s = false;

  if (state && c.messages) {
    for (var i = 0; i < c.messages.length; i++) {
      if (c.messages[i].name == 'opcUpdateCall' && c.messages[i].params.action) {
        s = true;
      }
    }
  }

  if (!s) {
    xAlert(txt_ajax_error_note, lbl_error, 'E');
  }

  return true;
}

// Widget :: check element
ajax.widgets.checkout.obj.prototype.checkElement = function(elm) {

  if (!elm) {
    elm = this.elm;
  }

  return elm && $(elm).hasClass('opc-container');
}

// Widget :: display error message
ajax.widgets.checkout.obj.prototype.showMessage = function(msg, type) {

  if (msg === undefined || msg == '') {
    return true;
  }

  $.unblockUI();

  showTopMessage(msg, type);

  return true;
}

// Widget :: display popup message
ajax.widgets.checkout.obj.prototype.showPopup = function(msg, type) {

  if (msg === undefined || msg == '') {
    return true;
  }

  $.unblockUI();

  xAlert(msg, '', type);

  return true;
}

// Widget :: update personal details
ajax.widgets.checkout.obj.prototype.doUpdateProfile = function(e) {

  if (!this.isReady()) {
    return false;
  }

  var form = $('form', this.profile).eq(0);

  if (!checkFormFields(form.get(0))) {
    return true;
  }

  if (!checkRegFormFields(form)) {
    return true;
  }

  $(this.profile).block();
  
  var s = this;

  return ajax.query.add(
    {
      type: 'POST',
      url: form.attr('action'),
      data: form.serialize(),
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d);
      }
    }
  ) !== false;

}

// Widget :: edit personal details
ajax.widgets.checkout.obj.prototype.changeProfile = function() {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.hideWaitMessage = true;
  this.disableCheckout();
  this.hideWaitMessage = false;
  $(this.profile).block();

  var _s = this;
  return ajax.core.loadBlock(this.profile, 'opc_profile', { edit_profile: true }, function() {});
}

// Widget :: update shipping block
ajax.widgets.checkout.obj.prototype.updateShipping = function() {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  $(this.shipping).block();
  
  var _s = this;
  return ajax.core.loadBlock(this.shipping, 'opc_shipping', {}, function() {
    shippingid = $('input[name=shippingid]:checked', this.shipping).val();
    _s._updateCodPaymentMethods();
  });
}

// Widget :: update totals block
ajax.widgets.checkout.obj.prototype.updateTotals = function() {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  $(this.totals).block();

  return ajax.core.loadBlock(this.totals, 'opc_totals');
}

ajax.widgets.checkout.obj.prototype.updatePayment = function() {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  $(this.payment).block();
  
  return ajax.core.loadBlock(this.payment, 'opc_payment');
}

// Widget :: select address from address book
ajax.widgets.checkout.obj.prototype.selectAddress = function(elm) {

  if (!this.isReady()) {
    return false;
  }
  
  var formid = $(elm).attr('id').replace('address_box_', 'address_');
  var f = $('#' + formid).get(0);

  $('.popup-dialog').dialog('close');

  $.blockUI();

  var s = this;

  return ajax.query.add(
    {
      type: 'POST',
      url: $(f).attr('action'),
      data: $(f).serialize(),
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d) && s.enableCheckout();
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d) && s.enableCheckout();
      }
    }
  ) !== false;
}

// Widget :: select shipping (switch shipping method)
ajax.widgets.checkout.obj.prototype.selectShipping = function(elm) {

  if (!this.isReady()) {
    return false;
  }

  if (shippingid == elm.value) {
    return false;
  }

  shippingid = elm.value;
  var f = elm.form;

  var s = this;

  s.disableCheckout();

  return ajax.query.add(
    {
      type: 'POST',
      url: $(f).attr('action'),
      data: {
        shippingid: shippingid,
        mode: 'checkout',
        action: 'update'
      },
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d) && (s.isXPCPayment(paymentid) ? true : s.enableCheckout());
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d) && s.enableCheckout();
      }
    }
  ) !== false;
}

// Widget :: reload XPC iframe
ajax.widgets.checkout.obj.prototype.reloadXPCIframe = function() {
  if (this.isXPCPayment(this.paymentid)) {
    this.selectPMethod($('#pm' + this.paymentid).get(0), true);
  }
}

// Widget :: select payment method
ajax.widgets.checkout.obj.prototype.selectPMethod = function(elm, allowReload) {

    if (!this.isReady() || !this.checkElement()) {
      return false;
    }

    if (paymentid == elm.value && allowReload !== true) {
      return false;
    }

    var pid     = elm.value;
    var empty   = false;

    $('input, select, textarea', 'tr.payment-details:visible').prop('disabled', true);
    $('table.checkout-payments tr.payment-details:visible', this.payment).hide();

    this.prepareXPCIframe(pid);

    $('tr#pmbox_' + pid, this.payment).not('.hidden').show();
    $('input, select, textarea', 'tr#pmbox_' + pid).removeAttr('disabled');

    paymentid = pid;

    var f = elm.form;
    var s = this;
  
    s.disableCheckout();

    return ajax.query.add(
      {
        type: 'POST',
        url: $(f).attr('action'),
        data: {
          action: 'update',
          mode: 'checkout',
          paymentid: paymentid
        },
        success: function(a, b, c, d) {
          return s.callback(true, a, b, c, d) && !$('#personal_details').is(':visible')
           && (s.isXPCPayment(paymentid) ? true : s.enableCheckout());
        },
        error: function(a, b, c, d) {
          return s.callback(false, a, b, c, d) && !$('#personal_details').is(':visible') && s.enableCheckout();
        }
      }
    ) !== false;


}

// Widget :: check if payment method is XPC iframe payment method
ajax.widgets.checkout.obj.prototype.isXPCPayment = function(paymentid) {
  return $.inArray(parseInt(paymentid), xpc_paymentids) != -1;
}

// Widget :: check if widget has XPC iframe payment methods
ajax.widgets.checkout.obj.prototype.hasXPCPayments = function() {
  for (var i in xpc_paymentids) {
    if (xpc_paymentids[i] > 0)
      return true;
  }

  return false;
}

// Widget :: apply coupon
ajax.widgets.checkout.obj.prototype.applyCoupon = function(elm) {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  var form = $('form', this.coupon).eq(0);
  $(this.coupon).block();

  var s = this;

  return ajax.query.add(
    {
      type: 'POST',
      url: form.attr('action'),
      data: form.serialize(),
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d);
      }
    }
  ) !== false;
}

// Widget :: unset coupon
ajax.widgets.checkout.obj.prototype.unsetCoupon = function(elm) {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.totals.block();

  var s = this;

  return ajax.query.add(
    {
      type: 'GET',
      url:  $(elm).attr('href'),
      data: {},
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d);
      }
    }
  ) !== false;
}

// Widget :: unset exemption certificate (TaxCloud)
ajax.widgets.checkout.obj.prototype.unsetCert = function(elm) {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.totals.block();

  var s = this;

  return ajax.query.add(
    {
      type: 'GET',
      url:  $(elm).attr('href'),
      data: {},
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d);
      }
    }
  ) !== false;
}

// Widget :: apply gift certificate
ajax.widgets.checkout.obj.prototype.applyGC = function(elm) {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.payment.block();

  var s = this;

  return ajax.query.add(
    {
      type: 'POST',
      url:  xcart_web_dir + '/payment/payment_giftcert.php',
      data: {
        gcid:   $('#gcid').val(),
        action: 'apply_gc'
      },
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d);
      }
    }
  ) !== false;
}

// Widget :: unset GC
ajax.widgets.checkout.obj.prototype.unsetGC = function(elm) {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.totals.block();

  var s = this;

  return ajax.query.add(
    {
      type: 'GET',
      url:  $(elm).attr('href'),
      data: {},
      success: function(a, b, c, d) {
        return s.callback(true, a, b, c, d);
      },
      error: function(a, b, c, d) {
        return s.callback(false, a, b, c, d);
      }
    }
  ) !== false;
}

// Widget :: final check and submit
ajax.widgets.checkout.obj.prototype.onProceedCheckout = function(elm) {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  if (!checkFormFields($('form[name=paymentform]', this.payment).get(0))) {
    return false;
  }

  if (!checkCheckoutForm(elm)) {
    return false;
  }

  var paymentData = $('.payment-details:visible', this.payment).find(':input').filter(
    function () {
      return this.checked
        || (
          this.type != 'checkbox'
          && this.type != 'radio'
        );
    }
  );

  var appendData = $(document.createElement('div'));

  if (paymentData.length > 0) {
    paymentData.each(function() {
      $(document.createElement('input'))
        .attr('type','hidden')
        .attr('name', $(this).attr('name'))
        .val($(this).val())
        .appendTo(appendData);
    });
  }

  appendData.prependTo(elm);

  if (!payments[this.paymentid].iframe) {

    // Regular checkout

    var waitMessage = msg_being_placed;

    if (payments[this.paymentid].message !== undefined) {
      waitMessage = payments[this.paymentid].message;
    }

    $.blockUI({
      message: '<span class="waiting being-placed">' + waitMessage + '</span>',
      css: {
        width: '450px',
        left:  $(window).width()/2-225
      }
    });

    if (this.xpcShown) {
      var message = {
        message: 'submitPaymentForm',
        params: {}
      };

      if (this.xpcShown.contentWindow && window.postMessage && window.JSON) {
        this.xpcShown.contentWindow.postMessage(JSON.stringify(message), '*');
      }

      return false;
    }

    elm.submit();

  } else {

    // iFrame checkout (note that only inputs and textareas are posted to iframe)

    var postVars = {};
    $.each($(elm).find('input,textarea'), function(key, val) {
      postVars[val.name] = val.value;
    });

    _s = this;

    popupOpen(
      $(elm).attr('action'),
      '',
      {
        close: function(event, ui) {
          _s.blockPage();
          if (typeof paymentCancelUrl != 'undefined' && paymentCancelUrl) {
            $.get(paymentCancelUrl, function() {
              location.reload();
            });
          } else {
            location.reload();
          }
        }
      },
      postVars
    );
  }

  return false;
}

// Widget :: block whole page and show "Please wait..." message
ajax.widgets.checkout.obj.prototype.blockPage = function() {

  $.blockUI({
    css: {
      width: '300px',
      left:  $(window).width()/2-150
    }
  });

  return true;
}

// Widget :: disable checkout sections and button during profile change
ajax.widgets.checkout.obj.prototype.disableCheckout = function() {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.disableCheckoutButton();
  this.disablePaymentSelection();
  this.disableShippingSelection();
}

// Widget :: enable checkout sections and button after profile change
ajax.widgets.checkout.obj.prototype.enableCheckout = function() {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  if (!paypal_express_selected) {
    this.enablePaymentSelection();
  }
  this.enableShippingSelection();

  if (!this.enableCheckoutButton()) {
    var _s = this;
    this.timer_id_check_button = setInterval( 
      function() { _s.enableCheckoutButton(); },
      2000
    );
  }

}

ajax.widgets.checkout.obj.prototype.blockSection = function (elem, showMessage) {
  var opts = {
    overlayCSS: {
      background: 'white',
      opacity: '0.6'
    }
  };

  if (!showMessage)
    opts['message'] = null;

  $(elem).block(opts);
}

ajax.widgets.checkout.obj.prototype.unblockSection = function (elem) {
  $(elem).unblock();
}

ajax.widgets.checkout.obj.prototype.disableShippingPaymentColumn = function () {
  this.blockSection('#opc_shipping_payment', this.isXPCPayment(paymentid) && this.hideWaitMessage !== true);
}

ajax.widgets.checkout.obj.prototype.enableShippingPaymentColumn = function () {
  this.unblockSection('#opc_shipping_payment');
}

ajax.widgets.checkout.obj.prototype.disableSummaryColumn = function () {
  this.blockSection('#opc_summary_li');
}

ajax.widgets.checkout.obj.prototype.enableSummaryColumn = function () {
  this.unblockSection('#opc_summary_li');
}

// Widget :: enable checkout button
ajax.widgets.checkout.obj.prototype.enableCheckoutButton = function() {

  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.enableSummaryColumn();

  // additional check

  if (!this.cbutton.hasClass('inactive')) {
    if (this.timer_id_check_button)
      clearInterval(this.timer_id_check_button);

    this.timer_id_check_button = false;
    return true;
  }

  if (!this.isCheckoutReady()) {
    return false;
  }

  if (this.timer_id_check_button)
    clearInterval(this.timer_id_check_button);

  this.timer_id_check_button = false;
  this.cbutton.removeClass('inactive').unbind('click');
  return true;
}

// Widget :: disable checkout button
ajax.widgets.checkout.obj.prototype.disableCheckoutButton = function() {

  if (this.timer_id_check_button)
    clearInterval(this.timer_id_check_button);
  this.timer_id_check_button = false;
  
  if (!this.isReady() || !this.checkElement()) {
    return false;
  }

  this.disableSummaryColumn();

  if (this.cbutton.hasClass('inactive')) {
    return true;
  }

  this.cbutton.addClass('inactive').bind('click', function(e) {
    e.stopPrapagation;
    return false;
  });
}

// Widget :: disable selection of shipping methods
ajax.widgets.checkout.obj.prototype.disableShippingSelection = function() {
  $('input, select', this.shipping).prop('disabled', true);

  this.disableShippingPaymentColumn();
}

// Widget :: disable selection of payment methods
ajax.widgets.checkout.obj.prototype.disablePaymentSelection = function() {
  //$('input, select', this.payment).prop('disabled', true);

  this.disableShippingPaymentColumn();
}

// Widget :: enable selection of shipping methods
ajax.widgets.checkout.obj.prototype.enableShippingSelection = function() {
  $('input, select', this.shipping).removeAttr('disabled');

  this.enableShippingPaymentColumn();

  return true;
}

// Widget :: enable selection of payment methods
ajax.widgets.checkout.obj.prototype.enablePaymentSelection = function() {
  $('input, select', this.payment).removeAttr('disabled');

  this.enableShippingPaymentColumn();

  return true;
}

// Widget :: check if all data is collected
// need_shipping global var is changed 
//    on init page load(opc_init_js.tpl) 
//    on ajax request loadBlock('opc_profile')(opc_profile.tpl) 
//    on ajax request loadBlock('opc_shipping')(opc_shipping.tpl)
ajax.widgets.checkout.obj.prototype.isCheckoutReady = function() {
  
  if (!this.isReady() || !this.checkElement()) {
    return false;
  }
  
  if (
    $('form[name=registerform]').length > 0
    || (need_shipping && (undefined === shippingid || shippingid <= 0))
    || (!paymentid || undefined === paymentid || paymentid <= 0)
  ) {
    return false;
  }
 
  return true;
}

// Widget :: profile form update listener
ajax.widgets.checkout.obj.prototype._callbackUpdateProfile = function(data) {

  if (data.status == 1) {

    if (data.new_user == 1 && !data.autologin) {
      self.location = 'home.php';
      return false;
    }

    var res = ajax.core.loadBlock(this.profile, 'opc_profile');

    this.showMessage(data.content, 'I');

    if (data.s_changed == 1 || data.new_user == 1) {
      this._updateShipping();
      this._updateTotals();
      this.switchXPCIframe(this.paymentid);
    }

    this._updatePayment();
    
    if (data.new_user == 1) {
      ajax.core.loadBlock(this.authbox, 'opc_authbox');
    }

    this.switchXPCIframe(this.paymentid);

    var ret = res && this.enableCheckout();

    this.reloadXPCIframe();

    return ret;

  }
  else if (data.error) {

    this.showMessage(data.error.errdesc, 'E');
    if (data.error.fields) {
      $.each(data.error.fields, function(f) {
        markErrorField($('#'+f));
      });
    }
    this.profile.unblock();

    if (typeof window['change_antibot_image'] == 'function') {
      change_antibot_image('on_registration');
    }

    if (data.av_error == 1) {
      popupOpen('popup_address.php?action=cart');
    }

  }

  return true;

}

// Widget :: totals update listener
ajax.widgets.checkout.obj.prototype._callbackUpdateTotals = function(data) {

  this.switchXPCIframe(this.paymentid);

  return this._updateTotals();
}

// Widget :: profile form update listener
ajax.widgets.checkout.obj.prototype._callbackSelectAddress = function(data) {

  if (data.status == 1) {
    var res = ajax.core.loadBlock(this.profile, 'opc_profile');
    this._updateShipping();
    this._updateTotals();
    this._updatePayment();
    this.switchXPCIframe(this.paymentid);
    this.showMessage(data.content, 'I');
    return res;
  }

  return true;
}

// Widget :: apply coupon listener
ajax.widgets.checkout.obj.prototype._callbackUpdateCoupon = function(data) {

  if (data.status == 1) {
    if (data.update_ship == 1) {
      this._updateShipping();
    }
    this._updateTotals();
    $('input[name=coupon]').val('');
    $('#coupon-applied-container, #couponform-container').toggle();
  }
  else {
    this.showPopup(data.message, 'E');
  }

  $(this.coupon).unblock();

  this.prepareXPCIframe(paymentid);
  this.switchXPCIframe(this.paymentid);

  return true;
}

// Widget :: unset GC listener
ajax.widgets.checkout.obj.prototype._callbackUpdateGC = function(data) {

  if (data.status == 1) {
    this._updateTotals();

    if (data.gc_total && data.gc_total > 0) {
      $('span.applied-gc span.currency').val(price_format(data.gc_total));
      $('span.applied-gc').show();
      if (data.covered && data.covered == 1) {
        $('input', this.payment).prop('disabled', true);
      }
      $('#gcid').val('');
      $('#pmbox_14').hide();
    } else {
      $('span.applied-gc').hide();
      $('#pmbox_14').show();
      $('input', this.payment).prop('disabled', false);
    }
  }

  if (data.message !== null) {
    this.showMessage(data.message, data.status == 1 ? 'I' : 'E');
  }

  this.payment.unblock();

  return true;
}

// Widget :: shipping change listener
ajax.widgets.checkout.obj.prototype._callbackShippingChanged = function(data) {

  shippingid = this.shippingid = data.value;

  this._updateCodPaymentMethods();

  return true;
}

// Widget :: payment change listener
ajax.widgets.checkout.obj.prototype._callbackPaymentChanged = function(data) {

  paymentid = this.paymentid = data.value;
  var f = $('#checkout_form');

  $('#paymentid', f).val(this.paymentid);
  $('#payment_method', f).val(payments[this.paymentid].name);
  document.checkout_form.setAttribute('action', payments[this.paymentid].url);

  this.switchXPCIframe(this.paymentid);

  return true;
}
 
// Widget :: payment method list change listener
ajax.widgets.checkout.obj.prototype._callbackPaymentMethodListChanged = function(data) {

  var url = 'cart.php?mode=checkout';

  if ($.browser.msie) {
    setTimeout(
      function() {
        self.location = url;
      },
      200
    );

  } else {
    self.location = url;
  }

  return true;
}


// Display/Hide COD payment methods and set current paymentid(optional)
ajax.widgets.checkout.obj.prototype._updateCodPaymentMethods = function() {
  var _paymentid = func_display_cod();

  if (_paymentid > 0 && _paymentid != this.paymentid) {
    if (payments[_paymentid].surcharge != payments[this.paymentid].surcharge) {
      // reload total Block to update surcharge and total
      this.selectPMethod($('#pm' + _paymentid).get(0));
    } else {
      this._callbackPaymentChanged({'value' : _paymentid});
    }
  } else if (_paymentid == 0) {
    this.paymentid = paymentid = null;
  }

  return true;
}

/**
 * Private methods 
 */

// Widget :: prepare checkout page
ajax.widgets.checkout.obj.prototype._prepareCheckout = function() {

  // Prepare general objects

  this.profile  = $('#opc_profile',  this.elm$);
  this.shipping = $('#opc_shipping', this.elm$);
  this.payment  = $('#opc_payment',  this.elm$);
  this.coupon   = $('#opc_coupon',   this.elm$);
  this.totals   = $('#opc_totals',   this.elm$);
  this.authbox  = $('#opc_authbox',  this.elm$);
  
  // Checkout button
  this.cbutton  = $('.place-order-button button', this.elm$);
  
  // Bind events
  
  $('.edit-profile', this.profile).live('click', this._changeProfile);
  $('form', this.profile).live('submit', this._doUpdateProfile);
  $('button.update-profile', this.profile).live('click', this._doUpdateProfile);
  $('.address-select').live('click', this._selectAddress);
  $('input[name=shippingid]').live('change', this._selectShipping);
  $('input[name=paymentid]').live('change', this._changePMethod);
  $('form', this.coupon).live('submit', this._applyCoupon);
  $('input[type=image]', this.coupon).live('click', this._applyCoupon);
  $('a.unset-coupon-link').live('click', this._unsetCoupon);
  if ($('a.unset-tc-cert-link').length > 0) {
    $('a.unset-tc-cert-link').live('click', this._unsetTcCert);
  }  
  $('a.unset-gc-link').live('click', this._unsetGC);
  $('#apply_gc_button', this.payment).live('click', this._applyGC);
  $('form[name=checkout_form]', this.elm$).submit(this._onProceedCheckout);

  // Prevent submitting payment methods form
  $('form input', this.payment).not('#gcid').live('keypress', function(event) {
    if (event.keyCode == '13') {
      event.stopPropagation();
      return false;
    }
  });

  $('input, select, textarea', 'tr.payment-details:hidden').prop('disabled', true);

  if (!this.isCheckoutReady()) {
    this.hideWaitMessage = true;
    this.disableCheckout();
    this.hideWaitMessage = false;
  }

  $(this)
    .ajaxStart(function() {
      this.disableCheckoutButton()
    })
    .ajaxStop(function() {
      this.enableCheckoutButton()
    });

  if (undefined !== av_error && av_error == true) {
    setTimeout( function() {
        popupOpen('popup_address.php?action=cart');
      },
      1000
    );
  }

  // Check Paypal express authorization
  if (paypal_express_selected) {
    $('input, select', this.payment).prop('disabled', true);
    $('a.paypal-express-remove').live('click', function(){
      $('input, select', this.payment).removeAttr('disabled');
      $('div.paypal-express-sel-note').remove();
      paypal_express_selected = false;
    });
  } 

  if (window.JSON) {
    if (window.addEventListener)
      addEventListener('message', $.proxy(this, 'messageListener'), false);
    else
      attachEvent('onmessage', $.proxy(this, 'messageListener'));
  }

  window.reloadXPCIframe = $.proxy(this, 'reloadXPCIframe');

  if (this.isCheckoutReady()) {
    this.prepareXPCIframe(this.paymentid);
    this.switchXPCIframe(this.paymentid);
  }
}

ajax.widgets.checkout.obj.prototype.messageListener = function (event) {
  var target = $('iframe.xpc_iframe').filter(function () {
    return $(this)[0].contentWindow === event.source;
  });

  if (target.length > 0) {
    var msg = JSON.parse(event.data);

    if (msg) {
      if ('paymentFormSubmitError' === msg.message) {
        $.unblockUI();
      }

      if ('ready' === msg.message) {
        msg.params.height >= 0 && target.height(msg.params.height);

        $.unblockUI();

        $('.blockMsg').remove();

        !$('#personal_details').is(':visible') && this.enableCheckout();
      }
    }
  }
}

/**
 * Check whether XPC iframe should be shown and if necessary switch it
 */
ajax.widgets.checkout.obj.prototype.switchXPCIframe = function(paymentid) {
  if (!xpc_iframe_methods) {
    return;
  }

  this.hideXPCIframe(paymentid);

  for (var i=0; i<xpc_paymentids.length; i++) {
    if (paymentid == xpc_paymentids[i])
      this.showXPCIframe(paymentid);
  }
}

ajax.widgets.checkout.obj.prototype.prepareXPCIframe = function(paymentid) {
  if (this.isXPCPayment(paymentid)) {
    $('.xpc_iframe').height(0);

    this.disableCheckout();
  }
}
 
/**
 * Hide XPC iframe
 */
ajax.widgets.checkout.obj.prototype.hideXPCIframe = function(paymentid) {
  var iframe = $('#xpc_iframe' + paymentid);

  iframe.height(0);

  this.xpcShown = false;
}

/**
 * Show XPC iframe for specified paymentid
 */

ajax.widgets.checkout.obj.prototype.showXPCIframe = function(paymentid) {
  var iframe = $('#xpc_iframe' + paymentid);

  iframe.attr('src', 'payment/cc_xpc_iframe.php?paymentid=' + paymentid);

  this.xpcShown = iframe[0];
}

/**
 * Onload handler 
 */
$(ajax).bind(
  'load',
  function() {
    var result = ajax.widgets.checkout();

    return result;
  }
);  


// Small hack for IE to bind onchange events to radio buttons
$(function () {
  if ($.browser.msie) {
    $('input:radio').live('click', function () {
      this.blur();
      if( this.type != "hidden" && this.style.display != "none" && !this.disabled ) {
        this.focus();
      }
    });
  }
});
