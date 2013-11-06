{*
0cb748cfe54aa44f575e07f094db0d91f16aa400, v2 (xcart_4_5_3), 2012-09-18 12:55:10, customer_reviews_list.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $printable ne 'Y'}
  {if $current_page eq "product_reviews"}
    {include file="modules/Advanced_Customer_Reviews/product_info.tpl" productid=$product.productid}
  {/if}

  {capture name=dialog}

  {if $total_pages gt 2}
    {assign var="navpage" value=$navigation_page}
  {/if}

  {if $current_page eq 'product_details' && $reviews}
    <div class="acr-product-tab-summary">
      <b>{$lng.lbl_acr_average_customer_rating}</b>:
      {include file="modules/Advanced_Customer_Reviews/general_product_rating.tpl" general_rating=$product.general_rating productid=$product.productid}
      <div class="clearing"></div><br />
      {include file="modules/Advanced_Customer_Reviews/button_add_review.tpl" productid=$product.productid}
      <div class="clearing"></div>
      <br />
  
      <span class="acr-reviews-order">
      {count assign="count_reviews" value=$reviews print=false}
      {$count_reviews} {if $config.Advanced_Customer_Reviews.acr_customer_reviews_order eq 'useful'}{$lng.lbl_acr_most_useful_reviews}{else}{$lng.lbl_acr_newest_reviews}{/if} (<a href="reviews.php?productid={$product.productid}">{$lng.lbl_acr_see_all_reviews}</a>):
      </span>
    </div>

  {else}

    {if $mode eq "search" && $reviews}

      {if $total_items gt "1"}
        <div class="acr-search-results">
        {$lng.txt_N_results_found|substitute:"items":$total_items}<br />
        {$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
        </div>
      {elseif $total_items eq "0"}
        {$lng.txt_N_results_found|substitute:"items":0}
      {/if}


    {include file="customer/main/navigation.tpl"}

    {/if}

  {/if}

  <div class="acr-reviews-list{if $current_page eq 'product_details'} acr-tab{/if}">

  {if $reviews}
    {foreach from=$reviews item=review}
    {include file="modules/Advanced_Customer_Reviews/customer_review.tpl"}
    <div class="acr-line"></div>
    {/foreach}
  {else}
    {if $productid}
    {$lng.txt_no_customer_reviews}
    {else}
    {$lng.txt_acr_no_customer_reviews}
    {/if}
    <br /><br />
  {/if}

  </div>

  {include file="customer/main/navigation.tpl"}
  {if $total_pages gt 1}
  <br />
  {/if}

  {if $current_page eq 'product_details' || $current_page eq 'product_reviews'}
  {include file="modules/Advanced_Customer_Reviews/button_add_review.tpl" productid=$product.productid}
  <div class="clearing"></div>
  {/if}

  {load_defer file="modules/Advanced_Customer_Reviews/func.js" type="js"}

  {/capture}

  {if $nodialog}
    {$smarty.capture.dialog}
  {else}
    {assign var=sort value=true}
    {assign var=products_sort_url value=$url}
    {include file="customer/dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_customers_feedback additional_class="acr-dialog" extra='width="100%"' title_page='category'}
  {/if}

{/if}
