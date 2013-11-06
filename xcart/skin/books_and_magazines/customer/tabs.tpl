{*
59b18741a7e0c882b9e5cd007ec33ae63ba56ab6, v1 (xcart_4_5_0), 2012-04-05 11:53:47, tabs.tpl, ferz
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var=speed_bar value=$speed_bar|@array_reverse}
{if $speed_bar}
  <div class="tabs2{if $all_languages_cnt gt 1} with_languages{/if}">
    <ul>

      {foreach from=$speed_bar item=sb name=tabs}
        <li{interline name=tabs}><a href="{$sb.link|amp}">{$sb.title}</a></li>
      {/foreach}

    </ul>
  </div>
{/if}
