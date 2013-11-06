{*
2aca87f302048436ed08b4e6738089849840409f, v10 (xcart_4_5_3), 2012-08-07 09:50:06, special.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{capture name=submenu}

  {if $active_modules.Manufacturers ne "" and $config.Manufacturers.manufacturers_menu ne "Y"}
    <li><a href="manufacturers.php">{$lng.lbl_manufacturers}</a></li>
  {/if}

  {if $active_modules.Gift_Certificates ne ""}
    {include file="modules/Gift_Certificates/gc_menu.tpl"}
  {/if}

  {if $active_modules.Feature_Comparison ne ""}
    {include file="modules/Feature_Comparison/customer_menu.tpl"}
  {/if}

  {if $active_modules.Survey ne ""}
    {include file="modules/Survey/menu_special.tpl"}
  {/if}

  {if $active_modules.Special_Offers ne ""}
    {include file="modules/Special_Offers/menu_special.tpl"}
  {/if}

  {if $active_modules.Wishlist and $active_modules.Gift_Registry}
    <li><a href="giftreg_manage.php">{$lng.lbl_gift_registry}</a></li>
  {/if}

  {if $active_modules.Wishlist and $wlid ne ""}
    <li><a href="cart.php?mode=friend_wl&amp;wlid={$wlid|escape}">{$lng.lbl_friends_wish_list}</a></li>
  {/if}

  {if $active_modules.Gift_Registry or $active_modules.RMA or $active_modules.Special_Offers}
    <li class="separator">&nbsp;</li>
  {/if}

  {if $active_modules.Gift_Registry ne ""}
    {include file="modules/Gift_Registry/giftreg_menu.tpl"}
  {/if}

  {if $active_modules.RMA}
    {include file="modules/RMA/customer/menu.tpl"}
  {/if}

  {if $active_modules.Special_Offers ne ""}
    {include file="modules/Special_Offers/menu_cart.tpl"}
  {/if}

  {if $active_modules.Sitemap ne ""}
    {include file="modules/Sitemap/menu_item.tpl"}
  {/if}

  {if $active_modules.Products_Map ne ""}
    {include file="modules/Products_Map/menu_item.tpl"}
  {/if}

  {if $active_modules.New_Arrivals ne ""}
    {include file="modules/New_Arrivals/new_arrivals_link.tpl"}
  {/if}

  {if $active_modules.On_Sale ne ""}
    {include file="modules/On_Sale/on_sale_link.tpl"}
  {/if}

  {if $active_modules.Quick_Reorder ne ""}
    {include file="modules/Quick_Reorder/quick_reorder_link.tpl" current_skin="vivid_dreams"}
  {/if}

  {if $active_modules.EU_Cookie_Law ne ""}
    {include file="modules/EU_Cookie_Law/menu_item_special.tpl"}
  {/if}

{/capture}
{if $smarty.capture.submenu|trim}
  <a name="special"></a>
  {capture name=menu}
    <ul>
      {$smarty.capture.submenu|trim}
    </ul>
  {/capture}
  {include file="customer/menu_dialog.tpl" title=$lng.lbl_special content=$smarty.capture.menu additional_class="menu-special"}
{/if}
