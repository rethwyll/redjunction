{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, search_sort_by_admin.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $search_prefilled.sort_field eq $sort_field}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}
<a href="{$url_to|amp}&amp;sort={$sort_field}&amp;sort_direction={if $search_prefilled.sort_field eq $sort_field}{if $search_prefilled.sort_direction eq 1}0{else}1{/if}{else}{$search_prefilled.sort_direction}{/if}">{$lng_label}</a>
