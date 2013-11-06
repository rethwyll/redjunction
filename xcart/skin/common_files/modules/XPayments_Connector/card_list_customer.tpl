{*
8114a90cb4d048f396f356dceb00470d99730617, v2 (xcart_4_6_1), 2013-09-03 16:22:55, card_list_customer.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

<form action="saved_cards.php?mode=set_default" method="post">
  <table cellspacing="3" cellpadding="2" border="0" width="90%" class="saved-cards">
    {if $saved_cards}
      <tr>
        <th>{$lng.lbl_order}</th>
        <th>{$lng.lbl_saved_card_header}</th>
        <th class="default-column">{$lng.lbl_saved_card_default}</th>
        <th></th>
      </tr>
      {foreach from=$saved_cards item=card key=orderid}
        <tr>
          <td>
            <a href="order.php?orderid={$orderid}">#{$orderid}</a>
          </td>
          <td>
            <div class="card-icon-container">
              <span class="card {$card.type|lower}"><img src="{$ImagesDir}/spacer.gif" alt="{$card.type}"/></span>
            </div>
            <div class="number">{$card.number}</div>
          </td>
          <td class="default-column">
            <input type="radio" name="orderid" {if $orderid eq $default_card_orderid} checked="checked"{/if} value="{$orderid}">
          </td>
          <td>
            <a href="saved_cards.php?mode=delete&orderid={$orderid}">{$lng.lbl_remove}</a>
          </td>
        </tr>
      {/foreach}
    {/if}  
    <tr class="button-row">
      <td colspan="4">
        {include file="customer/buttons/button.tpl" button_title=$lng.lbl_saved_card_set_default_card type="input" additional_button_class="main-button"}
      </td>
    </tr>
  </table>
</form>
