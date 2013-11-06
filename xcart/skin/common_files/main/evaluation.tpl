{*
f2c96ddb72c96605401a2025154fc219a84e9e75, v6 (xcart_4_6_1), 2013-08-19 12:16:49, evaluation.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $is_enabled_evaluation_popup}
<script type="text/javascript">
//<![CDATA[
  {if $shop_evaluation eq 'WRONG_DOMAIN'}
    var _popup_sets = {ldelim}width:700,height:420,closeOnEscape:true{rdelim};
  {else}
    var _popup_sets = {ldelim}width:700,height:530,closeOnEscape:true{rdelim};
  {/if}
{literal}
$(document).ready(function () {
  return popupOpen('popup_info.php?action=evaluationPopup', '', _popup_sets);
});
{/literal}
//]]>
</script>
{/if}
