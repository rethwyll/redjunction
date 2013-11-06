{*
dc4bb6f48c3d8c00ce3678772871d65bf7d64c0c, v3 (xcart_4_5_1), 2012-06-08 11:05:44, info_panel.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $view_info_panel eq "Y"}
<div id="eucl_panel">
  <table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td id="eucl_panel_msg">{$lng.txt_eu_cookie_law_panel_msg}&nbsp;</td>
    <td id="eucl_panel_btn">
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_eucl_change_settings tips_title=$lng.lbl_eucl_change_settings href="javascript: return func_change_cookie_settings();" additional_button_class="light-button"}
      &nbsp;&nbsp;&nbsp;&nbsp;
      {include file="customer/buttons/button.tpl" button_title=$lng.lbl_close tips_title=$lng.lbl_close href="javascript: return func_down_eucl_panel();" additional_button_class="light-button"}
    </td>
    <td id="eucl_panel_countdown">&nbsp;</td>
  </tr>
  </table>
</div>
{/if}
