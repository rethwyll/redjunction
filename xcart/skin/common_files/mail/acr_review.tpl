{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, acr_review.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/mail_header.tpl"}

{$lng.txt_acr_new_review}:
----------------------------
{$lng.lbl_acr_edit_review|default:'-'|mail_truncate}{$catalogs.admin}/review_modify.php?review_id={$review.review_id}

{$lng.lbl_acr_status|default:'-'|mail_truncate}{include file="modules/Advanced_Customer_Reviews/review_status.tpl" review_status=$review.status static="Y"}

{$lng.lbl_date|default:'-'|mail_truncate}{$review.datetime|date_format:$config.Appearance.datetime_format}

{$lng.lbl_product|default:'-'|mail_truncate}{$review.product} ({$catalogs.admin}/product_modify.php?productid={$review.productid}&section=acr_reviews)

{$lng.lbl_acr_author|default:'-'|mail_truncate}{$review.author}{if $review.userid gt 0} ({$catalogs.admin}/user_modify.php?usertype=C&user={$review.userid}){/if}

{if $review.email ne ''}{$lng.lbl_email|default:'-'|mail_truncate}{$review.email}{/if}

{$lng.lbl_acr_rating|default:'-'|mail_truncate}{$review.rating}

{$lng.lbl_acr_comment}:
{$review.message}

{if $config.Advanced_Customer_Reviews.acr_use_advantages_block eq 'Y'}{$lng.lbl_acr_advantages}:
{$review.advantages}

{$lng.lbl_acr_disadvantages}:
{$review.disadvantages}{/if}


{$lng.lbl_acr_edit_review|default:'-'|mail_truncate}{$catalogs.admin}/review_modify.php?review_id={$review.review_id}

{include file="mail/signature.tpl"}
