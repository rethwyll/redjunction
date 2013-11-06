{*
0a8117304b23012e710f997eda2e2568d16cc924, v2 (xcart_4_6_1), 2013-07-02 17:56:19, customer_review.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

{if $review}
<div class="acr-review{if $additional_class ne ""} {$additional_class}{/if}">
  <a name="review{$review.review_id}"></a>
  <div class="acr-author">
    <b>{$review.author|default:$lng.lbl_acr_guest}</b>
    {if $current_page eq 'all_reviews' && $review.productid ne 0}
      {$lng.lbl_acr_review_about}&nbsp;&nbsp;<span class="item"><a class="url fn" href="product.php?productid={$review.productid}">{$review.product|amp}</a></span>:<br />
    {/if}
    
  </div>

  {if $review.datetime gt 0}
  <div class="acr-date">{$review.datetime|date_format:"%b %e, %Y"}</div>
  {/if}
  <div class="clearing"></div>

  {if $review.is_verified eq 'Y'}
  <div class="acr-verified">
  {$lng.lbl_acr_customer_purchased_item}
  </div>
  {/if}

  {if $review.rating gt 0}
  <div class="acr-rating">
  {include file="modules/Advanced_Customer_Reviews/vote_bar.tpl" rating_value=$review.rating is_average="N"}
  <div class="clearing"></div>
  </div>
  {/if}

  <div class="acr-message">
  {$review.message|nl2br}
  {if $config.Advanced_Customer_Reviews.acr_use_advantages_block eq 'Y'}
  {if $review.advantages}
  <br /><br />
  <b>{$lng.lbl_acr_advantages}:</b> {$review.advantages|nl2br}
  {/if}
  {if $review.disadvantages}
  <br /><br />
  <b>{$lng.lbl_acr_disadvantages}:</b> {$review.disadvantages|nl2br}
  {/if}
  {/if}
  </div>

  {if $config.Advanced_Customer_Reviews.acr_display_useful_box eq 'Y' && $printable neq 'Y'}
    {include file="modules/Advanced_Customer_Reviews/useful_box.tpl" productid=$review.productid}
  {/if}
</div>

{/if}
