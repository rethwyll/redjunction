{*
256314b934f377e4026ebcea1902f9ab73ee82a6, v2 (xcart_4_5_2), 2012-07-17 06:38:26, gcheckout_button.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $gcheckout_button}
  <div class="gcheckout-cart-buttons">
    <div>
      {if not $std_checkout_disabled or $paypal_express_active}
        <p>{$lng.lbl_or_use}</p>
      {/if}
      {$gcheckout_button}
    </div>
  </div>
{/if}
