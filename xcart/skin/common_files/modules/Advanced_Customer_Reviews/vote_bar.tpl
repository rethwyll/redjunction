{*
a00efb58ff6921e52f88ed94a38df9c66709025b, v2 (xcart_4_5_3), 2012-09-19 13:33:26, vote_bar.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $rating_value}
  {assign var=rating_level value=$rating_value/$stars.cost}
  {assign var=full_stars value=$rating_level}
{else}
  {assign var=rating_level value=$rating.rating_level}
  {assign var=full_stars value=$rating.full_stars}
{/if}

{if $rating.allow_add_rate eq "Y" && $allow_add_rate neq "N"}
  {assign var="allow_add_rate" value="Y"}
{else}
  {assign var="allow_add_rate" value="N"}
{/if}
  
<div class="acr-rating-box rating">
  {if $allow_add_rate eq "Y"}
  <script type="text/javascript" src="{$SkinDir}/modules/Advanced_Customer_Reviews/vote_bar.js"></script>
  <script type="text/javascript">
  <!--
    var stars_cost = {$stars.cost|default:1};
  -->
  </script>
  {/if}

  <div class="acr-vote-bar{if $allow_add_rate eq "Y"} acr-allow-add-rate{/if}" title="{if $rating_level gt 0 && $allow_add_rate eq "N"}{if $is_average eq "N"}{$lng.lbl_acr_m_out_of_n|substitute:m:$rating_level|substitute:n:$stars.length}{else}{$lng.lbl_acr_average_customer_rating}: {$lng.lbl_acr_m_out_of_n|substitute:m:$rating_level|substitute:n:$stars.length} {if $rating.votes_total gt 0}{$lng.lbl_acr_rating_based_on_n_votes_n_reviews|substitute:votes_count:$rating.votes_total|substitute:reviews_count:$rating.reviews_total}{else}{$lng.lbl_acr_rating_based_on_n_reviews|substitute:reviews_count:$rating.reviews_total}{/if}{/if}{/if}">

  {section loop=`$stars.length` name=vote_subbar}
  {if $allow_add_rate eq "Y"}
  <a href="javascript:void(0);" title="{$stars.titles[$smarty.section.vote_subbar.index]|escape}"{if $rating_level gte $smarty.section.vote_subbar.iteration} class="full"{/if} id="star-{$smarty.section.vote_subbar.iteration}">
    {if $config.UA.browser eq 'MSIE' and $config.UA.version lt 7}
    <span class="bg"></span>
    {/if}
  </a>

  {else}

  <span{if $full_stars gt $smarty.section.vote_subbar.index} class="full"{/if}>
    {if $full_stars eq $smarty.section.vote_subbar.index and $rating.percent gt 0}
    <img src="{$ImagesDir}/spacer.gif" alt="" style="width: {$rating.percent}%;" />
    {/if}
  </span>
  {/if}

  {/section}

  </div>

</div>
