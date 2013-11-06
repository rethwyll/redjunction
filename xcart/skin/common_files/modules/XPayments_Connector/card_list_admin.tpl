{*
77c5c46814ed430684d3e2145cd8915fc8b7c81c, v1 (xcart_4_6_1), 2013-08-26 17:55:46, card_list_admin.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

{if $saved_cards}
    <table cellspacing="3" cellpadding="2" border="0" width="70%" class="saved-cards">
      <tr>
        <th>{$lng.lbl_order}</th>
        <th>{$lng.lbl_saved_card_header}</th>
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
          <td>
            <a href="user_modify.php?action=delete_saved_card&orderid={$orderid}&user={$smarty.get.user}&usertype=C">{$lng.lbl_remove}</a>
          </td>
        </tr>
      {/foreach}
    </table>
{/if}
