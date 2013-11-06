{*
50d259a2c23a547c0fecb1fafd08ee23accb4f99, v4 (xcart_4_5_1), 2012-06-14 09:14:35, cart_checkout_links.tpl, aim 
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="cart-checkout-links">
{if $active_modules.Wishlist ne "" or $minicart_total_items gt 0}
<hr class="minicart" />
{/if}
{if $minicart_total_items gt 0}
  <ul>
    <li><a href="cart.php">{$lng.lbl_view_cart}</a></li>
    
    {getvar var='paypal_express_active' func='func_get_paypal_express_active'}
    {if !$gcheckout_enabled and !$paypal_express_active and !$amazon_enabled}
      <li><a href="cart.php?mode=checkout">{$lng.lbl_checkout}</a></li>
    {/if}
  </ul>
{/if}
</div>
