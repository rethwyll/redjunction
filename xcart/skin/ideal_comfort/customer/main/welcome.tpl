{*
3feabb2abeed6f3359a2afda653a80a201516b14, v7 (xcart_4_6_1), 2013-06-19 13:04:33, welcome.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="0" class="welcome-table" summary="{$lng.lbl_special|escape}">
<tr>
	<td class="welcome-cell">
    {include file="customer/main/home_page_banner.tpl"}
 		{$lng.txt_welcome}<br />

    {if $active_modules.Bestsellers}
      {getvar var=bestsellers func=func_tpl_get_bestsellers}
    {/if}

		{if $active_modules.Bestsellers and $config.Bestsellers.bestsellers_menu ne "Y"}
		  {include file="modules/Bestsellers/bestsellers.tpl"}<br />
		{/if}
		{if $active_modules.Bestsellers && $bestsellers}
			{assign var=row_length value=2}
		{else}
			{assign var=row_length value=false}
		{/if}
    {if $active_modules.New_Arrivals}
      {include file="modules/New_Arrivals/new_arrivals.tpl" is_home_page="Y"}
    {/if}
    {if $active_modules.On_Sale}
      {include file="modules/On_Sale/on_sale.tpl" is_home_page="Y"}
    {/if}
		{include file="customer/main/featured.tpl" row_length=$row_length}
	</td>
	{if $active_modules.Bestsellers && $bestsellers}
	<td class="bestsellers-cell">
		{include file="modules/Bestsellers/menu_bestsellers.tpl"}
	</td>
	{/if}
</tr>
</table>
