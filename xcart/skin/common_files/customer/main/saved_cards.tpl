{*
77c5c46814ed430684d3e2145cd8915fc8b7c81c, v1 (xcart_4_6_1), 2013-08-26 17:55:46, saved_cards.tpl, aim 
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_saved_cards}</h1>

{$lng.lbl_saved_cards_top_note}

<br /><br />

{if $saved_cards}

  {include file="modules/XPayments_Connector/card_list_customer.tpl"}

{else}

  {$lng.lbl_no_saved_cards}

{/if}
