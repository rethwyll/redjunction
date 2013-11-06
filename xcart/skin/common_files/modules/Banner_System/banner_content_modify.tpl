{*
3734fe330427b35941425513146251bf996204df, v5 (xcart_4_6_1), 2013-08-26 12:53:51, banner_content_modify.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*} 

{include file="page_title.tpl" title=$lng.lbl_bs_banner_content}

<script type="text/javascript" src="{$SkinDir}/js/popup_image_selection.js"></script>

<div align="right">
  <a href="banner_system.php?type={$type}">{$lng.lbl_bs_back_to_banners_list}</a>
</div>

{$lng.txt_bs_banner_content_top_text}

<br /><br />

{if $banner_images}

{capture name=dialog}

<form action="banner_content.php" method="post" name="uploadform">
<input type="hidden" name="bannerid" value="{$bannerid}" />
<input type="hidden" name="mode" value="update_images" />
<input type="hidden" name="type" value="{$type}" />

<table cellspacing="1" cellpadding="3" class="data-table">

<tr class="TableHead">
  <td width="10">&nbsp;</td>
  <td width="5%">{$lng.lbl_pos}</td>
  <td width="40%">{$lng.lbl_image}</td>
  <td width="25%">{$lng.lbl_url}</td>
  <td width="20%">{$lng.lbl_alternative_text}</td>
  <td width="10%">{$lng.lbl_availability}</td>
</tr>

{section name=image loop=$banner_images}

<tr class="valign-top-row{cycle values=", TableSubHead"}">

	<td align="center">
    <input type="checkbox" value="Y" name="iids[{$banner_images[image].imageid}]" />
  </td>

  <td align="center">
    <input type="text" size="3" maxlength="5" name="image[{$banner_images[image].imageid}][orderby]" value="{$banner_images[image].orderby}" />
  </td>

	<td align="center">
    <a href="{$xcart_web_dir}/image.php?id={$banner_images[image].imageid}&amp;type=A" target="_blank"><img src="{$xcart_web_dir}/image.php?id={$banner_images[image].imageid}&amp;type=A" width="{if $banner_images[image].image_x gte '210'}210{else}{$banner_images[image].image_x}{/if}" alt="" /></a>
    <div>{$lng.lbl_image_properties}:</div>
{$banner_images[image].image_x}x{$banner_images[image].image_y},
{$banner_images[image].image_size}b
	</td>

  <td>
    <input type="text" name="image[{$banner_images[image].imageid}][url]" value="{$banner_images[image].url|escape}" style="width:99%;" />
  </td>

  <td>
    <input type="text" name="image[{$banner_images[image].imageid}][alt]" value="{$banner_images[image].alt|escape}" style="width:99%;" />
  </td>

  <td align="center">
    <select name="image[{$banner_images[image].imageid}][avail]">
      <option value="Y" {if $banner_images[image].avail eq "Y"}selected{/if}>{$lng.lbl_enabled}</option>
      <option value="N" {if $banner_images[image].avail eq "N"}selected{/if}>{$lng.lbl_disabled}</option>
    </select>
	</td>

</tr>
{/section}

<tr>
	<td colspan="5">
    <input type="button" value="{$lng.lbl_update|escape}" onclick="document.uploadform.mode.value='update_images';document.uploadform.submit();" />&nbsp;&nbsp;&nbsp;
    <input type="button" value="{$lng.lbl_delete_selected|escape}" onclick="javascript: document.uploadform.mode.value='delete_images'; document.uploadform.submit();" />
  </td>
</tr>

</table>
</form>
{/capture}

{include file="dialog.tpl" title=$lng.lbl_bs_banner_images content=$smarty.capture.dialog extra='width="100%"'}

{/if}

{if $html_banners}

<br /><br />

{capture name=dialog}

{include file="main/language_selector.tpl" script="banner_content.php?bannerid=`$bannerid`&amp;"}
<br /><br />

<form action="banner_content.php" method="post" name="uploadhtmlform">

<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="bannerid" value="{$bannerid}" />
<input type="hidden" name="mode" value="update_html_code" />

