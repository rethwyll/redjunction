{*
ee7464d91a69f6b17f6d573ffc718b040c494ae5, v3 (xcart_4_6_0), 2013-05-24 17:09:12, new_arrivals_product_list_date.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.New_Arrivals.show_date_col_on_product_list eq "Y"}
  {if $is_header eq "Y"}
    <td nowrap="nowrap">{if $search_prefilled.sort_field eq "add_date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$url_to|amp}&amp;sort=add_date&amp;sort_direction={if $search_prefilled.sort_field eq "add_date"}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng.lbl_new_arrivals_date_added}</a></td>
  {else}
    <td>{$product.add_date|date_format:$config.Appearance.date_format}</td>
  {/if}
{/if}
