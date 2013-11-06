{*
ffdeb556053932ad55da6117f4ff8ba45b4b46d7, v5 (xcart_4_6_1), 2013-08-01 17:20:15, on_sale_icon.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{strip}

{if $href eq ""}
  {assign var="href" value="product.php?productid=`$product.productid`"}
{/if}

{if $usertype eq "C" && $product.on_sale eq "Y"}

  {capture name="on_sale_wrapper"}
    <div class="on_sale_wrapper">
  {/capture}

  {capture name="on_sale_icon"}
      {if 
        $active_modules.Special_Offers 
        && $product.have_offers
        && $config.Special_Offers.offers_show_thumb_on_lists eq "Y"
      }
        <div class="on-sale-icon-with-so">
      {else}
        <div class="on-sale-icon">
      {/if}

        <a href="{$href}"><img src="{$SkinDir}/modules/On_Sale/images/on_sale.png" alt="" /></a>
      </div>
    </div>
  {/capture}

{else}

  {capture name="on_sale_wrapper"}
  {/capture}

  {capture name="on_sale_icon"}
  {/capture}

{/if}

{if $module eq "cart"}

  {if $config.On_Sale.on_sale_on_cart_page eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  <a href="{$href}">
    {include file="product_thumbnail.tpl" productid=$product.display_imageid product=$product.product tmbn_url=$product.pimage_url type=$product.is_pimage image_x=$product.tmbn_x}
  </a>

  {if $config.On_Sale.on_sale_on_cart_page eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "product"}

  {if $config.On_Sale.on_sale_on_product_page eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  {include file="product_thumbnail.tpl" productid=$product.image_id image_x=$product.image_x image_y=$product.image_y product=$product.product tmbn_url=$product.image_url id="product_thumbnail" type=$product.image_type}

  {if $config.On_Sale.on_sale_on_product_page eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "products_list"}

  {if $usertype eq "C" && $product.on_sale eq "Y"
    && (
      ($main eq "catalog" && ($cat le "0" || $cat eq "") && $config.On_Sale.on_sale_on_home_page eq "Y")
      || ($main eq "catalog" && $cat gt "0" && $config.On_Sale.on_sale_on_product_list eq "Y")
      || ($main eq "on_sale" && $config.On_Sale.on_sale_on_sale_page eq "Y")
      || (($main eq "search" || $main eq "advanced_search") && $config.On_Sale.on_sale_on_search_page eq "Y")
      || ($main eq "pmap_customer" && $config.On_Sale.on_sale_on_pmap_page eq "Y")
    )}
    {assign var=is_on_sale_product value="Y"}
  {else}
    {assign var=is_on_sale_product value="N"}
  {/if}

  {if $is_on_sale_product eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  {if $current_skin eq "fashion_mosaic"}
    <a href="{$href}" class="image-link">
  {else}
    <a href="{$href}">
  {/if}

  {include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.tmbn_x image_y=$product.tmbn_y product=$product.product tmbn_url=$product.tmbn_url}

  </a>

  {if $is_on_sale_product eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "products_t"}

  {if $usertype eq "C" && $product.on_sale eq "Y"
    && (
      ($main eq "catalog" && ($cat le "0" || $cat eq "") && $config.On_Sale.on_sale_on_home_page eq "Y")
      || ($main eq "catalog" && $cat gt "0" && $config.On_Sale.on_sale_on_product_list eq "Y")
      || ($main eq "on_sale" && $config.On_Sale.on_sale_on_sale_page eq "Y")
      || (($main eq "search" || $main eq "advanced_search") && $config.On_Sale.on_sale_on_search_page eq "Y")
      || ($main eq "pmap_customer" && $config.On_Sale.on_sale_on_pmap_page eq "Y")
    )}
    {assign var=is_on_sale_product value="Y"}
  {else}
    {assign var=is_on_sale_product value="N"}
  {/if}

  {if $is_on_sale_product eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  <a href="{$href}">
    {include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.tmbn_x image_y=$product.tmbn_y product=$product.product tmbn_url=$product.tmbn_url}
  </a>

  {if $is_on_sale_product eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "bestsellers"}

  {if $config.On_Sale.on_sale_on_bestsellers eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  <a href="{$href}">
    {include file="product_thumbnail.tpl" tmbn_url=$product.tmbn_url productid=$product.productid image_x=$product.tmbn_x class="image" product=$product.product}
  </a>

  {if $config.On_Sale.on_sale_on_bestsellers eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "recently_viewed"}

  {if $config.On_Sale.on_sale_on_recently_viewed eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  <a href="{$href}">
    {include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.image_x product=$product.product tmbn_url=$product.tmbn_url}
  </a>

  {if $config.On_Sale.on_sale_on_recently_viewed eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "simple_products_list"}

  {if $config.On_Sale.on_sale_on_product_tabs eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  <a href="{$href}" {if $current_skin eq "books_and_magazines"}id="img_{$product.productid}" rel="#img_{$product.productid}_tooltip"{/if}>
    {include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.tmbn_x image_y=$product.tmbn_y product=$product.product tmbn_url=$product.tmbn_url}
  </a>

  {if $config.On_Sale.on_sale_on_product_tabs eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "wl_products"}

  {if $config.On_Sale.on_sale_on_wishlist_page eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  <a href="{$href}">
    {include file="product_thumbnail.tpl" productid=$product.display_imageid image_x=$product.tmbn_x product=$product.product tmbn_url=$product.pimage_url type=$product.is_pimage}
  </a>

  {if $config.On_Sale.on_sale_on_wishlist_page eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{elseif $module eq "wl_carousel"}

  {if $config.On_Sale.on_sale_on_wishlist_carousel eq "Y"}
    {$smarty.capture.on_sale_wrapper}
  {/if}

  <a href="{$href}">
    {include file="product_thumbnail.tpl" productid=$product.productid image_x=$product.tmbn_x image_y=$product.tmbn_y product=$product.product tmbn_url=$product.tmbn_url}
  </a>

  {if $config.On_Sale.on_sale_on_wishlist_carousel eq "Y"}
    {$smarty.capture.on_sale_icon}
  {/if}

{/if}

{/strip}
