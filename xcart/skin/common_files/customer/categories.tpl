{*
df14bc9e9dec4a3332552facb2c31eaaf22d39a2, v5 (xcart_4_4_2), 2010-12-17 15:12:37, categories.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $categories_menu_list ne '' or $fancy_use_cache}
{capture name=menu}

{if $active_modules.Flyout_Menus}

  {include file="modules/Flyout_Menus/categories.tpl"}
  {assign var="additional_class" value="menu-fancy-categories-list"}

{else}

  <ul>
    {foreach from=$categories_menu_list item=c name=categories}
      <li{interline name=categories}><a href="home.php?cat={$c.categoryid}" title="{$c.category|escape}">{$c.category|amp}</a></li>
    {/foreach}
  </ul>

  {assign var="additional_class" value="menu-categories-list"}

{/if}

{/capture}
{include file="customer/menu_dialog.tpl" title=$lng.lbl_categories content=$smarty.capture.menu}
{/if}
