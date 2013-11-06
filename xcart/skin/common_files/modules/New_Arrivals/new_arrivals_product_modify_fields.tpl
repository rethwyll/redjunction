{*
f53939a3ff8a37372a6aea8eb625d59a0b26f318, v2 (xcart_4_5_5), 2012-12-24 14:45:04, new_arrivals_product_modify_fields.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[mark_as_new]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_new_arrivals_new_product}:</td>
  <td class="ProductDetails">
    <table cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <input type="hidden" name="mark_as_new" value="N" />
          <input type="checkbox" name="mark_as_new" id="mark_as_new" value="Y"{if $product.mark_as_new eq "A" || $product.mark_as_new eq "S"} checked="checked"{/if} />
        </td>
        <td class="date-period-selector"{if $product.mark_as_new ne "A" && $product.mark_as_new ne "S"} style="padding: 0 0 0 12px; display: none"{else} style="padding: 0 0 0 12px"{/if}>
          {$lng.lbl_date_period}:
          <select name="show_as_new_date_period_selector" id="show_as_new_date_period_selector">
            <option value="A"{if $product.mark_as_new eq "A" || $product.mark_as_new eq ""} selected="selected"{/if}>{$lng.lbl_new_arrivals_always}</option>
            <option value="S"{if $product.mark_as_new eq "S"} selected="selected"{/if}>{$lng.lbl_new_arrivals_specific_dates}</option>
          </select>
        </td>
      </tr>
    </table>

    <div class="date-period-always"{if $product.mark_as_new ne "A"} style="display: none"{/if}>
      <p>{$lng.lbl_new_arrivals_date_period_note}</p>
    </div>

    <br />
    <table cellspacing="0" cellpadding="0" class="date-period-specific"{if $product.mark_as_new ne "S"} style="display: none"{/if}>
      <tr>
        <td align="right">{$lng.lbl_from}:&nbsp;</td>
        <td>{include file="main/datepicker.tpl" name="show_as_new_from" date=$product.show_as_new_from}</td>
      </tr>
      <tr>
        <td align="right">{$lng.lbl_to}:&nbsp;</td>
        <td>{include file="main/datepicker.tpl" name="show_as_new_to" date=$product.show_as_new_to}</td>
      </tr>
    </table>
  </td>
</tr>

<script type="text/javascript">
//<![CDATA[
{literal}
 
$(function () {
 
  function markAsNew() {
    if ($("#mark_as_new").is(':checked')) {
      $(".date-period-selector").show();
      showAsNewDatePeriodSelectorChange();
    } else {
      $(".date-period-selector").hide();
      $(".date-period-specific").hide();
      $(".date-period-always").hide();
    }
  }

  function showAsNewDatePeriodSelectorChange() {
    if ($("#show_as_new_date_period_selector").val() == 'S') {
      $(".date-period-specific").show();
      $(".date-period-always").hide();
    } else {
      $(".date-period-specific").hide();
      $(".date-period-always").show();
    }
  }
 
  $("#mark_as_new").click(function() {
    markAsNew();
  });

  $("#show_as_new_date_period_selector").change(function() {
    showAsNewDatePeriodSelectorChange();
  });
 
})
 
{/literal}
//]]>
</script>

