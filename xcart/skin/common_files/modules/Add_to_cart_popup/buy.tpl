{*
351bd21bf1e3d2b22f531c405834e9a7a722a809, v2 (xcart_4_6_1), 2013-07-17 15:24:25, buy.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.Appearance.buynow_button_enabled eq "Y"}
<script type="text/javascript">
//<![CDATA[
  products_data[{$p.productid}] = {ldelim}{rdelim};
//]]>
</script>
  {if $login ne ''}{assign var="is_logged_in" value=1}{/if}
  {include_cache file="customer/main/buy_now.tpl" product=$p is_matrix_view=$is_matrix_view login=$is_logged_in|default:'' smarty_get_cat=$smarty.get.cat smarty_get_page=$smarty.get.page smarty_get_quantity=$smarty.get.quantity}
{/if}
