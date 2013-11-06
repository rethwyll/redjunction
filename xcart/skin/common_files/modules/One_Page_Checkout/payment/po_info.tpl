{*
850e5138e855497e58a9e99e00c2e8e04e3f7234, v1 (xcart_4_4_0_beta_2), 2010-05-21 08:31:50, po_info.tpl, joy
vim: set ts=2 sw=2 sts=2 et:
*}

{if $hide_header ne "Y"}
  <h3>{$lng.lbl_po_information}</h3>
{/if}

<ul>

  <li class="single-field">
    {capture name=regfield}
      <input type="text" size="32" id="PO_Number" name="PO_Number" />
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_po_number field="PO_Number"}
  </li>

  <li class="single-field">
    {capture name=regfield}
      <input type="text" size="32" id="Company_name" name="Company_name" />
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_company_name field="Company_name"}
  </li>

  <li class="single-field">
    {capture name=regfield}
      <input type="text" size="32" id="Name_of_purchaser" name="Name_of_purchaser" />
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_name_of_purchaser field="Name_of_purchaser"}
  </li>

  <li class="single-field">
    {capture name=regfield}
      <input type="text" size="32" id="Position" name="Position" />
    {/capture}
    {include file="modules/One_Page_Checkout/opc_form_field.tpl" content=$smarty.capture.regfield required="Y" name=$lng.lbl_position field="Position"}
  </li>

</ul>
