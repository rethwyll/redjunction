{*
77c5c46814ed430684d3e2145cd8915fc8b7c81c, v1 (xcart_4_6_1), 2013-08-26 17:55:46, saved_cards.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $hide_header eq ""}
<tr>
<td colspan="3" class="RegSectionTitle">{$lng.lbl_saved_cards}<hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr>
  <td colspan="3">

    {if $saved_cards}

      {include file="modules/XPayments_Connector/card_list_admin.tpl"}

    {else}

      {$lng.lbl_no_saved_cards}

    {/if}

  </td>
</tr>
