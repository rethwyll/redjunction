{*
23663ab722599bb67140d607ae2900b6d69f951d, v5 (xcart_4_5_3), 2012-09-13 13:13:01, banner_system_modify.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*} 

{if $type ne ''}

  <div align="right">
    <a href="banner_system.php">{$lng.lbl_bs_back_to_locations_list}</a>
  </div>

  {include file="page_title.tpl" title=$page_title}

  {capture name=dialog}

  {if $banners ne ''}

    <form action="banner_system.php" method="post" name="bannersform">

      <input type="hidden" name="mode" value="update" />
      <input type="hidden" name="type" value="{$type}" />

      <table cellpadding="3" cellspacing="1" class="data-table">

        <tr class="TableHead">
          <td>&nbsp;</td>
          <td width="5%">{$lng.lbl_pos}</td>
          <td width="5%">{$lng.lbl_bs_banner_location}</td>
          <td width="26%">{$lng.lbl_categories}</td>
          <td width="5%">{$lng.lbl_bs_banner_width}</td>
          <td width="5%">{$lng.lbl_bs_banner_height}</td>
          <td width="15%">{$lng.lbl_bs_banner_start_date}</td>
          <td width="15%">{$lng.lbl_bs_banner_end_date}</td>
          <td width="5%">{$lng.lbl_bs_banner_effect}</td>
          <td width="5%">{$lng.lbl_bs_banner_unlimited}</td>
          <td width="5%">{$lng.lbl_bs_banner_navigation}</td>
        </tr>

        {foreach from=$banners item=b}

        {assign var="bannerid" value=$b.bannerid}
        {cycle name="banner_rows" values=', TableSubHead' assign="banner_trcolor"}

        <tr class="valign-top-row{$banner_trcolor}">

          <td>
            <input type="checkbox" value="Y" name="ids[{$bannerid}]" />
          </td>

          <td>
            <input type="text" name="banner_data[{$bannerid}][orderby]" value="{$b.order_by}" size="2" />
          </td>

          <td>
            <select name="banner_data[{$bannerid}][location]">
              <option value='T'{if $b.location eq 'T'} selected="selected"{/if}>{$lng.lbl_top}</option>
              <option value='B'{if $b.location eq 'B'} selected="selected"{/if}>{$lng.lbl_bottom}</option>
              <option value='L'{if $b.location eq 'L'} selected="selected"{/if}>{$lng.lbl_bs_left_column}</option>
              <option value='R'{if $b.location eq 'R'} selected="selected"{/if}>{$lng.lbl_bs_right_column}</option>
            </select>
          </td>

          <td>
            {include file="modules/Banner_System/category_selector.tpl" id="categoryselect`$bannerid`" name="banner_data[`$bannerid`][categoryids][]" banner=$b categories=$banner_categories.$bannerid}
          </td>

          <td>
            <input type="text" name="banner_data[{$bannerid}][width]" value="{$b.width}" size="2" />
          </td>

          <td>
            <input type="text" name="banner_data[{$bannerid}][height]" value="{$b.height}" size="2" />
          </td>

          <td nowrap="nowrap"{if $b.status ne ''} style="background-color: {if $b.status eq 'future'}#aaf{elseif $b.status eq 'expired'}#faa{/if};"{/if}>
            {include file="main/datepicker.tpl" name="banner_data[`$bannerid`][start_date]" date=$b.start_date id="datepicker`$bannerid`_start" start_year='-1' end_year='+4'}
          </td>

          <td nowrap="nowrap"{if $b.status ne ''} style="background-color: {if $b.status eq 'future'}#aaf{elseif $b.status eq 'expired'}#faa{/if};"{/if}>
            {include file="main/datepicker.tpl" name="banner_data[`$bannerid`][end_date]" date=$b.end_date id="datepicker`$bannerid`_end" start_year='-1' end_year='+4'}
          </td>

          <td>
            {include file="modules/Banner_System/banner_effect_selector.tpl" name="banner_data[`$bannerid`][effect]" selected_effect=$b.effect}
          </td>

          <td align="center">
            <input type="checkbox" value="Y" {if $b.unlimited eq 'Y'} checked="checked"{/if} name="banner_data[{$bannerid}][unlimited]" />
          </td>

          <td align="center">
            <input type="checkbox" value="Y" {if $b.nav eq 'Y'} checked="checked"{/if} name="banner_data[{$bannerid}][nav]" />
          </td>

        </tr>

        <tr>
          <td colspan="11" style="padding: 5px 0px 8px 12px;" class="{$banner_trcolor}">
            <a href="banner_content.php?bannerid={$bannerid}&type={$type}"><strong>{$lng.lbl_bs_edit_banner_content}</strong></a>
          </td>
        </tr>
        {/foreach}

        {if $config.Banner_System.unlimited_banners_time ne 'Y'}
        <tr>
           <td colspan="11">
              <hr />
              {$lng.txt_bs_banner_colors_note}
              <br /><br />
          </td>
        </tr>
        {/if}

        <tr>
          <td colspan="11">
            <div class="main-button"><input type="submit" value="{$lng.lbl_update|escape}" /></div>
          </td>
        </tr>

        <tr>
          <td colspan="11">
            <input type="submit" value="{$lng.lbl_delete_selected|escape}" onclick="javascript: submitForm(this, 'delete_banner');"/>
          </td>
        </tr>

      </table>

    </form>

  {else}

    {$lng.txt_bs_no_banners_defined}

  {/if}

  {/capture}

  {include file="dialog.tpl" title=$lng.lbl_banners content=$smarty.capture.dialog extra='width="100%"'}

  <br />
  <br />

  {capture name=dialog}

  <form action="banner_system.php" method="post" name="banneruploadform">

  <input type="hidden" name="type" value="{$type}" />
  <input type="hidden" name="mode" value="add" />

  <table cellpadding="3" cellspacing="1" class="data-table">

    <tr class="TableHead">
      <td width="5%">{$lng.lbl_pos}</td>
      <td width="26%">{$lng.lbl_categories}</td>
      <td width="5%">{$lng.lbl_bs_banner_width}</td>
      <td width="5%">{$lng.lbl_bs_banner_height}</td>
      <td width="15%">{$lng.lbl_bs_banner_start_date}</td>
      <td width="15%">{$lng.lbl_bs_banner_end_date}</td>
      <td width="5%">{$lng.lbl_bs_banner_effect}</td>
      <td width="5%">{$lng.lbl_bs_banner_unlimited}</td>
      <td width="5%">{$lng.lbl_bs_banner_navigation}</td>
    </tr>

    <tr class="valign-top-row">

      <td>
        <input type="text" value="" name="banner_orderby" size="2" />
      </td>

      <td>
        <input type="hidden" value="{$type}" name="banner_location" />
        {include file="modules/Banner_System/category_selector.tpl" id="categoryselect" name="categoryids[]" categories=$allcategories}
      </td>

      <td>
        <input type="text" name="banner_width" value="" size="2" />
      </td>

      <td>
        <input type="text" name="banner_height" value="" size="2" />
      </td>

      <td nowrap="nowrap">
        {include file="main/datepicker.tpl" name="start_date" start_year='-1' end_year='+4'}
      </td>

      <td nowrap="nowrap">
        {include file="main/datepicker.tpl" name="end_date" start_year='-1' end_year='+4'}
      </td>

      <td>
        {include file="modules/Banner_System/banner_effect_selector.tpl" name="banner_effect"}
      </td>

      <td align="center">
        <input type="checkbox" value="Y" name="banner_unlimited" />
      </td>

      <td align="center">
        <input type="checkbox" value="Y" name="banner_nav" />
      </td>

    </tr>

    <tr>

      <td colspan="9">
        <input type="submit" value="{$lng.lbl_add|escape}" />
      </td>

    </tr>

  </table>

  </form>

  {/capture}

  {include file="dialog.tpl" title=$lng.lbl_add_banner content=$smarty.capture.dialog extra='width="100%"'}

{else}

  {include file="page_title.tpl" title=$lng.lbl_banner_system}

  {capture name=dialog}
    <ul>
      <li>
        <a href="banner_system.php?type=T">{$lng.lbl_bs_top_banners}</a>
      </li>
      <li>
        <a href="banner_system.php?type=B">{$lng.lbl_bs_bottom_banners}</a>
      </li>
      <li>
        <a href="banner_system.php?type=L">{$lng.lbl_bs_left_column_banners}</a>
      </li>
      <li>
        <a href="banner_system.php?type=R">{$lng.lbl_bs_right_column_banners}</a>
      </li>
    </ul>
  {/capture}

  {include file="dialog.tpl" title=$lng.lbl_bs_choose_banner_location content=$smarty.capture.dialog extra='width="100%"'}

{/if}
