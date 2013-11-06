{*
f53939a3ff8a37372a6aea8eb625d59a0b26f318, v2 (xcart_4_5_5), 2012-12-24 14:45:04, new_arrivals_search_by_date.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

<script type="text/javascript">
//<![CDATA[
searchform_def[searchform_def.length] = ['posted_data[date_period]', ''];

{literal}

function managedate(type, status) {
  var fields = ['f_start_date', 'f_end_date'];

  for (i in fields) {
    if (document.searchform.elements[fields[i]]) {
      if (status) {
        $(document.searchform.elements[fields[i]]).prop("disabled", true).addClass('ui-state-disabled' );
      } else {
        $(document.searchform.elements[fields[i]]).prop("disabled", false).removeClass('ui-state-disabled' );
      }
    }
  }
}

{/literal}
//]]>
</script>

<tr>
  <td height="10" width="20%" class="FormButton" nowrap="nowrap" valign="top">{$lng.lbl_new_arrivals_date_added}:</td>
  <td>

    <table cellpadding="2" cellspacing="2">

    <tr>
      <td width="5"><input type="radio" id="date_period_null" name="posted_data[date_period]" value=""{if $search_prefilled eq "" or $search_prefilled.date_period eq ""} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
      <td class="OptionLabel" colspan="2"><label for="date_period_null">{$lng.lbl_all_dates}</label></td>
    </tr>

    <tr>
      <td width="5"><input type="radio" id="date_period_M" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
      <td class="OptionLabel" colspan="2"><label for="date_period_M">{$lng.lbl_this_month}</label></td>
    </tr>

    <tr>
      <td width="5"><input type="radio" id="date_period_W" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
      <td class="OptionLabel" colspan="2"><label for="date_period_W">{$lng.lbl_this_week}</label></td>
    </tr>

    <tr>
      <td width="5"><input type="radio" id="date_period_D" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
      <td class="OptionLabel" colspan="2"><label for="date_period_D">{$lng.lbl_today}</label></td>
    </tr>

    <tr>
      <td width="5"><input type="radio" id="date_period_C" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:managedate('date',false)" /></td>
      <td class="OptionLabel" align="right"><label for="date_period_C">{$lng.lbl_from}:</label></td>
      <td>{include file="main/datepicker.tpl" name="start_date" date=$search_prefilled.start_date|default:$start_date}</td>
    </tr>

    <tr>
      <td width="5">&nbsp;</td>
      <td class="OptionLabel" align="right"><label>{$lng.lbl_to}:</label></td>
      <td>{include file="main/datepicker.tpl" name="end_date" date=$search_prefilled.end_date|default:$end_date}</td>
    </tr>

    </table>

  </td>
</tr>

<script type="text/javascript">
//<![CDATA[
$(document).ready( function(){ldelim}
  {if $search_prefilled.date_period eq "C"}
    managedate('date', false);
  {else}
    managedate('date', true);
  {/if}
{rdelim});
//]]>
</script>

