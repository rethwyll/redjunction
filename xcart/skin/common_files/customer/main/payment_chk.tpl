{*
7400f06c6f1a75de77f30593d7b3861e7a9c34a0, v2 (xcart_4_4_0_beta_2), 2010-07-01 08:18:49, payment_chk.tpl, igoryan
vim: set ts=2 sw=2 sts=2 et:
*}
{if $payment_cc_data.disable_ccinfo ne "Y"}

  {if $checkout_module neq 'One_Page_Checkout'}
    <table cellspacing="0" class="data-table" summary="{$lng.lbl_check_information|escape}">
  {/if}

    {if $payment_cc_data.c_template ne ""}
      {include file=$payment_cc_data.c_template}
    {else}
      {include file="customer/main/register_chinfo.tpl"}
    {/if}

  {if $checkout_module neq 'One_Page_Checkout'}
    </table>
  {/if}

{else}

  {$lng.disable_chinfo_msg}
  <br />

{/if}
