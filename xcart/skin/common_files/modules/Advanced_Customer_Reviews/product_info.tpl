{*
a00efb58ff6921e52f88ed94a38df9c66709025b, v3 (xcart_4_5_3), 2012-09-19 13:33:26, product_info.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $product.general_rating.rating_level gt 0}
  {assign var="use_rich_snippet" value=true}
{/if}
<div class="acr-product-info">
  <h1>{$product.product}</h1>

  <div class="acr-image"{if $max_image_width gt 0} style="width: {$max_image_width}px;"{/if}>
    <a href="product.php?productid={$product.productid}">{include file="product_thumbnail.tpl" productid=$product.productid image_x=$config.Appearance.thumbnail_width product=$product.product tmbn_url=$product.image_url}</a>
  </div>

  <div class="acr-details"{if $config.Appearance.thumbnail_width gt 0 or $product.tmbn_x gt 0} style="margin-left: {$max_images_width|default:$config.Appearance.thumbnail_width|default:$product.tmbn_x}px;"{/if}>
    {include file="modules/Advanced_Customer_Reviews/general_product_rating.tpl" general_rating=$product.general_rating productid=$product.productid show_detailed_ratings='Y' use_rich_snippet=true}
    {include file="modules/Advanced_Customer_Reviews/detailed_product_ratings.tpl" detailed_ratings=$product.detailed_ratings productid=$product.productid}

    <br />
    {include file="modules/Advanced_Customer_Reviews/button_add_review.tpl" productid=$product.productid}
  </div>

  <div class="clearing"></div>
  <br /><br />

  {if $search_prefilled.rating gt 0}
  {$lng.lbl_acr_viewing_n_stars_reviews|substitute:count:$search_prefilled.stars} (<a href="{$reviews_page_url|amp}">{$lng.lbl_acr_see_all_reviews}</a>)
  <br /><br />
  {/if}

</div>
