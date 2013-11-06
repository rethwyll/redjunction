{*
2e4157ecd036b2f5e692a43ac7dd4ef2eb69af53, v4 (xcart_4_6_0), 2013-05-22 14:21:42, acr_review.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}

<br />{$lng.txt_acr_new_review}:
<table>
  <tr>
    <td colspan="4"><a href="{$catalogs.admin}/review_modify.php?review_id={$review.review_id}">{$lng.lbl_acr_edit_review}</a></td>
  </tr>

  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>

  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_date}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{$review.datetime|date_format:$config.Appearance.datetime_format}</td>
  </tr>

  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_acr_status}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{include file="modules/Advanced_Customer_Reviews/review_status.tpl" review_status=$review.status static="Y"}</td>
  </tr>

  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_product}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">
      <a href="{$catalogs.admin}/product_modify.php?productid={$review.productid}&amp;section=acr_reviews">{$review.product}</a>
    </td>
  </tr>


  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_acr_author}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{$review.author}</td>
  </tr>

  {if $review.userid gt 0}
  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_customer}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%"><a href="{$catalogs.admin}/user_modify.php?usertype=C&amp;user={$review.userid}">{$review.user}</a></td>
  </tr>
  {/if}

  {if $review.email ne ''}
  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_email}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{$review.email}</td>
  </tr>
  {/if}

  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_acr_rating}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{$review.rating}</td>
  </tr>

  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_acr_comment}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{$review.message|nl2br}</td>
  </tr>

  {if $config.Advanced_Customer_Reviews.acr_use_advantages_block eq 'Y'}
  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_acr_advantages}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{$review.advantages|nl2br}</td>
  </tr>

  <tr>
    <td width="25">&nbsp;&nbsp;&nbsp;</td>
    <td width="20%">{$lng.lbl_acr_disadvantages}:</td>
    <td width="10">&nbsp;</td>
    <td width="80%">{$review.disadvantages|nl2br}</td>
  </tr>
  {/if}

  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>

</table>

{include file="mail/html/signature.tpl"}
