{*
ed2bc7a05147f77d2c58cad24476f0ed0bfb727e, v3 (xcart_4_5_0), 2012-04-04 05:55:15, tabs.tpl, ferz
vim: set ts=2 sw=2 sts=2 et:
*}
{if $speed_bar}
  <div class="tabs{if $all_languages_cnt gt 1} with_languages{/if}">
    <ul>

      {foreach from=$speed_bar item=sb name=tabs}
        <li{interline name=tabs}><a href="{$sb.link|amp}">{$sb.title|escape}</a></li>
      {/foreach}

    </ul>
  </div>
{/if}
