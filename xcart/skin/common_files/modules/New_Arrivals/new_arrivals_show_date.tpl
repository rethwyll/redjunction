{*
1f6152e37b8537396ee173ec0e4d1364bfc40fb9, v2 (xcart_4_6_0), 2013-05-20 17:39:12, new_arrivals_show_date.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $new_arrivals_show_date eq 'Y' && $config.New_Arrivals.show_date_row_in_customer_area eq "Y"}
  <div>{$lng.lbl_added}:&nbsp;{$product.add_date|date_format:$config.Appearance.date_format}</div>
{/if}
