{*
537a3272759a84a0176b1e4663e61c66f207a7b3, v1 (xcart_4_5_3), 2012-08-03 11:35:59, order_total.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}

  <tr>
    <td colspan="2" {if $is_nomail eq 'Y'}class="invoice-total-name"{else}style="padding-left: 5px; height: 25px; width: 100%; text-align: right;"{/if}>
      {$lng.mc_lbl_amount_billed|substitute:"X":$order.extra.mc_primary_currency_symbol}:
      <span {if $is_nomail eq 'Y'}class="invoice-total-value"{else}style="white-space: nowrap; padding-right: 5px; height: 25px; text-align: right;"{/if}>{alter_currency value=$order.total currency=$order.extra.mc_primary_currency no_brackets=1}</span>
    </td>
  </tr>

