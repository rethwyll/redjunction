{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, admin_reviews_list.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.Advanced_Customer_Reviews ne ""}

{if $product.productid}
{$lng.txt_adm_reviews_top_text}

<br /><br />
{/if}

{capture name=reviews_list}

{if $total_pages gt 2}
{assign var="navpage" value=$navigation_page}
{else}
{assign var="navpage" value=1}
{/if}

{include file="main/navigation.tpl"}

{if $reviews}
  {include file="main/check_all_row.tpl" form="modify_reviews_form" prefix="rids"}
{/if}
<br />

<form action="{if $product.productid ne ""}product_modify.php?productid={$product.productid}&amp;section=acr_reviews{else}reviews.php{/if}" method="post" name="modify_reviews_form">
<input type="hidden" name="mode" value="acr_update_reviews" />
<input type="hidden" name="productid" value="{$product.productid}" />
<input type="hidden" name="geid" value="{$geid}" />
<input type="hidden" name="navpage" value="{$navpage}" />

{assign var="colspan" value=9}

{if $current_page eq "product_reviews"}
{assign var="colspan" value=7}
{assign var="url_to" value="product_modify.php?productid=`$product.productid`&amp;section=acr_reviews"}
{else}
{assign var="url_to" value="reviews.php?mode=search&amp;page=`$navpage`"}
{/if}

<table cellspacing="0" cellpadding="3" width="100%">

<tr class="TableHead">
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td width="15" class="DataTable">&nbsp;</td>
  {if $current_page neq 'product_reviews'}
  <td width="15%" class="DataTable">({include file="main/search_sort_by_admin.tpl" sort_field="productid" lng_label="#`$lng.lbl_productid`"}) {include file="main/search_sort_by_admin.tpl" sort_field="product" lng_label="`$lng.lbl_product`"}</td>
  {/if}
  <td width="10%" class="DataTable">{include file="main/search_sort_by_admin.tpl" sort_field="author" lng_label="`$lng.lbl_acr_author` (`$lng.lbl_email`)"}</td>
  <td width="10%" class="DataTable">{include file="main/search_sort_by_admin.tpl" sort_field="remote_ip" lng_label="`$lng.lbl_remote_IP`"}</td>
  <td width="60%"class="DataTable">{include file="main/search_sort_by_admin.tpl" sort_field="message" lng_label="`$lng.lbl_acr_comment`"}</td>
  <td width="10%" class="DataTable">{include file="main/search_sort_by_admin.tpl" sort_field="date" lng_label="`$lng.lbl_date`"}</td>
  <td width="5%" class="DataTable">{include file="main/search_sort_by_admin.tpl" sort_field="rating" lng_label="`$lng.lbl_rating`"}</td>
  <td width="5%" class="DataTable">{include file="main/search_sort_by_admin.tpl" sort_field="status" lng_label="`$lng.lbl_status`"}</td>
  <td width="5%" class="DataTable">&nbsp;</td>
</tr>

{if $reviews}

{foreach from=$reviews item=r}
<tr valign="top"{cycle values=', class="TableSubHead"'}>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[review][{$r.review_id}]" /></td>{/if}
  <td width="15" class="DataTable"><input type="checkbox" value="Y" name="rids[{$r.review_id}]" /></td>
  {if $current_page neq 'product_reviews'}
  <td class="DataTable" nowrap="nowrap"><a href="product_modify.php?productid={$r.productid}">(#{$r.productid}) {$r.product|truncate:30:"...":true}</a></td>
  {/if}
  <td class="DataTable">{if $r.userid gt 0 && ($usertype eq "A" || ($usertype eq "P" && $single_mode))}<a href="user_modify.php?user={$r.userid}&amp;usertype=C">{$r.author}</a>{else}{$r.author}{/if}{if $r.email ne ''} ({$r.email}){/if}</td>
  <td class="DataTable">{$r.remote_ip}</td>
  <td class="DataTable">{$r.message|nl2br|truncate:120:"...":true}</td>
  <td class="DataTable" nowrap="nowrap">{if $r.datetime gt 0}{$r.datetime|date_format:$config.Appearance.date_format}{else}{$lng.lbl_acr_no_date}{/if}</td>
  <td class="DataTable" align="center">
    {include file="modules/Advanced_Customer_Reviews/rating_selector.tpl" is_short="Y" field="posted[`$r.review_id`][rating]" review_rating=$r.rating}
  </td>
  <td class="DataTable" align="center">
    {include file="modules/Advanced_Customer_Reviews/review_status.tpl" is_short="Y" field="posted[`$r.review_id`][status]" review_status=$r.status}
  </td>
  <td class="DataTable" align="center">
    {if $current_page eq 'product_reviews'}
      {assign var=url value="product_modify.php?productid=`$product.productid`&section=acr_reviews&mode=review_modify&review_id=`$r.review_id``$redirect_geid`"}
    {else}
      {assign var=url value="review_modify.php?review_id=`$r.review_id`"}
    {/if}
    <a href="{$url|amp}">{$lng.lbl_edit}</a></td>
</tr>
{/foreach}

{else}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="{$colspan}" align="center">{$lng.txt_no_reviews}</td>
</tr>

{/if}

</table>

{include file="main/navigation.tpl"}

<br /><br />
{if $reviews}
  <input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
  &nbsp;&nbsp;&nbsp;
  <input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('rids', 'ig'))) {ldelim} document.modify_reviews_form.mode.value='delete_reviews'; document.modify_reviews_form.submit();{rdelim}" />
{/if}
{if $current_page eq "product_reviews"}
<br /><br />{include file="buttons/button.tpl" href="product_modify.php?productid=`$product.productid`&section=acr_reviews&mode=review_modify`$redirect_geid`" button_title=$lng.lbl_add_review}
{/if}

</form>
{/capture}

{if $nodialog}
  {$smarty.capture.reviews_list}
{else}
  {include file="dialog.tpl" content=$smarty.capture.reviews_list title=$lng.lbl_edit_reviews extra='width="100%"'}
{/if}
{/if}
