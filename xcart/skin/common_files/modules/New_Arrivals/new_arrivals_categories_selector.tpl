{*
0005997d13d4b212af5127e1d760b5ce2f50312c, v2 (xcart_4_6_0), 2013-05-23 10:30:55, new_arrivals_categories_selector.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $is_list eq "Y"}
  {if $is_header eq "Y"}
    <td align="center">{$lng.lbl_new_arrivals_show_new_arrivals}</td>
  {else}
    <td align="center">
    <select name="posted_data[{$categoryid}][show_new_arrivals]">
      <option value="Y"{if $category.show_new_arrivals eq "Y" || $category.show_new_arrivals eq ""} selected="selected"{/if}>{$lng.lbl_yes}</option>
      <option value="N"{if $category.show_new_arrivals eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
    </select>
    </td>
  {/if}
{elseif $is_details eq "Y"}
  <tr>
    <td height="10" class="FormButton" nowrap="nowrap">{$lng.lbl_new_arrivals_show_new_arrivals}:</td>
    <td width="10" height="10"><font class="Star"></font></td>
    <td height="10">
      <select name="show_new_arrivals">
        <option value="Y" {if $category.show_new_arrivals eq "Y" || $category.show_new_arrivals eq ""} selected="selected"{/if}>{$lng.lbl_yes}</option>
        <option value="N" {if $category.show_new_arrivals eq "N"} selected="selected"{/if}>{$lng.lbl_no}</option>
      </select>
    </td>
  </tr>
{/if}
