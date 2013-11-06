{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, tabs.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}
{if $speed_bar}
  <div class="tabs">
    <ul>

      {assign var=speed_bar value=$speed_bar|@array_reverse}
      {foreach from=$speed_bar item=sb name=tabs}
        <li{interline name=tabs}><a href="{$sb.link|amp}">{$sb.title}</a></li>
      {/foreach}

    </ul>
  </div>
{/if}
