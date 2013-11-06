{*
4a6a40d4ed82b72efe24961b6b25570f9d783e7f, v1 (xcart_4_6_0), 2013-02-25 10:54:15, order_message_widget.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_invoice}</h1>

{if $this_is_printable_version eq ""}

  {capture name=dialog}

    <p class="text-block">{$lng.txt_order_placed}</p>
    {$lng.txt_order_placed_msg}

  {/capture}
  {include file="customer/dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog}

{/if}

<div id="amazonOrderDetail" class="halign-center"></div>
<script>
new CBA.Widgets.OrderDetailsWidget ({ldelim}
merchantId: '{$config.Amazon_Checkout.amazon_mid}',
orderID: '{$amazon_orderid}'
{rdelim}).render ("amazonOrderDetail");
</script>
