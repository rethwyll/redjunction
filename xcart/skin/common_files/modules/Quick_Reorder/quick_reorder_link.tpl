{*
2aca87f302048436ed08b4e6738089849840409f, v1 (xcart_4_5_3), 2012-08-07 09:50:06, quick_reorder_link.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

{if $show_quick_reorder_link eq "Y"}
  {if $current_skin eq "books_and_magazines"}
    <td><a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a></td>
  {elseif  $current_skin eq "fashion_mosaic"}
    &nbsp;&nbsp;<a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a>
  {elseif  $current_skin eq "ideal_comfort"}
    <a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a>
  {elseif  $current_skin eq "vivid_dreams"}
    <li><a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a></li>
  {else}
    |
    <a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a>
  {/if}
{/if}
