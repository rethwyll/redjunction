{*
22be276001bfbfd838c39afa2616c35096f23e17, v3 (xcart_4_5_3), 2012-10-03 06:14:00, product_notification_request_form.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var='text_lbl_name' value="txt_prod_notif_request_`$type`"}
<div class="prod-notif" id="prod_notif_{$productid}_{$type}" style="display: none;">

    <div class="prod-notif-text">
      {$lng.$text_lbl_name}
    </div>

    <div class="prod-notif-request-submit-line">
      <input type="text" size="32" maxlength="128" name="prod_notif_email" id="prod_notif_email_{$productid}_{$type}" class="prod-notif-email{if $prod_notif_prefilled_email eq ''} prod-notif-email-default-value{/if}" value="{if $config.General.use_cached_templates eq "Y"}{$lng.lbl_prod_notif_email_default}{else}{$prod_notif_prefilled_email|default:$lng.lbl_prod_notif_email_default|escape}{/if}" />
      <span id="prod_notif_submit_block_{$productid}_{$type}">
        <span id="prod_notif_submit_button_{$productid}_{$type}">
          {include file="customer/buttons/button.tpl" type="link" style="image" title=$lng.lbl_prod_notif_submit button_id="prod_notif_submit_button"}
        </span>
        <span id="prod_notif_submit_waiting_{$productid}_{$type}" style="display: none;">
          <img src="{$ImagesDir}/prod_notif_ajax_loader.gif" alt="{$lng.lbl_prod_notif_waiting_msg|escape}" />
        </span>  
      </span>
    </div>

    <div id="prod_notif_submit_message_{$productid}_{$type}" class="prod-notif-request-submit-message">
    </div>

</div>
<script type="text/javascript">
//<![CDATA[
ProductNotificationWidgets.push(ProductNotificationWidget({$productid}, {$variantid|default:0}, '{$type}'));
//]]>
</script>