<table cellspacing="1" cellpadding="3" class="data-table">

  <tr class="TableHead">

    <td width="10">&nbsp;</td>

    <td width="5%">{$lng.lbl_pos}</td>

    <td width="85%">{$lng.lbl_code}</td>

    <td width="10%">{$lng.lbl_availability}</td>

  </tr>

  {section name=item loop=$html_banners}
  <tr class="valign-top-row{cycle name="html_cycle" values=", TableSubHead"}">

    <td align="center">
      <input type="checkbox" value="Y" name="ciids[{$html_banners[item].id}]" />
    </td>

    <td align="center">
      <input type="text" size="3" maxlength="5" name="code_data[{$html_banners[item].id}][order_by]" value="{$html_banners[item].order_by}" />
    </td>

    <td align="center">
      {assign var="thisid" value=$html_banners[item].id}
      <textarea id="html_code_{$thisid}" name="code_data[{$thisid}][html_code]" cols="70" rows="8" style="width: 99%;">{$html_banners[item].code|escape:"html"}</textarea>
      {if $active_modules.HTML_Editor}
        {include file="modules/HTML_Editor/popup_link.tpl" id="html_code_`$thisid`" width="99%"}
      {/if}
    </td>

    <td align="center">
      <select name="code_data[{$html_banners[item].id}][avail]" >
        <option value="Y" {if $html_banners[item].avail eq "Y"}selected{/if}>{$lng.lbl_enabled}</option>
        <option value="N" {if $html_banners[item].avail eq "N"}selected{/if}>{$lng.lbl_disabled}</option>
      </select>
    </td>

  </tr>
  {/section}

  <tr>
    <td colspan="5">
      <input type="button" value="{$lng.lbl_update|escape}" onclick="document.uploadhtmlform.mode.value='update_html_code';document.uploadhtmlform.submit();" />&nbsp;&nbsp;&nbsp;
      <input type="button" value="{$lng.lbl_delete_selected|escape}" onclick="javascript: document.uploadhtmlform.mode.value='delete_html_code'; document.uploadhtmlform.submit();" />
    </td>
  </tr>

</table>

</form>
{/capture}

{include file="dialog.tpl" title=$lng.lbl_bs_banner_html_code content=$smarty.capture.dialog extra='width="100%"'}

{/if}

<br /><br />

{capture name=dialog}
<form action="banner_content.php" method="post" name="mainuploadform">

<input type="hidden" name="bannerid" value="{$bannerid}" />
<input type="hidden" name="mode" value="upload_image" />
<input type="hidden" name="type" value="{$type}" />
<input type="hidden" name="banner_type" id="banner_type" value="image" />

<script type="text/javascript">
//<![CDATA[
  $(function() {ldelim}
    var tOpts = {ldelim}
      idPrefix: 'ui-tabs-', cookie: {ldelim} expires: 1 {rdelim}{if $selected}, selected: '{$selected}'{/if}
    {rdelim};
    $('#ui-tabs-container').tabs(tOpts);
  {rdelim});
//]]>
</script>

<div id="ui-tabs-container">

  <ul>
    <li>
      <a href="#upload_image">{$lng.lbl_bs_add_image}</a>
    </li>
    <li>
      <a href="#upload_html">{$lng.lbl_bs_add_html_code}</a>
    </li>
  </ul>

  <div id="upload_image"> 

    <table>

      <tr>
        <td>
          <table>
            <tr>
              <td><b>{$lng.lbl_preview}:</b></td>
              <td>&nbsp;</td>
              <td><a href="{$xcart_web_dir}/image.php?id={$bannerid}&amp;type=A&amp;tmp" target="_blank"><img id="edit_image_A" src="{$xcart_web_dir}/image.php?id=0&amp;type=A&amp;tmp" alt="" /></a></td>
            </tr>
            <tr>
              <td style="display: none;" id="edit_image_A_text"><br /><br />{$lng.txt_save_custom_image_note}</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table cellpadding="4" cellspacing="0">
            <tr>
              <td nowrap="nowrap">{$lng.lbl_select_file}:</td>
              <td>
                <input type="button" value="{$lng.lbl_browse_|escape}" onclick="popup_image_selection('A', '{$bannerid}', 'edit_image_A');" />
              </td>
            </tr>
            <tr>
              <td nowrap="nowrap">{$lng.lbl_url}:</td>
              <td>
                <input type="text" size="70" name="url" value="" />
              </td>
            </tr>
            <tr>
              <td nowrap="nowrap">{$lng.lbl_alternative_text}:</td>
              <td>
                <input type="text" size="70" name="alt" value="" />
              </td>
            </tr>
          </table>
        </td>
      </tr>

    </table>

  </div>

  <div id="upload_html">
    {include file="main/textarea.tpl" name="html_banner" cols="65" rows="15"}
  </div>

</div>

<script type="text/javascript">
//<![CDATA[
{literal}
  $(document).ready(function() {
    var selected = $( "#ui-tabs-container" ).tabs( "option", "selected" );
    if (selected == 0) {
      $("input[name=banner_type]").val("image");
    } else {
      $("input[name=banner_type]").val("html");
    }

    $( "#ui-tabs-container" ).tabs({
      select: function(e, ui) {
        var selected = $( "#ui-tabs-container" ).tabs( "option", "selected" );
        if (selected == 1) {
          $("input[name=banner_type]").val("image");
        } else {
          $("input[name=banner_type]").val("html");
        }
        return true;
      }
    });
  });
{/literal}
//]]>
</script>

<br />

<input type="submit" value="{$lng.lbl_bs_add_content|escape}" />

</form>
{/capture}

{include file='dialog.tpl' title=$lng.lbl_bs_add_banner_content content=$smarty.capture.dialog extra='width="100%"' zero_cellspacing=true}
