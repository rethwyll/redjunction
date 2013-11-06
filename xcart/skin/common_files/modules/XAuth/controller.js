/* vim: set ts=2 sw=2 sts=2 et: */
/**
 * Common controller
 *
 * @category   X-Cart
 * @package    X-Cart
 * @subpackage JS Library
 * @author     Ruslan R. Fazlyev <rrf@x-cart.com>
 * @version    4f1ff6c4e9836a1056434e21838988bb60aaef4d, v2 (xcart_4_6_0), 2013-04-30 17:31:58, controller.js, random
 * @link       http://www.x-cart.com/
 * @see        ____file_see____
 */

function xauthTogglePopup(link, mode)
{
  xauthLoadingChecker(true);

  return !popupOpen(
    'xauth_layer.php' + (mode ? '?mode=' + mode : ''),
    null,
    {
      width: 435,
      height: 285,
      closeOnEscape: true
    }
  );
}

function xauthLoadingChecker(reset)
{
  if (reset) {
    arguments.callee.counter = 0;
  }

  if (0 < jQuery('.xauth-popup-rpx iframe').length) {

    // Safari / Webkit hack
    if (jQuery.browser.safari || jQuery.browser.webkit) {
      var pd = jQuery('.xauth-popup-rpx').parents('.ui-dialog').find('.ui-dialog-titlebar');

      pd.css('width', pd.width() + 'px');

      setTimeout(
        function() { pd.css('width', 'auto'); },
        100
      );

    } else if (jQuery.browser.msie && jQuery.browser.version == 7) {

      if (jQuery('table.xauth-vertical').length) {
        $('.popup-dialog').dialog({ width: 400, position: 'center' });

      } else if (jQuery('table.xauth-horizontal').length) {
        $('.popup-dialog').dialog({ width: 790, position: 'center' });
      }

      jQuery('.ui-dialog-titlebar').width(jQuery('.ui-dialog').width() - 30);

    }

    jQuery('.xauth-popup-rpx iframe').load(
      function() {
        jQuery('.xauth-bg-loading').remove();
      }
    );

  } else if (100 > arguments.callee.counter) {
      setTimeout(arguments.callee, 300);
      arguments.callee.counter++;
  }
}
xauthLoadingChecker.counter = 0;

function xauthOpenProductShare(button)
{
  var product = jQuery('h1').eq(0).text();
  var description = jQuery('.product-details .descr').text();
  
  if (!product || !window.janrain) {
    return false;
  }

  window.janrain.engage.share.reset();
  window.janrain.engage.share.setDescription(xauthRPXEscape(description ? description : product));
  window.janrain.engage.share.setUrl(self.location + '');
  window.janrain.engage.share.show();
  return true;
}

function xauthOpenCartItemShare(elm)
{
  var tbl = jQuery(elm).parents('tr').eq(0);
  var product = jQuery('.details a', tbl).text();
  var description = jQuery('.details .descr', tbl).text();
  var url = jQuery('.details a', tbl).attr('href');

  if (!tbl.length || !product || !url || !window.janrain) {
    return false;
  }

  if (url.search(/^\//) != -1) {
    url = 'http://' + xauth_current_host + url;

  } else if (url.search(/^https?:\/\//) == -1) {
    url = xauth_catalogs_customer + '/' + url;
  }

  window.janrain.engage.share.reset();
  window.janrain.engage.share.setDescription(xauthRPXEscape(description ? description : product));
  window.janrain.engage.share.setUrl(url);
  window.janrain.engage.share.show();

  return true;
}

function xauthOpenInvoiceShare(elm)
{
  if (!window.janrain) {
    return false;
  } 

  window.janrain.engage.share.reset();
  window.janrain.engage.share.show();

  return true;
}

function xauthRPXEscape(str)
{
  str = str.substr(0, 760);

  var len = str.length;
  var collect = '';
  var c, x, n;

  for (var i = 0; i < len; i++) {
    n = str.charCodeAt(i);

    if (n <= 127) {
        collect += str.substr(i, 1);

    } else {
        c = n.toString(16);
        for (x = c.length; x < 4; x++) {
          c = '0' + c;
        }
        collect += '\\u' + c;
    }
  }

  return collect;
}

function xauthRPXPrepareURL(url)
{
  if (url.substr(0, 1) == '/') {
    url = self.location.protocol + '//'
      + self.location.host
      + (self.location.port ? ':' + self.location.port : '')
      + url

  } else if (-1 == url.search(/^https?:\/\//)) {
    url = self.location.protocol + '//'
      + self.location.host
      + (self.location.port ? ':' + self.location.port : '')
      + self.location.pathname.replace(/\/[^\/]+\.(php|html|htm)$/, '') + '/' + url;
  }

  return url;
}

setTimeout(
  function() {
    jQuery(document).ready(
      function() {
        if (jQuery('#dialog-message .xauth-err').length) {
          jQuery('#dialog-message').clearQueue();
        }

        jQuery('a,input,button').filter(
          function() {
            return 'undefined' != typeof(this.onclick)
              && this.onclick
              && this.onclick.toString().search(/popupOpen..login\.php../) != -1;
          }
        ).click(
          function() {
            xauthLoadingChecker(true);
          }
        );

        if ((self.location + '').search(/login.php/) != -1) {
          xauthLoadingChecker(true);
        }
      }
    );
  },
  500
);
