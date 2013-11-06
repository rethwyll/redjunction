{*
a00efb58ff6921e52f88ed94a38df9c66709025b, v3 (xcart_4_5_3), 2012-09-19 13:33:26, acr_product_details.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var="general_rating" value=$product.general_rating}
{assign var="productid" value=$product.productid}
{include file="modules/Advanced_Customer_Reviews/general_product_rating.tpl" use_rich_snippet=true}
{if $break_line eq "Y"}
<br />
{/if}
