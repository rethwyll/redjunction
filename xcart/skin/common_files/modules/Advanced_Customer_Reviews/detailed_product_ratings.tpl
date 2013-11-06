{*
31c916a86f77bd691d94210afd54135cd7103170, v5 (xcart_4_5_3), 2012-10-02 07:29:56, detailed_product_ratings.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<div>

{if $acr_show_comment && $rating.rating_level gt 0}
  {assign var=m value=$rating.rating_level}
  {assign var=n value=$stars.length}
  {assign var="votes_count" value=$rating.votes_total}
  {assign var="reviews_count" value=$rating.reviews_total}

  <b>{$lng.lbl_acr_average_customer_rating}</b>: {$lng.lbl_acr_m_out_of_n|substitute:m:$m|substitute:n:$n}

  {if $rating.votes_total gt 0}
  <br />{$lng.lbl_acr_rating_based_on_n_votes_n_reviews|substitute:votes_count:$votes_count|substitute:reviews_count:$reviews_count}
  {else}
  <br />
  {$lng.lbl_acr_rating_based_on_n_reviews|substitute:reviews_count:$reviews_count}
  {/if}
  <br />
{/if}

{if $detailed_ratings}
<br />
<table class="acr-detailed-product-ratings">
{section loop=$detailed_ratings name=current_rating start=$stars.length step=-1} 
  {assign var=rating value=$detailed_ratings[current_rating]}
  <tr{if $rating.total lte 0} class="no-reviews"{else}{if $rating.to eq $search_prefilled.rating} class="selected"{/if}{/if}>
    <td nowrap="nowrap" width="15%">
      {if $rating.total gt 0 && $rating.to && $search_prefilled && !isset($search_prefilled.rating[$rating.to])}
      <a href="{$reviews_page_url|amp}&amp;rating={$rating.to}">{$lng.lbl_acr_n_stars|substitute:count:$rating.stars}</a>:&nbsp;
      {else}
        {$lng.lbl_acr_n_stars|substitute:count:$rating.stars}:&nbsp;
      {/if}
    </td>
    <td class="acr-rating-bar">
      <div class="acr-rating-bar-full" style="width: {$rating.percent}%;"></div>
    </td>
    <td class="acr-comment" nowrap="nowrap">&nbsp;{$lng.lbl_acr_count_reviews|substitute:count:$rating.total}
    </td>
  </tr>
{/section}
</table>
{/if}

</div>
