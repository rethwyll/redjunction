{*
1f6152e37b8537396ee173ec0e4d1364bfc40fb9, v2 (xcart_4_6_0), 2013-05-20 17:39:12, on_sale.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $on_sale_products ne "" && $usertype eq "C" && ($is_home_page neq "Y" || ($is_home_page eq "Y" && $config.On_Sale.on_sale_home eq "Y"))}
{capture name=dialog}
  {if $navigation eq "Y"}
    {include file="customer/main/navigation.tpl" navigation_script=$on_sale_navigation_script}
  {/if}

  {include file="customer/main/products.tpl" products=$on_sale_products is_on_sale_products="Y"}

  {if $navigation eq "Y"}
    {include file="customer/main/navigation.tpl" navigation_script=$on_sale_navigation_script}
  {/if}
{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_on_sale content=$smarty.capture.dialog sort=false}
{/if}
