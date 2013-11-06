{*
9faacf0d475a27006f6705032e250c09d04cd25e, v1 (xcart_4_5_3), 2012-08-13 09:07:12, mailchimp_news.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $active_modules.News_Management}
  {insert name="gate" func="news_exist" assign="is_news_exist" lngcode=$shop_language}

  {if $is_news_exist}

    {insert name="gate" func="news_subscription_allowed" assign="is_subscription_allowed" lngcode=$shop_language}

    {capture name=menu}

      <div class="news">

        {if $news_message eq ""}

          {$lng.txt_no_news_available}

        {else}

          <strong>{$news_message.date|date_format:$config.Appearance.date_format}</strong><br />
          {$news_message.body}<br /><br />
          <a href="mailchimp_news.php" class="prev-news">{$lng.lbl_previous_news}</a>
           &nbsp;&nbsp;
        {/if}
            <br />
            <a href="mailchimp_news.php#subscribe" class="subscribe">{$lng.lbl_subscribe}</a>
      </div>

    {/capture}
    {include file="customer/menu_dialog.tpl" title=$lng.lbl_news content=$smarty.capture.menu additional_class="menu-news"}

  {/if}
{/if}
