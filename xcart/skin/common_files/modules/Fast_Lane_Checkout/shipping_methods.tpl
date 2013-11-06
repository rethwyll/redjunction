{*
350042ea72f8d02d75dfce69a0fafa85d0867aed, v5 (xcart_4_4_4), 2011-07-29 13:41:09, shipping_methods.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{foreach from=$shipping item=s name=sm}
  <label{interline name=sm}>
    {if not $simple_list}
      <input type="radio" name="shippingid" value="{$s.shippingid}"{if $s.shippingid eq $cart.shippingid} checked="checked"{/if}{if $allow_cod} onclick="javascript: func_display_cod();"{/if} />
    {/if}
    <span>
      {$s.shipping|trademark}{if $s.shipping_time ne ""} - {$s.shipping_time}{/if}{if $config.Appearance.display_shipping_cost eq "Y" and ($userinfo ne "" or $config.General.apply_default_country eq "Y" or $cart.shipping_cost gt 0)} ({currency value=$s.rate}){/if}
    </span>
  </label>
  {if $s.warning ne ""}
    <div class="{if $s.shippingid eq $cart.shippingid}error-message{else}small-note{/if}">{$s.warning}</div>
  {/if}
{/foreach}
