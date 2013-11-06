{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, acr_product_modify.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $section eq "acr_reviews"}
  <a name="section_acr_reviews"></a>
  {if $submode eq 'review_modify'}
    {include file="modules/Advanced_Customer_Reviews/admin_review.tpl" current_page="product_reviews"}
  {else}
    {include file="modules/Advanced_Customer_Reviews/admin_reviews_list.tpl" current_page="product_reviews"}
  {/if}
  <br />
{/if}
