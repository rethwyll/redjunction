{*
8ec8b858917c921a726e2197071ce74d020e5ead, v1 (xcart_4_6_0), 2013-04-02 16:43:59, quick_reorder_link.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $show_quick_reorder_link eq "Y"}
  {if $current_skin eq "books_and_magazines"}
    <td><a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a></td>
  {elseif $current_skin eq "fashion_mosaic"}
    &nbsp;&nbsp;<a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a>
  {elseif $current_skin eq "ideal_comfort" or $current_skin eq "ideal_responsive"}
    <a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a>
  {elseif $current_skin eq "vivid_dreams"}
    <li><a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a></li>
  {else}
    |
    <a href="quick_reorder.php">{$lng.lbl_quick_reorder_customer}</a>
  {/if}
{/if}
