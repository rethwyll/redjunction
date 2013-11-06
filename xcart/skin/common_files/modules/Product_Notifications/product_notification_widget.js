/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Ajax product notification widget
 * 
 * @category   X-Cart
 * @package    Modules
 * @subpackage Product Notification
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com> 
 * @version    572af17946fc92f59611bb3a27492ea64d78fd2c, v3 (xcart_4_5_5), 2012-12-20 15:15:39, product_notification_widget.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

// Widget :: factory
ProductNotificationWidget = function(productid, variantid, type) {
  // Check if jQuery is used
  if (typeof(window.jQuery) == 'undefined') {
    return false;
  }

  // Check if widget constants defined
  if (typeof(window.ProductNotificationWidget_CONST) == 'undefined') {
    return false;
  }

  // Check parameters
  if (!type || !productid) {
    return false;
  }

  if (
    typeof(variantid) == 'undefined'
    || variantid <= 0
  ) {
    variantid = 0;
  }
  
  // Get HTML widget root element
  var rootElement = $('#' + ProductNotificationWidget_CONST.ROOT_ELEMENT_ID_PREFIX + productid + '_' + type).get(0);
  if (!rootElement) {
    return false;
  }

  // Create an widget object
  if (!rootElement.productNotificationWidget) {
    rootElement.productNotificationWidget = new ProductNotificationWidget.obj(rootElement, productid, variantid, type);
  }

  return rootElement.productNotificationWidget;
}

// Widget :: object
ProductNotificationWidget.obj = function(rootElement, productid, variantid, type) {
  this.rootElement = $(rootElement);
  this.productid = productid;
  this.variantid = variantid;
  this.type = type;

  this.init();
}

/*
 * Properties 
 */
ProductNotificationWidget.obj.prototype.rootElement = false;
ProductNotificationWidget.obj.prototype.type = false;
ProductNotificationWidget.obj.prototype.productid = 0;
ProductNotificationWidget.obj.prototype.variantid = 0;
ProductNotificationWidget.obj.prototype.inStock = 0;
ProductNotificationWidget.obj.prototype.minAmount = 0;
ProductNotificationWidget.obj.prototype.submitButton = false;
ProductNotificationWidget.obj.prototype.requestButton = false;
ProductNotificationWidget.obj.prototype.emailInput = false;
ProductNotificationWidget.obj.prototype.isEmailInputReset = false;
ProductNotificationWidget.obj.prototype.isEmailInputContentChanged = false;

/*
 * Methods
 */

// Initialize widget
ProductNotificationWidget.obj.prototype.init = function() {
  // Define form elements
  this.submitButton = this.rootElement.find('#' + ProductNotificationWidget_CONST.SUBMIT_BUTTON_ELEMENT_ID_PREFIX + this.productid + '_' + this.type);
  this.submitWaiting = this.rootElement.find('#' + ProductNotificationWidget_CONST.SUBMIT_WAITING_ELEMENT_ID_PREFIX + this.productid + '_' + this.type);
  this.emailInput = this.rootElement.find('#' + ProductNotificationWidget_CONST.EMAIL_INPUT_ELEMENT_ID_PREFIX + this.productid + '_' + this.type);
  this.requestButton = $('#' + ProductNotificationWidget_CONST.REQUEST_BUTTON_ELEMENT_ID_PREFIX + this.productid + '_' + this.type);
  this.messageBlock = this.rootElement.find('#' + ProductNotificationWidget_CONST.MESSAGE_ELEMENT_ID_PREFIX + this.productid + '_' + this.type);


  // Define email input initial state
  if (this.emailInput.val() != ProductNotificationWidget_CONST.LBL_PROD_NOTIF_EMAIL_DEFAULT) {
    // Email value is pre-filled
    this.isEmailInputReset = true;
  }
  
  // Bind event handlers
  var s = this;
 
  this.requestButton.click(
    function(data) {
      return s.toggleBody();
    }
  );

  if (typeof(this.requestButton.tooltip) == 'function' && !$.browser.msie) {
    this.requestButton.tooltip(
      {
        id: 'prod_notif_tooltip',
        extraClass: 'prod-notif-tooltip-' + this.type,
        track: false,
        delay: 0,
        showURL: false,
        fade: 200
      }
    );
  }

  this.submitButton.click(
    function(data) {
      return s.submitButton_click();
    }
  );

  this.emailInput.keydown(
    function(event){
        if(event.keyCode == 13){
          event.preventDefault();
          return s.submitButton_click();
        }
    }
  );

  this.emailInput.focus(
    function(data) {
      return s.emailInput_focus();
    }
  );
  
  this.emailInput.change(
    function(data) {
      return s.emailInput_change();
    }
  );
  
  this.emailInput.blur(
    function(data) {
      return s.emailInput_blur();
    }
  );
}

