{*
2aca87f302048436ed08b4e6738089849840409f, v1 (xcart_4_5_3), 2012-08-07 09:50:06, on_sale_link.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

{if $config.On_Sale.on_sale_show_in_special eq "Y"}
  <li><a href="on_sale.php">{$lng.lbl_on_sale}</a></li>
{/if}
