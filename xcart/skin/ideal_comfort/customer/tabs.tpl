{*
2a30536f400a253c86be1a18eb00603b4ad45f18, v1 (xcart_4_5_1), 2012-06-22 11:52:57, tabs.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $speed_bar}
  <div class="tabs{if $all_languages_cnt gt 1} with_languages{/if}">
    <ul>

      {foreach from=$speed_bar item=sb name=tabs}
         {strip}
			<li{interline name=tabs}>
				<a href="{$sb.link|amp}">
					{$sb.title}
					<img src="{$ImagesDir}/spacer.gif" alt="" />
				</a>
				<div class="t-l"></div><div class="t-r"></div>
			</li>
		{/strip}
      {/foreach}

    </ul>
  </div>
{/if}
