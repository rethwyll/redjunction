{* 850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, comment_form.tpl, joy 
vim: set ts=2 sw=2 sts=2 et:
*}
<div align="left">{include file="main/visiblebox_link.tpl" mark="com" title=$lng.lbl_aom_leave_comment}</div>
<div style="display: none;" id="boxcom">
<table cellpadding="3" cellspacing="1">
<tr>
  <td colspan="2"><textarea name="history_comment" cols="70" style="width: 400px;" rows="4"></textarea></td>
</tr>
<tr>
  <td width="20" align="left"><input type="checkbox" id="hp" name="history_is_public" value="Y" /></td>
  <td><label for="hp">{$lng.lbl_aom_visible_to_customer}</label></td>
</tr>
</table>
</div>