// Refresh widget depending on product/variant data
ProductNotificationWidget.obj.prototype.refresh = function(data) {
  // TODO: object inheritance
  if (typeof(data.inStock) != 'undefined' && data.inStock >= 0) {
    this.inStock = data.inStock;
  }
  if (typeof(data.minAmount) != 'undefined' && data.minAmount >= 0) {
    this.minAmount = data.minAmount;
  }
  
  if ('B' == this.type) {
    if (this.inStock <= 0 || this.inStock < this.minAmount) {
      // Product/variant is out of stock
      this.show();

    } else {
      this.hide();
    }
  }

  if ('L' == this.type) {
    if (this.inStock > ProductNotificationWidget_CONST.PROD_NOTIF_L_AMOUNT) {
      this.show();

    } else {
      this.hide();
    }
  }

}

// Hide widget
ProductNotificationWidget.obj.prototype.hide = function() {
  this.requestButton.hide();
  this.hideBody();
}

// Show widget
ProductNotificationWidget.obj.prototype.show = function() {
  this.hideBody();
  this.clearMessages();
  this.requestButton.show();
}

// Find selected variant
ProductNotificationWidget.obj.prototype.detectProductVariant = function() {
  if (
    typeof variants != 'undefined' 
    && variants
    && typeof getPOValue == 'function'
  ) {
    for (var x in variants) {
      if (!hasOwnProperty(variants, x) || variants[x][1].length == 0) {
        continue;
      }
      variantid = x;
      for (var c in variants[x][1]) {
        if (!hasOwnProperty(variants[x][1], c)) {
          continue;
        }

        if (getPOValue(c) != variants[x][1][c]) {
          variantid = false;
          break;
        }
      }

      if (variantid) {
        this.variantid = variantid;
        break;
      }
    }
  }
}

// Validate e-mail address
ProductNotificationWidget.obj.prototype.checkEmail = function() {
  var email = this.emailInput.val();

  if (!email || 0 == email.length) {
    return false;
  }
  if (email.replace(/^\s+/g, '').replace(/\s+$/g, '').search(ProductNotificationWidget_CONST.PROD_NOTIF_EMAIL_REGEXP) == -1) {
    return false;
  }

  return true;
}

// Show message to customer 
ProductNotificationWidget.obj.prototype.showMessage = function(text, isError) {
  this.messageBlock.html('');
  if (isError) {
    this.messageBlock.addClass(ProductNotificationWidget_CONST.ERROR_MSG_CSS);

  } else {
    this.messageBlock.removeClass(ProductNotificationWidget_CONST.ERROR_MSG_CSS);
  }
  this.messageBlock.html(text);
}

// Clear all messages and error alerts
ProductNotificationWidget.obj.prototype.clearMessages = function() {
  this.emailInput.removeClass(ProductNotificationWidget_CONST.INVALID_EMAIL_CSS);
  this.messageBlock.html('');
}

// Show/hide product notification request form
ProductNotificationWidget.obj.prototype.toggleBody = function() {
  if (this.isBodyVisible) {
    this.hideBody();

  } else {
    this.showBody();
  }
}

// Show product notification request form
ProductNotificationWidget.obj.prototype.showBody = function() {
  this.rootElement.slideDown(ProductNotificationWidget_CONST.REQUEST_FORM_SLIDE_DELAY);
  this.isBodyVisible = true;
}

