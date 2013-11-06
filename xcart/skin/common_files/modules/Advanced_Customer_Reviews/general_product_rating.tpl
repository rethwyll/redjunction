{*
a00efb58ff6921e52f88ed94a38df9c66709025b, v3 (xcart_4_5_3), 2012-09-19 13:33:26, general_product_rating.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $general_rating}
<div class="acr-general-product-rating">

{if $use_rich_snippet && $general_rating.rating_level gt 0}
  {if !$active_modules.Rich_Google_Search_Results}
  <div itemscope itemtype="http://schema.org/Product">
  <meta itemprop="name" content="{$product.product}" />
  {/if}

    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
      <meta itemprop="ratingValue" content="{$general_rating.rating_level}" />
      <meta itemprop="bestRating" content="{$stars.length}" />
      {if $general_rating.reviews_total gt 0}
      <meta itemprop="reviewCount" content="{$general_rating.reviews_total}" />
      {/if}
      {if $general_rating.votes_total gt 0 && $general_rating.reviews_total eq 0}
      <meta itemprop="ratingCount" content="{$general_rating.votes_total}" />
      {/if}
    </div>

  {if !$active_modules.Rich_Google_Search_Results}
  </div>
  {/if}
{/if}

{if $general_rating.total gt 0 && $show_detailed_ratings neq 'Y'}
  {assign var=use_dropdown_button value='Y'}
  {assign var=colspan value=3}
{else}
  {assign var=use_dropdown_button value='N'}
  {assign var=colspan value=2}
{/if}

<table class="acr-container{if $is_multicolumns eq "Y"}-multicolumns{/if}">
  <tr>
    <td class="left-indent">&nbsp;</td>

    <td class="rating-box">
      <div style="width: {$smarty.const.ACR_STARS_RATING_WIDTH}px">{include file="modules/Advanced_Customer_Reviews/vote_bar.tpl" rating=$general_rating}</div>
    </td>

    {if $use_dropdown_button eq 'Y'}
    <td class="dropdown-button" title="{$lng.lbl_acr_click_dropdown}">
      <a href="{$xcart_web_dir}/get_block.php?block=acr_get_product_ratings&amp;productid={$productid}"><img src="{$ImagesDir}/acr_reviews_dropout_down.png" alt="" /></a>
    </td>
    {/if}

    <td class="comment" nowrap="nowrap">
      (<a href="reviews.php?productid={$productid}{if $cat}&amp;cat={$cat}{/if}">{$general_rating.total} {$lng.lbl_acr_reviews}</a>)
    </td>

    <td class="right-indent">&nbsp;</td>
  </tr>

  <tr>
    <td class="left-indent"></td>
    <td colspan="{$colspan}"><div class="acr-static-popup-container"></div></td>
    <td class="right-indent"></td>
  </tr>

</table>
</div>
{load_defer file="modules/Advanced_Customer_Reviews/func.js" type="js"}
{/if}
