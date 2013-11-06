{*
2aca87f302048436ed08b4e6738089849840409f, v1 (xcart_4_5_3), 2012-08-07 09:50:06, new_arrivals_link.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

{if $config.New_Arrivals.new_arrivals_show_in_special eq "Y"}
  <li><a href="new_arrivals.php">{$lng.lbl_new_arrivals}</a></li>
{/if}
