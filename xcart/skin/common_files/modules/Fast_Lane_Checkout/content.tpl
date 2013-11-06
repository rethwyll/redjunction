{*
f2c96ddb72c96605401a2025154fc219a84e9e75, v7 (xcart_4_6_1), 2013-08-19 12:16:49, content.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $top_message}
  {include file="main/top_message.tpl"}
{/if}

{if $main ne 'cart'}

  {include file="modules/Fast_Lane_Checkout/tabs_menu.tpl"}
  <div class="clearing"></div>

{else}

  <div class="checkout-buttons">
    {if !$std_checkout_disabled and !$gcheckout_enabled and !$amazon_enabled and !$paypal_express_active}
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_checkout style="div_button" href="cart.php?mode=checkout" additional_button_class="checkout-3-button"}
    {/if}
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_continue_shopping style="div_button" href=$stored_navigation_script additional_button_class="checkout-1-button"}
  </div>
  <div class="clearing"></div>

{/if}

{include file="modules/Fast_Lane_Checkout/home_main.tpl"}
