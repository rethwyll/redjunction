{*
e261b269b530195bc00da078993ea2b2dccee8b3, v4 (xcart_4_5_5), 2012-10-29 06:51:42, iframe_common.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<!-- MAIN -->
<script type="text/javascript">
//<![CDATA[

  var paymentCancelUrl = '{$cancel_url}';

  {if $active_modules.One_Page_Checkout}
  function frameLoaded() {ldelim}
    $('#payment_content').unblock();
  {rdelim}
  {/if}

//]]>
</script>

<div id="payment_content">
  <iframe src="{$iframe_src}" height="{$height}" width="{$width}" frameborder="0" scrolling="no" onload="return frameLoaded();" id="payment_content_iframe"></iframe>
</div>

{if $active_modules.One_Page_Checkout}
<script type="text/javascript">
//<![CDATA[
  $('#payment_content').block();
//]]>
</script>
{else $active_modules.Fast_Lane_Checkout}
<script type="text/javascript">
//<![CDATA[
  var msg_confirmation = '{$lng.msg_payment_cancel_confirmation_js|wm_remove|escape:"javascript"}';
//]]>
</script>

<div align="center">
  {include file="customer/buttons/button.tpl" button_title=$lng.lbl_cancel href="javascript:if(confirm(msg_confirmation))window.location=paymentCancelUrl" additional_button_class="main-button" js_to_href="Y"}
</div>
{/if}
<!-- /MAIN -->
