{*
4a6dcebbfb61538166b0873899a951eeae6a6368, v2 (xcart_4_6_1), 2013-09-03 16:28:33, allow_recharges.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if 
  ($config.General.checkout_module eq 'Fast_Lane_Checkout' and $payment_data.use_recharges eq "Y")
  or ($config.General.checkout_module ne 'Fast_Lane_Checkout' and $payment.use_recharges eq "Y")
}
  {if $userinfo.id}
    <br/>
    <label>
      <input type="checkbox" value="Y" name="allow_save_cards" {if $allow_save_cards eq "Y"}checked="checked"{/if}/>
      {$lng.lbl_allow_save_cards_checkout}
      <br/>
      <small><a href="javascript: void();" onclick="javascript: xAlert('{$lng.txt_save_cards_is_safe|escape}', '{$lng.lbl_information|escape}')">{$lng.lbl_save_cards_is_safe}</a></small>
    </label>
    <br/><br/>
  {else}
    <input type="hidden" name="allow_save_cards" value="N">
  {/if}
{/if}

