{*
77c5c46814ed430684d3e2145cd8915fc8b7c81c, v3 (xcart_4_6_1), 2013-08-26 17:55:46, xpc_iframe.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

{* Draws iframe container *}

{if $active_modules.One_Page_Checkout}

  <script type="text/javascript">
    xpc_paymentids[{$payment.paymentid}] = {$payment.paymentid};
  </script>

  <iframe style="width: 100%; border: 0px; height: 0px;" border="0" marginheight="0" marginwidth="0" frameborder="0" class="xpc_iframe" id="xpc_iframe{$payment.paymentid}" name="xpc_iframe{$payment.paymentid}">
  </iframe>

{elseif $active_modules.Fast_Lane_Checkout}

  <a name="payment_details"></a>

  <div class="xpc_iframe_container">
    <iframe style="width: 100%; border: 0px; height: 100px;" border="0" marginheight="0" marginwidth="0" frameborder="0" class="xpc_iframe" id="xpc_iframe" name="xpc_iframe" src="payment/cc_xpc_iframe.php?paymentid={$paymentid}">
    </iframe>
  </div>

  <script type="text/javascript">
    xpc_iframe_method = true;

    if (window.location.hash == '')
      window.location.hash = 'payment_details';

    $('.xpc_iframe_container').block();
  </script>

{/if}

{include file="modules/XPayments_Connector/allow_recharges.tpl"}
