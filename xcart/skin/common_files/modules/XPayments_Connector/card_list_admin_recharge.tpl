{*
77c5c46814ed430684d3e2145cd8915fc8b7c81c, v1 (xcart_4_6_1), 2013-08-26 17:55:46, card_list_admin_recharge.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

{if $saved_cards}
  <ul class="saved-cards">
    {foreach from=$saved_cards item=card key=orderid}
      <li>
        <label>
          <input type="radio" name="recharge_orderid" value="{$orderid}" {if $orderid eq $default_card_orderid}checked="checked"{/if}/>
          <span class="card-icon-container">
            <span class="card {$card.type|lower}"><img src="{$ImagesDir}/spacer.gif" alt="{$card.type}"/></span>
          </span>
          <span class="number">{$card.number}</span>
        </label>
      </li>
    {/foreach}
  </ul>
{/if}
