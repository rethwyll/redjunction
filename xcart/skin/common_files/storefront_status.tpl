{*
392e2a18aed0c6e6352cea30a1dd3e354af680c6, v1 (xcart_4_5_5), 2013-02-11 14:24:34, storefront_status.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $login}
  {if not $no_container}
    <div class="storefront-status">
  {/if}
  {if $config.General.shop_closed eq "Y"}
    <div class="closed-store">{$lng.lbl_close_storefront|substitute:'STOREFRONT':$http_location:'SHOPKEY':$config.General.shop_closed_key}{if $need_storefront_link} [ <a href="{$storefront_link|amp}">{$lng.lbl_open}</a> ]{/if}</div>
  {else}
    <div class="open-store">{$lng.lbl_open_storefront|substitute:'STOREFRONT':$http_location}{if $need_storefront_link} [ <a href="javascript:void(0);" onclick="javascript:if(confirm('{$lng.lbl_open_storefront_warning|wm_remove|escape:'javascript'}'))window.location='{$storefront_link|amp}';">{$lng.lbl_close}</a> ]{/if}</div>
  {/if}
  {if not $no_container}
    </div>
  {/if}
{/if}
