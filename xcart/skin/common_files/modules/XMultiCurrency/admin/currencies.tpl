{*
854724ecab846c1500f9e68871ccac377aa8ad86, v2 (xcart_4_6_0), 2013-03-13 10:41:27, currencies.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{assign var="TplImages" value="`$SkinDir`/images"}

<script type="text/javascript">
//<![CDATA[

var tplImages = '{$TplImages}';
var skinDir = '{$SkinDir}';
var primaryCurrency = '{$primaryCurrency}';
var msgDeletePrimaryCurrency = '{$lng.mc_txt_primary_currency_delete_warning}';
var msgSwitchPrimaryCurrency = '{$lng.mc_txt_primary_currency_switch_warning}';

{literal}

function addCurrency() {
  var code = $('#mc_currency_selector').val();
  var currency = $("#mc_currency_selector option:selected").text();
  var style = "background: url('"+tplImages+"/delete_cross.gif') no-repeat scroll 0 4px transparent; cursor: pointer; display: inline-block; height: 18px; margin-right: 4px; width: 12px;";
  $('#mc_add_currencies_block').append('<div><div style="'+style+'" onclick="javascript: $(this).parent().remove();">&nbsp;</div><input type="hidden" name="added_currencies[]" value="'+code+'" />'+currency+'</div>');
};

function deleteCurrency(code) {
  if (code == primaryCurrency) {
    alert(msgDeletePrimaryCurrency);

  } else {
    var orig_row = $('#tablerow_'+code).html();
    var _tmp_row = '<td colspan="8" align="center"><div style="background: url('+tplImages+'/progress.gif) repeat-x scroll 0 4px transparent; cursor: pointer; display: inline-block; height: 18px; margin-right: 4px; width: 80%;"></div></td>';
    $('#tablerow_'+code).html(_tmp_row);
    $.ajax({
      type: 'GET',
      url: 'currencies.php',
      data: {action: 'delete_currency', delete_code: code},
      error: function(_data){
        $('#tablerow_'+code).html(orig_row);
      },
        success: function(_data){
        $('#tablerow_'+code).remove();
        $('#tablesubrow_'+code).remove();
        if (_data == 'last') {
          if ($('#rateslastupdatedbox')) {
            $('#rateslastupdatedbox').hide();
          }
          $('#updateratesbox').hide();
        }
        if ($('#mc_default_currency option[value="'+code+'"]').length > 0) {
          $('#mc_default_currency option[value="'+code+'"]').remove();
        }
      }
    });
  }
}

function switchCurrency(code, enabled) {
  if (code == primaryCurrency) {
    alert(msgSwitchPrimaryCurrency);

  } else {
    $.ajax({
      type: 'GET',
      url: 'currencies.php',
      data: {action: 'switch_currency', code: code, state: enabled},
      success: function(_data){
        if (enabled) {
          var newState = '1';
          var oldState = '0';
          var newBgColor = '#FFFFFF';
          var defCurrencyDisplayStatus = '';
        } else {
          var newState = '0';
          var oldState = '1';
          var newBgColor = '#FAFAFA';
          var defCurrencyDisplayStatus = 'none';
        }
        $('#tablerow_'+code+' .currency-state-'+oldState).css('display', 'none');
        $('#tablerow_'+code+' .currency-state-'+newState).css('display', 'inline-block');
        $('#tablerow_'+code).css('background-color', newBgColor);
        if ($('#mc_default_currency option[value="'+code+'"]').length > 0) {
          $('#mc_default_currency option[value="'+code+'"]').css('display', defCurrencyDisplayStatus);
          if ($('#mc_default_currency option[selected]').val() == code) {
            $('#mc_default_currency :first').attr('selected', 'selected');
          }
        }
      }
    });
  }
}

function toggleCountry(country_code) {
  $.ajax({
    type: 'GET',
    url: 'currencies.php',
    data: {action: 'toggle_country', code: country_code},
    success: function(_data){
      var currentState = $('#allowed-country-' + country_code).css('text-decoration');
      if ('none' == currentState) {
        $('#allowed-country-' + country_code).css('text-decoration', 'line-through');
        $('#allowed-country-' + country_code).css('color', '#999999');

      } else {
        $('#allowed-country-' + country_code).css('text-decoration', 'none');
        $('#allowed-country-' + country_code).css('color', '#333333');
      }
    }
  });
}

{/literal}

//]]>
</script>


{include file="page_title.tpl" title=$lng.mc_lbl_currencies_management}


<form action="currencies.php" method="post" id="currenciesform">

  <input type="hidden" name="action" value="update_currencies" id="currenciesformaction" />


  {if $currencies}

  {include file="main/subheader.tpl" title="Currencies list"}

  <table cellpadding="3" cellspacing="1" width="100%" id="currencieslisttable">

    <tr class="TableHead" style="height: 28px; font-size: 14px;">
      <td>{$lng.mc_lbl_pos}</td>
      <td>{$lng.mc_lbl_primary}</td>
      <td>{$lng.mc_lbl_currency}</td>
      <td>{$lng.mc_lbl_code}</td>
      <td>{$lng.mc_lbl_symbol}</td>
      <td>{$lng.mc_lbl_format}</td>
      <td>{$lng.mc_lbl_number_format}</td>
      <td>{$lng.mc_lbl_rate}</td>
      <td>{$lng.mc_lbl_updated}</td>
      <td>&nbsp;</td>
    </tr>

    {foreach item=currency from=$currencies}

    {assign var="symbol" value=$currency.symbol|default:$currency.code}
    {assign var="code" value=$currency.code}

    <tr id="tablerow_{$currency.code}" {if !$currency.enabled}style="background-color: #FAFAFA"{/if}>
      <td align="center"><input type="text" name="updated_currencies[{$currency.code}][pos]" value="{$currency.pos}" size="3" maxlength="10" /></td>
      <td align="center"><input type="radio" name="primary" value="{$currency.code}"{if $currency.code eq $primaryCurrency} checked="checked"{/if} /></td>
      <td align="center">{$currency.name}</td>
      <td align="center">{$currency.code} / {$currency.code_int}</td>
      <td align="center">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
          <tr>
            <td align="right" width="55%"><input type="text" name="updated_currencies[{$currency.code}][symbol]" value="{$currency.symbol|escape}" size="10" /></td>
            <td align="center">&nbsp;-&nbsp;</td>
            <td align="left" width="45%">{$symbol}</td>
          </tr>
        </table>
      </td>
      <td align="center">
        <select name="updated_currencies[{$currency.code}][format]" style="width: 100px;">
        {foreach key=cformat item=cformatName from=$currencyFormats}
          <option value="{$cformat}"{if $cformat eq $currency.format} selected="selected"{/if}>{$cformatName|replace:"$":$symbol}</option>
        {/foreach}
        </select>
      </td>
      <td align="center">
        <select name="updated_currencies[{$currency.code}][number_format]" style="width: 100px;">
        {foreach key=nformat item=nformatName from=$numberFormats}
          <option value="{$nformat}"{if $nformat eq $currency.number_format} selected="selected"{/if}>{$nformatName}</option>
        {/foreach}
        </select>
      </td>
      <td align="center">{if $currency.is_default}1{else}<input type="text" name="updated_currencies[{$currency.code}][rate]" value="{$currency.rate}" size="15" />{/if}</td>
      <td align="center">
        <div style="font-size: 9px;">
        {if $currency.is_default}-
        {else}
          {assign var="service" value=$updatedRates.$code.service}
          {$updatedRates.$code.date|date_format:$config.Appearance.datetime_format}<br />
          ({if $service eq "M"}offline{else}{$mcServices.$service}{/if}){/if}
        </div>
      </td>
    <td align="center">
      <div id="statuscurrencycontrol_{$currency.code}" class="currency-state-1" style="background: url('{$SkinDir}/modules/XMultiCurrency/images/enabled_icon.png') no-repeat scroll 0 4px transparent; cursor: pointer; display: inline-block; height: 18px; margin-right: 4px; width: 12px; {if !$currency.enabled}display: none;{/if}" onclick="javascript: switchCurrency('{$currency.code}',0);" title="{$lng.mc_lbl_enabled}">&nbsp;</div>
      <div id="statuscurrencycontrol_{$currency.code}" class="currency-state-0" style="background: url('{$SkinDir}/modules/XMultiCurrency/images/disabled_icon.png') no-repeat scroll 0 4px transparent; cursor: pointer; display: inline-block; height: 18px; margin-right: 4px; width: 12px; {if $currency.enabled}display: none;{/if}" onclick="javascript: switchCurrency('{$currency.code}',1);" title="{$lng.mc_lbl_disabled}">&nbsp;</div>
      <div id="deletecurrencycontrol_{$currency.code}" style="background: url('{$TplImages}/delete_cross.gif') no-repeat scroll 0 4px transparent; cursor: pointer; display: inline-block; height: 18px; margin-right: 4px; width: 12px;" onclick="javascript: deleteCurrency('{$currency.code}');" title="Delete">&nbsp;</div>
    </td>
    </tr>

    <tr>
      <td id="tablesubrow_{$currency.code}" colspan="9"><div style="width:100%; height: 1px; background-color: #eeeeee;"></div></td>
    </tr>

    {/foreach}

  </table>

  {if $config.mc_rates_last_updated_date}
  <div style="float: right;" id="rateslastupdatedbox">
    {$lng.mc_lbl_rates_last_updated} {$config.mc_rates_last_updated_date|date_format:$config.Appearance.datetime_format}
    {if $config.mc_rates_last_updated_metod eq "M"}
    ({$lng.mc_lbl_offline})
    {else}
    {assign var="update_method" value=$config.mc_online_service}
    ({$lng.mc_lbl_method} {$mcServices.$update_method}, {$config.mc_update_time} {$lng.mc_lbl_seconds})
    {/if}
  </div>
  {/if}

  <br /><br />
  <br /><br />

  {/if}

  <div style="display: inline-block; width: 47%;">

  {include file="main/subheader.tpl" title=$lng.mc_lbl_add_currency}

  {include file="main/select_currency.tpl" name="new_currency" current_currency=$primaryCurrency currencies=$allCurrencies id="mc_currency_selector"}

  <input type="button" value="{$lng.mc_lbl_add_currency}" onclick="javascript: $('#currenciesformaction').val('add_currency'); $('#currenciesform').submit();" />

  <div id="mc_add_currencies_block" style="padding-top: 20px;"></div>

  <br /><br />
  <br /><br />

  <div id="sticky_content">
    <div class="main-button">
      <input type="submit" class="big-main-button" value="{$lng.lbl_apply_changes|strip_tags:false|escape}" />
    </div>
  </div>

  </div>

  <div style="float: right; width: 47%;">

{if $currencies}

  <div id="defaultcurrencybox">
    {include file="main/subheader.tpl" title=$lng.mc_lbl_default_currency}
    <div style="padding-left: 20px;">
      {$lng.mc_lbl_select_default_currency}:
      <select id="mc_default_currency" name="mc_default_currency">
        <option value=''>{$lng.lbl_select_one}</option>
        {foreach item=currency from=$availCurrencies}
        <option value="{$currency.code}"{if $currency.code eq $config.mc_default_currency} selected="selected"{/if}>{$currency.name}</option>
        {/foreach}
      </select>

      {include file="main/tooltip_js.tpl" text=$lng.mc_lbl_default_currency_note type="img" id="mc_default_currency_note" width='500'}

    </div>

    <br /><br />

  </div>

  {if $mcServices}

  <div id="updateratesbox">
    {include file="main/subheader.tpl" title=$lng.mc_lbl_update_rates}
    <div style="padding-left: 20px;">
      <div>
      {$lng.mc_lbl_online_service}:
      <select name="mc_service">
      {foreach key=mcServiceCode item=mcServiceLabel from=$mcServices}
        <option value="{$mcServiceCode}"{if $mcServiceCode eq $config.mc_online_service} selected="selected"{/if}>{$mcServiceLabel|default:$mcServiceCode}</option>
      {/foreach}
      </select>

      <input type="button" value="{$lng.mc_lbl_update_rates_now}" onclick="javascript: $('#currenciesformaction').val('update_rates'); $('#currenciesform').submit();" />
      </div>

      <br /><br />

      <div>
        <input id="autoupdatecheckbox" type="checkbox" name="auto_update" value="1"{if $config.mc_autoupdate_enabled eq "Y"} checked="checked"{/if} onclick="javascript: if (this.checked) $('#updatetimebox').show(); else $('#updatetimebox').hide();" />
        <label for="autoupdatecheckbox">{$lng.mc_lbl_enable_autoupdate}</label>
      </div>

      <div style="padding-top: 10px;{if $config.mc_autoupdate_enabled ne "Y"} display: none;{/if}" id="updatetimebox">
        {$lng.mc_lbl_time_of_update}:
        <input type="text" name="update_time" value="{$config.mc_autoupdate_time|default:"13:00"}" size="10" />
        <br />
        <div style="font-size: 11px; color: #999999;">{$lng.mc_lbl_time_of_update_note|substitute:"T":$current_time}</div>
      </div>

    </div>

    <br /><br />

    {/if}

  </div>

  <div id="updateoptionsbox">
    {include file="main/subheader.tpl" title=$lng.mc_lbl_update_options}
    <div style="padding-left: 20px;">

      <div>
        {$lng.mc_txt_selector_note_admin}
      </div>

      <br />

      <div>
        <input id="allow_select_country" type="checkbox" name="allow_select_country" value="1"{if $config.mc_allow_select_country eq "Y"} checked="checked"{/if} onclick="javascript: if (this.checked) $('#allowed_select_country').show(); else $('#allowed_select_country').hide();" />
        <label for="allow_select_country">{$lng.mc_lbl_allow_select_country}</label>
      </div>

      <div id="allowed_select_country" style="padding-bottom: 25px;{if $config.mc_allow_select_country ne "Y"} display: none;{/if}">

        <div>
          <input id="use_custom_countries_list" type="checkbox" name="use_custom_countries_list" value="1"{if $config.mc_use_custom_countries_list eq "Y"} checked="checked"{/if} onclick="javascript: if (this.checked) $('#available_countries').show(); else $('#available_countries').hide();" />
          <label for="use_custom_countries_list">{$lng.mc_lbl_use_custom_countries}</label>
          {include file="main/tooltip_js.tpl" text=$lng.mc_lbl_use_custom_countries_note type="img" id="mid_note" width='500'}
        </div>

        <div id="available_countries" style="font-size: 11px; color: #999999; padding-top: 15px;{if $config.mc_use_custom_countries_list ne "Y"} display: none;{/if}">
          {list2matrix assign="countries_matrix" assign_width="cell_width" list=$availableCountries row_length=3}
          {foreach from=$countries_matrix item=row name=countries_matrix}
          <div style="width: 100%;">
            {foreach from=$row item=country name=countries}
              <a id="allowed-country-{$country.country_code}" style="width: 30%; display: inline-block; padding-left: 3px; padding-right: 5px; vertical-align: middle; font-size: 11px; {if $country.excluded}color: #999999; text-decoration: line-through;{else}color: #333333; text-decoration: none;{/if}" href="javascript: void();" onclick="javascript: toggleCountry('{$country.country_code}');">{$country.country}</a>
            {/foreach}
          </div>
          {/foreach}
        </div>

      </div>

    </div>

  </div>


  {/if}


  </div>
</form>

