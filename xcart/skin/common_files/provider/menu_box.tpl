{*
d6aa158b4360cf922cc315fb2472fe00cfc9e46c, v5 (xcart_4_5_3), 2012-08-22 07:52:08, menu_box.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

<ul id="horizontal-menu">

<li>
<a href="home.php">{$lng.lbl_dashboard}</a>
</li>

<li>

<a href="{$catalogs.provider}/orders.php?mode=new">{$lng.lbl_orders}</a>

<div>
<a href="{$catalogs.provider}/orders.php?mode=new">{$lng.lbl_this_month_orders}</a>
<a href="{$catalogs.provider}/orders.php">{$lng.lbl_search_orders_menu}</a>
<a href="{$catalogs.provider}/commissions.php">{$lng.lbl_provider_commissions}</a>
</div>
</li>

<li>

<a href='{$catalogs.provider}/search.php'>{$lng.lbl_catalog}</a>

<div>
<a href="{$catalogs.provider}/product_modify.php">{$lng.lbl_add_new_product}</a>
<a href="{$catalogs.provider}/search.php">{$lng.lbl_products}</a>
{if $active_modules.Extra_Fields ne ""}
<a href="{$catalogs.provider}/extra_fields.php">{$lng.lbl_extra_fields}</a>
{/if}
{if $active_modules.Manufacturers}
<a href="{$catalogs.provider}/manufacturers.php">{$lng.lbl_manufacturers}</a>
{/if}
{if $active_modules.Special_Offers ne ""}
{include file="modules/Special_Offers/menu_provider.tpl"}
{/if}
{if $active_modules.Product_Notifications ne ""}
<a href="{$catalogs.provider}/product_notifications.php">{$lng.lbl_prod_notif_adm}</a>
{/if}
<a href="{$catalogs.provider}/discounts.php">{$lng.lbl_discounts}</a>
{if $active_modules.Discount_Coupons ne ""}
<a href="{$catalogs.provider}/coupons.php">{$lng.lbl_coupons}</a>
{/if}
{if $active_modules.Product_Configurator ne ""}
{include file="modules/Product_Configurator/pconf_menu_provider.tpl"}
{/if}
{if $active_modules.Feature_Comparison ne ""}
{include file="modules/Feature_Comparison/admin_menu.tpl"}
{/if}
{if $active_modules.Advanced_Customer_Reviews ne ""}
<a href="{$catalogs.provider}/reviews.php">{$lng.lbl_customer_reviews}</a>
{/if}
</div>
</li>

<li>

{if $config.Shipping.enable_shipping eq "Y"}
  <a href="{$catalogs.provider}/shipping_rates.php">{$lng.lbl_shipping_and_taxes}</a>
{else}
  <a href="{$catalogs.provider}/taxes.php">{$lng.lbl_shipping_and_taxes}</a>
{/if}

<div>
<a href="{$catalogs.provider}/zones.php">{$lng.lbl_destination_zones}</a>
<a href="{$catalogs.provider}/taxes.php">{$lng.lbl_tax_rates}</a>
{if $config.Shipping.enable_shipping eq "Y"}
<a href="{$catalogs.provider}/shipping_rates.php">{$lng.lbl_shipping_charges}</a>
{if $config.Shipping.realtime_shipping eq "Y"}
<a href="{$catalogs.provider}/shipping_rates.php?type=R">{$lng.lbl_shipping_markups}</a>
{/if}
{/if}
</div>
</li>

<li>

<a href="{$catalogs.provider}/general.php">{$lng.lbl_tools}</a>

<div>
<a href="{$catalogs.provider}/general.php">{$lng.lbl_summary}</a>
<a href="{$catalogs.provider}/file_manage.php">{$lng.lbl_files}</a>
<a href="{$catalogs.provider}/import.php">{$lng.lbl_import_export}</a>
<a href="{$catalogs.provider}/inv_update.php">{$lng.lbl_update_inventory}</a>
</div>
</li>

{include file="admin/help.tpl"}

{include file="admin/goodies.tpl"}

</ul>