// Hide product notification request form
ProductNotificationWidget.obj.prototype.hideBody = function() {
  this.rootElement.slideUp('fast');
  this.clearMessages();
  this.isBodyVisible = false;
}

// Send a subscription data to the server by AJAX
ProductNotificationWidget.obj.prototype.submit = function() {
  this.startWaiting();

  // AJAX request
  var s = this;
  $.getJSON(
    ProductNotificationWidget_CONST.PROD_NOTIF_SUBMIT_PHP,
    {
      "mode": ProductNotificationWidget_CONST.PROD_NOTIF_SUBMIT_MODE,
      "productid": this.productid,
      "variantid": this.variantid,
      "type": this.type,
      "email": this.email
    },
    function(response) {
      s.submitCallback(response);
      s.stopWaiting();
    }
  );
}

// Submit callback function
ProductNotificationWidget.obj.prototype.submitCallback = function(response) {
  var isError = false;
  var message = ProductNotificationWidget_CONST.ERR_SUBMIT_PROD_NOTIF_UNKNOWN;
  if (!response) {
    isError = true;

  } else {
    if (typeof response.status != 'undefined') {
      if (0 == response.status) {
        // OK
        message = ProductNotificationWidget_CONST.MSG_SUBMIT_PROD_NOTIF_OK;
      } else if (1 == response.status) {
        // already subscribed
        message = ProductNotificationWidget_CONST.MSG_PROD_NOTIF_ALREADY_SUBSCRIBED;
      } else if (typeof response.message != 'undefined' && response.message.length > 0) {
        // Get error message
        isError = true;
        message = response.message;
      }
    }
  }

  this.showMessage(message, isError);

  return true;
}

// Show waiting image
ProductNotificationWidget.obj.prototype.startWaiting = function() {
  this.emailInput.attr('disabled', 'disabled');
  this.submitButton.hide();
  this.submitWaiting.show();
}

// Show waiting image
ProductNotificationWidget.obj.prototype.stopWaiting = function() {
  this.emailInput.removeAttr('disabled');
  this.submitWaiting.hide();
  this.submitButton.show();
}

// Focus changing event handler for the 'email' input
ProductNotificationWidget.obj.prototype.emailInput_focus = function() {
  this.clearMessages();
  if (!this.isEmailInputReset) {
    this.emailInput.val('');
    this.emailInput.removeClass(ProductNotificationWidget_CONST.DEFAULT_EMAIL_CSS);
    this.isEmailInputReset = true;
  }
  return true;
}

// Text changing event handler for the 'email' input
ProductNotificationWidget.obj.prototype.emailInput_change = function() {
  this.isEmailInputContentChanged = true;
  return true;
}

// 'Blur' event handler for the 'email' input
ProductNotificationWidget.obj.prototype.emailInput_blur = function() {
  if (
    !this.isEmailInputReset
    && !this.isEmailInputContentChanged
  ) {
    this.emailInput.val(ProductNotificationWidget_CONST.LBL_PROD_NOTIF_EMAIL_DEFAULT);
    this.emailInput.addClass(ProductNotificationWidget_CONST.DEFAULT_EMAIL_CSS);
    this.isEmailInputReset = false;
  }
  return true;
}

// Click event handler for submit button
ProductNotificationWidget.obj.prototype.submitButton_click = function() {
  this.clearMessages();

  var err = false;

  // Check email
  if (!this.checkEmail()) {
    this.emailInput.addClass(ProductNotificationWidget_CONST.INVALID_EMAIL_CSS);
    this.showMessage(ProductNotificationWidget_CONST.ERR_PROD_NOTIF_EMAIL, true);
    err = true;
  }

  if (!err) {
    // Determine selected product variant
    this.detectProductVariant();
    this.email = this.emailInput.val();
    this.submit();
  }
  return false;
}

/*
 * Define array of all product notification widgets of the page
 */
ProductNotificationWidgets = [];
