{*
2aca87f302048436ed08b4e6738089849840409f, v1 (xcart_4_5_3), 2012-08-07 09:50:06, quick_reorder.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $products}
  {capture name=dialog}
    {include file="customer/main/navigation.tpl"}
    {include file="customer/main/products.tpl" products=$products}
    {include file="customer/main/navigation.tpl"}
  {/capture}

  {include file="customer/dialog.tpl" title=$lng.lbl_previously_ordered_products content=$smarty.capture.dialog products_sort_url="quick_reorder.php?cat=0&amp;" sort=true additional_class="products-dialog dialog-category-products-list"}

{else}
  {$lng.txt_quick_reorder_no_products}
{/if}
