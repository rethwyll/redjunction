{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, acr_review_reminder.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/salutation.tpl" title=$userinfo.title firstname=$userinfo.firstname lastname=$userinfo.lastname}


{$lng.txt_acr_add_products_reviews|substitute:"company_name":$config.Company.company_name}

{section name=prod_num loop=$products}
 - {$lng.lbl_acr_review_for} {$products[prod_num].product} ({$catalogs.customer}/add_review.php?productid={$products[prod_num].productid}&author={$fullname_url})
{/section}


{include file="mail/signature.tpl"}
