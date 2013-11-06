{*
537a3272759a84a0176b1e4663e61c66f207a7b3, v1 (xcart_4_5_3), 2012-08-03 11:35:59, currency_note_order.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

{if $_mc_display_note}
{assign var="_mc_currsymbol" value=$order.extra.mc_primary_currency_symbol|default:$order.extra.mc_primary_currency}
{alter_currency value=1 currency=$order.extra.mc_primary_currency no_brackets=1 assign="billCurr"}
{currency value=1 currency=$order.extra.mc_store_currency currency_rate=$order.extra.mc_store_currency_rate precision=1 assign="displCurr"}
<div {if $is_nomail eq 'Y'}class="mc-currency-note-order-block"{else}style="background-color: #eeeeee; border-top: 2px solid #999999; padding: 5px; width: 100%; margin-top: 15px; margin-bottom: 15px;"{/if}>
  {$lng.mc_txt_currency_note_order|substitute:"currency":$order.extra.mc_primary_currency_name:"symbol":$_mc_currsymbol:"X":$billCurr:"Y":$displCurr}
</div>
{/if}

