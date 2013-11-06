{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, acr_products_list.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var="general_rating" value=$product.general_rating}
{assign var="productid" value=$product.productid}
{include file="modules/Advanced_Customer_Reviews/general_product_rating.tpl"}
{if $break_line eq "Y"}
<br />
{/if}
