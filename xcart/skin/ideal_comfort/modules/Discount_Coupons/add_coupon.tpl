{*
f692fbcbac0fb74b8e6d23053dfca2f9a5120c1b, v4 (xcart_4_6_1), 2013-09-16 12:17:51, add_coupon.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}

{capture name=dialog}

{*  <p class="text-block">{$lng.txt_add_coupon_header}</p>

  {if $gcheckout_enabled}
    <p class="text-block">{$lng.txt_gcheckout_add_coupon_note}</p>
  {/if}*}

  <a name='check_coupon'></a>
  <form action="cart.php" name="couponform">
    <input type="hidden" name="mode" value="add_coupon" />

    <table cellspacing="0" summary="{$lng.lbl_redeem_discount_coupon|escape}">
      <tr>
        <td class="data-name">{$lng.lbl_have_coupon_code}</td>
        <td><input type="text" class="text default-value" size="32" name="coupon" value="{$lng.lbl_coupon_code}" /></td>
        <td>&nbsp;</td>
        <td>{include file="customer/buttons/submit.tpl" type="input"}</td>
      </tr>
    </table>

  </form>

{/capture}
{if $page eq 'place_order'}
  {include file="customer/dialog.tpl" title=$lng.lbl_redeem_discount_coupon content=$smarty.capture.dialog additional_class="cart" noborder=true}
{else}
  {include file="customer/dialog.tpl" title=$lng.lbl_redeem_discount_coupon content=$smarty.capture.dialog additional_class="simple-dialog" noborder=true}
{/if}
