{*
92f1206cf48f45411b7d1db0a9e263848b1a618d, v2 (xcart_4_4_0), 2010-08-04 07:15:54, cc_viaklix2pf.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<h1>viaKlix 2.0</h1>
{$lng.txt_cc_configure_top_text}
<br /><br />
{$lng.txt_viaklix2_note|substitute:"http_location":$http_location}<br /><br />
<br /><br />
{capture name=dialog}
<form action="cc_processing.php?cc_processor={$smarty.get.cc_processor|escape:"url"}" method="post">

<table cellspacing="10">
<tr>
<td>{$lng.lbl_cc_viaklix2_id}:</td>
<td><input type="text" name="param01" size="24" value="{$module_data.param01|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_viaklix2_userid}:</td>
<td><input type="text" name="param07" size="24" value="{$module_data.param07|escape}" /></td>
</tr>
</tr>

<tr>
<td>{$lng.lbl_cc_viaklix2_userpin}:</td>
<td><input type="text" name="param02" size="24" value="{$module_data.param02|escape}" /></td>
</tr>
</tr>

<tr>
<td>{$lng.lbl_cc_testlive_mode}:</td>
<td>
<select name="testmode">
<option value="Y"{if $module_data.testmode eq "Y"} selected="selected"{/if}>{$lng.lbl_cc_testlive_test}</option>
<option value="N"{if $module_data.testmode eq "N"} selected="selected"{/if}>{$lng.lbl_cc_testlive_live}</option>
<option value="D"{if $module_data.testmode eq "D"} selected="selected"{/if}>{$lng.lbl_cc_testlive_demo}</option>
</select>
</td>
</tr>

<tr>
<td>{$lng.lbl_cc_order_prefix}:</td>
<td><input type="text" name="param04" size="36" value="{$module_data.param04|escape}" /></td>
</tr>

<tr>
<td>{$lng.lbl_cc_viaklix2_avs}:</td>
<td>
<select name="param06">
<option value="Y"{if $module_data.param06 eq "Y"} selected="selected"{/if}>Y</option>
<option value="N"{if $module_data.param06 eq "N"} selected="selected"{/if}>N</option>
</select>
</td>
</tr>

</table>
<br /><br />
<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_cc_settings content=$smarty.capture.dialog extra='width="100%"'}