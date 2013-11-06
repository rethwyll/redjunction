{*
3feabb2abeed6f3359a2afda653a80a201516b14, v2 (xcart_4_6_1), 2013-06-19 13:04:33, search_reviews.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_acr_reviews_management}

<script type="text/javascript">
//<![CDATA[
var txt_delete_products_warning = "{$lng.txt_acr_delete_reviews_warning|strip_tags|wm_remove|escape:javascript}";
//]]>
</script>

<br />

{if $mode ne "search" or $reviews eq ""}

<script type="text/javascript" src="{$SkinDir}/js/reset.js"></script>
<script type="text/javascript">
//<![CDATA[
var searchform_def = [
  ['posted_data[productid]', ''],
  ['posted_data[userid]', ''],
  ['posted_data[author]', ''],
  ['posted_data[remote_ip]', ''],
  ['posted_data[status][]', ''],
  ['posted_data[rating][]', ''],
  ['posted_data[substring]', '']
];
//]]>
</script>

{capture name=dialog}

<br />

<form name="searchform" action="reviews.php" method="post">
<input type="hidden" name="mode" value="search" />

<table cellpadding="1" cellspacing="5" width="100%">

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_search_for_pattern}:</td>
  <td height="10" width="80%">
    <input type="text" name="posted_data[substring]" size="30" style="width:70%" value="{$search_prefilled.substring|escape}" />
    <br />{$lng.txt_acr_search_substr}
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_author}:</td>
  <td height="10" width="80%">
    <input type="text" name="posted_data[author]" size="30" style="width:70%" value="{$search_prefilled.author|escape}" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_userid}:</td>
  <td height="10" width="80%">
    <input type="text" name="posted_data[userid]" size="30" style="width:15%" value="{$search_prefilled.userid|escape}" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_remote_IP}:</td>
  <td height="10" width="80%">
    <input type="text" name="posted_data[remote_ip]" size="30" style="width:15%" value="{$search_prefilled.remote_ip|escape}" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap">{$lng.lbl_productid}:</td>
  <td height="10" width="80%">
    <input type="text" name="posted_data[productid]" size="30" style="width:15%" value="{$search_prefilled.productid|escape}" />
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_acr_review_status}:</td>
  <td height="10" width="80%">
    {include file="modules/Advanced_Customer_Reviews/review_status.tpl" field="posted_data[status][]" data=$search_prefilled.status}
  </td>
</tr>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_acr_rating}:</td>
  <td height="10" width="80%">
    {include file="modules/Advanced_Customer_Reviews/rating_selector.tpl" field="posted_data[rating][]" data=$search_prefilled.rating}
  </td>
</tr>

</table>

<table cellpadding="1" cellspacing="5" width="100%">
  <tr>
    <td class="FormButton normal" width="20%">
      <a href="javascript:void(0);" onclick="javascript: reset_form('searchform', searchform_def);" class="underline">{$lng.lbl_reset_filter}</a>
    </td>
    <td class="main-button">
      <input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" />
    </td>
  </tr>
</table>

</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_acr_search_reviews content=$smarty.capture.dialog extra='width="100%"'}

<br />

<!-- SEARCH FORM DIALOG END -->

{/if}

<!-- SEARCH RESULTS SUMMARY -->

<a name="results"></a>

{if $mode eq "search"}
{if $total_items gt "1"}
{$lng.txt_N_results_found|substitute:"items":$total_items}<br />
{$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item}
{elseif $total_items eq "0"}
{$lng.txt_N_results_found|substitute:"items":0}
{/if}
{/if}

{if $mode eq "search" and $reviews ne ""}

<!-- SEARCH RESULTS START -->

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="reviews.php"}</div>

{assign var=nodialog value='Y'}

{include file="modules/Advanced_Customer_Reviews/admin_reviews_list.tpl" reviews=$reviews}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}

{/if}
