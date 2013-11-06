{*
2f2decb2a53862fefe55d0348186e74189c305df, v2 (xcart_4_5_5), 2012-12-07 13:01:03, customer_add_review.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $is_allow_add_review}
<h1>{$lng.lbl_acr_add_review}</h1>

{$lng.txt_acr_add_review_about_product|substitute:"productid":$product.productid|substitute:"product":$product.product}<br /><br />
{if $stored_review.is_verified eq 'Y'}
{$lng.txt_acr_verified_review|substitute:"orders":$stored_review.orderids}<br /><br />
{/if}

{capture name=dialog}

<form method="post" action="{$xcart_web_dir}/add_review.php?productid={$productid}">

  <input type="hidden" name="mode" value="add_review" />
  <input type="hidden" name="productid" value="{$productid}" />
  <input type="hidden" name="antibot_text" value="{$antibot_text}" />

  <table cellspacing="1" cellpadding="3" class="acr-data-table data-table" summary="{$lng.lbl_add_your_review|escape}">

    <tr>
      <td class="data-name"><label for="rating">{$lng.lbl_acr_your_rating}</label></td>
      <td class="data-required">*</td>
      <td>
        {include file="modules/Advanced_Customer_Reviews/vote_bar.tpl" rating_value=$stored_review.rating}
        <input type="hidden" name="review[rating]" id="rating" value="{$stored_review.rating|default:0|escape}" size="40" />
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="author">{$lng.lbl_your_name}</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="text" name="review[author]" id="author" value="{if $stored_review.author}{$stored_review.author|escape}{/if}" size="40" />
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="email">{$lng.lbl_acr_your_email}</label></td>
      <td class="data-required">*</td>
      <td>
        <input type="text" class="input-email" name="review[email]" id="email" value="{$stored_review.email|escape}" size="40" />
        <div id="email_note" class="note-box" style="display: none;">{$lng.txt_acr_email_optional_descr}</div>
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="review_message">{$lng.lbl_acr_your_comment}</label></td>
      <td class="data-required">*</td>
      <td>
        <textarea cols="40" rows="4" name="review[message]" id="review_message">{$stored_review.message|escape}</textarea>
        <br />
        {$lng.lbl_acr_max_length|substitute:length:$smarty.const.ACR_TEXT_MAX_LENGTH}
      </td>
    </tr>

    {if $config.Advanced_Customer_Reviews.acr_use_advantages_block eq 'Y'}
    <tr>
      <td class="data-name"><label for="review_advantages">{$lng.lbl_acr_advantages}</label></td>
      <td>&nbsp;</td>
      <td>
        <textarea cols="40" rows="4" name="review[advantages]" id="review_advantages">{$stored_review.advantages|escape}</textarea>
        <br />
        {$lng.lbl_acr_max_length|substitute:length:$smarty.const.ACR_TEXT_MAX_LENGTH}
      </td>
    </tr>

    <tr>
      <td class="data-name"><label for="review_disadvantages">{$lng.lbl_acr_disadvantages}</label></td>
      <td>&nbsp;</td>
      <td>
        <textarea cols="40" rows="4" name="review[disadvantages]" id="review_disadvantages">{$stored_review.disadvantages}</textarea>
        <br />
        {$lng.lbl_acr_max_length|substitute:length:$smarty.const.ACR_TEXT_MAX_LENGTH}
      </td>
    </tr>
    {/if}

    {include file="customer/buttons/submit.tpl" type="input" assign="submit_button"}

    {if $active_modules.Image_Verification and $show_antibot.on_reviews eq 'Y'}
      {include file="modules/Image_Verification/spambot_arrest.tpl" mode="data-table" id=$antibot_sections.on_reviews button_code=$submit_button antibot_err=$stored_review.antibot_err}
    {else}
    <tr>
      <td align="center" colspan="3">
        {$submit_button}
      </td>
    </tr>
    {/if}

  </table>

</form>

{load_defer file="modules/Advanced_Customer_Reviews/func.js" type="js"}

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_acr_add_review content=$smarty.capture.dialog}

{else}

  <div class="review-rated">{$lng.txt_you_already_voted}</div>

{/if}
