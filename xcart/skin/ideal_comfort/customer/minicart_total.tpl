{*
0a15ac9033113298aa7ae7d6e9fc237763d1d1c6, v1 (xcart_4_5_2), 2012-07-12 13:57:38, minicart_total.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="minicart">
  {if $minicart_total_items gt 0}

    <div class="valign-middle full">

      <table cellspacing="0" summary="{$lng.lbl_your_cart|escape}">
        <tr>
          <td class="your-cart">{$minicart_total_items} {$lng.lbl_cart_items} <span>/&nbsp;</span>
            {capture name=tt assign=val}
              {currency value=$minicart_total_cost}
            {/capture}
            {include file="main/tooltip_js.tpl" class="help-link" title=$val text=$lng.txt_minicart_total_note}
          </td>
        </tr>
        {getvar var='paypal_express_active' func='func_get_paypal_express_active'}
        {if !$gcheckout_enabled and !$paypal_express_active}
        <tr>
          <td class="state"><a href="cart.php?mode=checkout" class="minicart-checkout-link"><span>{$lng.lbl_checkout}</span></a></td>
        </tr>
        {/if}
      </table>

    </div>

  {else}

    <div class="valign-middle empty">

      <strong>{$lng.lbl_cart_is_empty}</strong>

    </div>

  {/if}

{if $minicart_total_standalone}
{load_defer_code type="css"}
{load_defer_code type="js"}
{/if}
</div>
