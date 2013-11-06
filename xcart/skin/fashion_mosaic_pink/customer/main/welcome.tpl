{*
4f550a0b753878e34fc3d4947ade1e38ff1cb35d, v9 (xcart_4_6_0), 2013-03-27 13:55:55, welcome.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="welcome-top">

  {include file="customer/main/home_page_banner.tpl"}

  {if $categories_menu_list or $fancy_use_cache}
    {include file="customer/categories.tpl"}
  {else}
    <img src="{$ImagesDir}/spacer.gif" alt="" class="empty-height-extender" />
  {/if}

</div>

{if $active_modules.Bestsellers and $config.Bestsellers.bestsellers_menu ne "Y"}
  {include file="modules/Bestsellers/bestsellers.tpl"}<br />
{/if}

{if $active_modules.New_Arrivals}
  {include file="modules/New_Arrivals/new_arrivals.tpl" is_home_page="Y"}
{/if}
 
{if $active_modules.On_Sale}
  {include file="modules/On_Sale/on_sale.tpl" is_home_page="Y"}
{/if}

{include file="customer/main/featured.tpl"}

<img src="{$ImagesDir}/spacer.gif" class="menu-columns" alt="" />

<table cellspacing="0" class="menu-columns" summary="{$lng.lbl_special|escape}">
  <tr>

    <td>
      {if $active_modules.Feature_Comparison and $comparison_products ne ''}
        {include file="modules/Feature_Comparison/product_list.tpl"}
      {/if}
      {if $active_modules.Bestsellers}
        {include file="modules/Bestsellers/menu_bestsellers.tpl"}
      {/if}
      {if $active_modules.XAffiliate and $config.XAffiliate.partner_register eq 'Y'}
        {include file="partner/menu_affiliate.tpl"}
      {/if}
      {if not $active_modules.Simple_Mode and $config.General.provider_register eq 'Y' and $config.General.provider_display_backoffice_link eq 'Y'}
        {include file="customer/menu_provider.tpl"}
      {/if}
    </td>

    <td>
      {include file="customer/special.tpl"}
      {include file="customer/help/menu.tpl"}
    </td>

    <td>
   {if $active_modules.Adv_Mailchimp_Subscription}
       {include file="modules/Adv_Mailchimp_Subscription/customer/mailchimp_news.tpl"}
   {else}
       {include file="customer/news.tpl"}
   {/if}
      {if $active_modules.Interneka}
        {include file="modules/Interneka/menu_interneka.tpl"}
      {/if}
    </td>

    <td class="contact-us">
      <a href="help.php?section=contactus&amp;mode=update" class="contact-us"><img src="{$ImagesDir}/spacer.gif" alt="" /></a>
    </td>

  </tr>
</table>

