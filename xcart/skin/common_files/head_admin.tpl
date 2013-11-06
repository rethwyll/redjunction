{*
f579eaa7b2fb5bc67d1743d2d7673e8ee544bd15, v5 (xcart_4_6_0), 2013-02-19 14:18:01, head_admin.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $login ne ""}
{include file="quick_search.tpl"}
{/if}

<div id="head-admin">

  <div id="logo-gray">
    <a href="{$current_location}/"><img src="{$ImagesDir}/logo_gray.png" alt="" /></a>
  </div>

  {if $login}

    {getvar var='top_news' func='func_tpl_get_admin_top_news'}
    <div class="admin-top-news">
      {$top_news.description|default:$top_news.title}
    </div>

    {include file="authbox_top.tpl"}

  {/if}

  <div class="clearing"></div>

  {if $login and $menu}
    {include file="`$menu`/menu_box.tpl"}
  {/if}

</div>
