{*
8ec8b858917c921a726e2197071ce74d020e5ead, v1 (xcart_4_6_0), 2013-04-02 16:43:59, tabs.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $speed_bar}
  <div class="tabs{if $all_languages_cnt gt 1} with_languages{/if} monitor">
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
  <div class="tabs-mobile">

    <select name="mobileTabs" id="mobileTabs">
        <option value="">{$lng.lbl_quick_menu}</option>
        {foreach from=$speed_bar|@array_reverse item=sb name=tabs}
          {strip}
            <option value="{$sb.link|amp}">
	      {$sb.title}
	    </option>
          {/strip}
        {/foreach}
      </select>
    <script type="text/javascript">
      $(function (){ldelim}
          $("#mobileTabs").val('1');  
          $("#mobileTabs").change(function(e) {ldelim}
              window.location.href = $(this).val();
          {rdelim});
      {rdelim});
    </script>
  </div>
{/if}
