{*
77c5c46814ed430684d3e2145cd8915fc8b7c81c, v1 (xcart_4_6_1), 2013-08-26 17:55:46, card_list_customer_checkout.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

{if $saved_cards}
  <ul class="saved-cards">
    <li class="default-item">
      <label>
        <input type="hidden" name="recharge_orderid" value="{$default_card_orderid}" id="default_order_id"/>
        <span class="card-icon-container">
          <span class="card {$saved_cards[$default_card_orderid].type|lower}"></span>
        </span>
        <span class="number">{$saved_cards[$default_card_orderid].number}</span>
      </label>  
    </li>
    {foreach from=$saved_cards item=card key=orderid}
      <li class="all-items" style="display: none">
        <label>
          <input type="radio" name="recharge_orderid" value="{$orderid}" {if $orderid eq $default_card_orderid}checked="checked"{/if}/>
          <span class="card-icon-container">
            <span class="card {$card.type|lower}"></span>
            <img src="{$ImagesDir}/spacer.gif" alt="{$card.type}" width="0" height="0"/>
          </span>
          <span class="number">{$card.number}</span>
        </label>
      </li>
    {/foreach}
  </ul>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <small><a class="default-item" href="javascript: void(0);" onclick="javascript: switchSavedCards();">{$lng.lbl_show_all_cards}</a></small>
{/if}

<script type="text/javascript">
{literal}

    function switchSavedCards() {
        $('.default-item').hide();
        $('.all-items').show();
        $('#default_order_id').remove();
    }

{/literal}
</script>
