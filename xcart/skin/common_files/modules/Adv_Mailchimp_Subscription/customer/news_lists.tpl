{*
9faacf0d475a27006f6705032e250c09d04cd25e, v1 (xcart_4_5_3), 2012-08-13 09:07:12, news_lists.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

<h1>{$lng.lbl_news_subscribe_to_newslists}</h1>

{capture name=dialog}

<form action="mailchimp_news.php" method="post">
  <input type="hidden" name="mode" value="subscribe" />
  <input type="hidden" name="newsemail" value="{$newsemail|escape}" />

{foreach from=$mc_lists item=list key=k}
  <label class="news-item">
    <input type="checkbox" name="s_lists[]" value="{$list.mc_list_id}" checked="checked" />
    {$list.name}
  </label>
  <div class="news-item-descr">{$list.descr}</div>
{/foreach}

  <div class="button-row">
    {include file="customer/buttons/button.tpl" button_title=$lng.lbl_subscribe additional_button_class="main-button" type="input"}
  </div>

</form>

{/capture}
{include file="customer/dialog.tpl" title=$lng.lbl_news_subscribe_to_newslists content=$smarty.capture.dialog noborder=true}
