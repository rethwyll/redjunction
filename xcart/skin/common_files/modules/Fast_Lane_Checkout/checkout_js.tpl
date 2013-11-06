{*
f2c96ddb72c96605401a2025154fc219a84e9e75, v12 (xcart_4_6_1), 2013-08-19 12:16:49, checkout_js.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var txt_accept_terms_err = '{$lng.txt_accept_terms_err|wm_remove|escape:"javascript"}';
var lbl_warning = '{$lng.lbl_warning|wm_remove|escape:"javascript"}';

{literal}
function checkCheckoutForm() {
  var result = true;
{/literal}
  var unique_key = "{unique_key}";

{literal}

  if (!result) {
    return false;
  }

  var termsObj = $('#accept_terms')[0];
  if (termsObj && !termsObj.checked) {
    xAlert(txt_accept_terms_err, lbl_warning, 'W');
    return false;
  }

  if (result && checkDBClick()) {
    if (document.getElementById('msg'))
       document.getElementById('msg').style.display = '';

    if (document.getElementById('btn_box'))
       document.getElementById('btn_box').style.display = 'none';
  }

  return result;
}

var checkDBClick = function() {
  var clicked = false;
  return function() {
    if (clicked)
      return false;

    clicked = true;
    return true;
  }
}();

function checkCheckoutFormXP() {
  if (checkCheckoutForm()) {
    if (window.postMessage && window.JSON) {
      var message = {
        message: 'submitPaymentForm',
        params: {}
      };

      if (window.frames['xpc_iframe'])
        window.frames['xpc_iframe'].postMessage(JSON.stringify(message), '*');
    }
  }

  return false;
}

function messageListener(event) {
  if (event.source === window.frames['xpc_iframe'] && window.JSON) {
    var msg = JSON.parse(event.data);
    if (msg) {
      if ('paymentFormSubmitError' === msg.message) {
        $('#msg').hide();
        $('#btn_box').show();
      }

      if ('ready' === msg.message) {
        msg.params.height >= 0 && $('#xpc_iframe').height(msg.params.height);

        $('.xpc_iframe_container').unblock();
      }
    }
  }
}

if (window.addEventListener)
  addEventListener('message', messageListener, false);
else
  attachEvent('onmessage', messageListener);

{/literal}
//]]>
</script>

{if $active_modules.TaxCloud}
  {include file="modules/TaxCloud/exemption_js.tpl"}
{/if}

