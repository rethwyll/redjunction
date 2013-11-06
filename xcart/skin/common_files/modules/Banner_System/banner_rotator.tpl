{*
9d50ca9c180a5df59f30beab2ddec644e9b77769, v6 (xcart_4_5_5), 2012-12-18 11:10:43, banner_rotator.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*} 

{foreach from=$banners item=banner}

  {if $banner.content ne ''}

    <div style="overflow: hidden; position: relative;">

      <div id="slideshow{$banner.bannerid}" class="banner-system" style="height: {$banner.height|default:$config.Banner_System.default_banner_height|default:1}px; width: {$banner.width|default:$config.Banner_System.default_banner_width|default:1}px; {if $additional_style}{$additional_style}{/if}">
        {foreach from=$banner.content item=content}
          <div id="slideshow_content{$content.id}{if $content.type eq "I"}_I{/if}">
          {if $content.type eq "I"}
            {if $content.url ne ''}<a href="{$content.url|amp}">{/if}<img style="margin: 0px {if $content.image_x ne ''}{math equation="floor(x/2)-floor(y/2)" x=$banner.width|default:$config.Banner_System.default_banner_width|default:1 y=$content.image_x}px{/if};" src="{$content.image_path|amp}" alt="{$content.alt|escape}" width="{$content.image_x}" height="{$content.image_y}" />{if $content.url ne ''}</a>{/if}
          {else} 
            <div class="content" style="height: {$banner.height|default:$config.Banner_System.default_banner_height|default:1}px; width: {$banner.width|default:$config.Banner_System.default_banner_width|default:1}px;">{$content.info}</div>
          {/if}
          </div>
        {/foreach}
      </div>

      {if $banner.nav eq 'Y'}
        <div id="banner_system_navigation">
          <div id="slideshow_page_info{$banner.bannerid}" class="banner_system_navigation"></div>
          <div class="clearing"></div>
        </div>
      {/if}
    </div>

  {/if}

{/foreach}

<div id="banner-system-code-{$banner_location}">
  {foreach from=$banners item=banner}
    {if $banner.content ne ''}
      <pre>
        <code class="mix">
        <span class="jquery">$</span>(<span class="string">'#slideshow{$banner.bannerid}'</span>).cycle({ldelim} 
          <br />
          fx: <span class="string">'{if $banner.effect eq 'random'}all{else}{$banner.effect}{/if}'</span>
          {if $config.Banner_System.bs_rotation_time_delay ne ''}
            ,<br />
            timeout: <span class="numbers">{assign var='time_delay' value=$config.Banner_System.bs_rotation_time_delay*1000}{$time_delay}</span>
          {/if}
          {if $banner.nav eq 'Y'}
            ,<br />
            pager: <span class="pager">'#slideshow_page_info{$banner.bannerid}'</span>
          {/if}
          <br />
        {rdelim});
        </code>
      </pre>
    {/if}
  {/foreach}
</div>

<script type="text/javascript">
//<![CDATA[
  {literal}
   $(document).ready(function() {
     $('#banner-system-code-{/literal}{$banner_location}{literal} pre code').each(function() {
         eval($(this).text());
       });
     });
  {/literal}
//]]>
</script>
