{*
537a3272759a84a0176b1e4663e61c66f207a7b3, v1 (xcart_4_5_3), 2012-08-03 11:35:59, opc_currency_note.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

{if $mcNoteDisplayed ne 1 and $store_currency ne $primary_currency}
{assign var="mcNoteDisplayed" value=1}
{alter_currency value=1 no_brackets=1 assign="billCurr"}
{currency value=1 precision=1 assign="displCurr"}
<div class="mc-opc-currency-note-block">
  {if $primary_currency ne $primary_currency_data.symbol}
  {$lng.mc_txt_currency_note|substitute:"currency":$primary_currency:"symbol":$primary_currency_data.symbol:"X":$billCurr:"Y":$displCurr}
  {else}
  {$lng.mc_lblcurrency_note2|substitute:"currency":$primary_currency:"X":$billCurr:"Y":$displCurr}
  {/if}
</div>
{/if}

