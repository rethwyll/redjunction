{* 0d0465915bddae9202730b9dcd479c6724c60147, v1 (xcart_4_5_5), 2013-01-11 13:17:36, head_includings.tpl, random *}
<script type="text/javascript">
  //<![CDATA[
  {if $__frame_not_allowed && not $smarty.get.open_in_layer}
    if (top != self)
      top.location = self.location;
  {/if}
    var number_format_dec = '{$number_format_dec}';
    var number_format_th = '{$number_format_th}';
    var number_format_point = '{$number_format_point}';
    var store_language = '{$store_language|escape:javascript}';
    var xcart_web_dir = "{$current_location|escape:javascript}";
    var images_dir = "{$ImagesDir|escape:javascript}";
  {if $AltImagesDir}
    var alt_images_dir = "{$AltImagesDir|escape:javascript}";
  {/if}
    var lbl_no_items_have_been_selected = '{$lng.lbl_no_items_have_been_selected|escape:javascript}';
    var current_area = '{$usertype}';
    var currency_format = "{$config.General.currency_format|replace:'$':$config.General.currency_symbol}";
    var lbl_product_minquantity_error = "{$lng.lbl_product_minquantity_error|escape:javascript}";
    var lbl_product_maxquantity_error = "{$lng.lbl_product_maxquantity_error|escape:javascript}";
    var lbl_product_quantity_type_error = "{$lng.lbl_product_quantity_type_error|escape:javascript}";
    var is_limit = {if $config.General.unlimited_products eq 'Y'}false{else}true{/if};
    var lbl_required_field_is_empty = "{$lng.lbl_required_field_is_empty|strip_tags|escape:javascript}";
    var lbl_field_required = "{$lng.lbl_field_required|strip_tags|escape:javascript}";
    var lbl_field_format_is_invalid = "{$lng.lbl_field_format_is_invalid|escape:javascript}";
    var txt_required_fields_not_completed = "{$lng.txt_required_fields_not_completed|escape:javascript}";
    var lbl_blockui_default_message = "{$lng.lbl_blockui_default_message|escape:javascript}";
    var lbl_error = '{$lng.lbl_error|escape:javascript}';
    var lbl_warning = '{$lng.lbl_warning|escape:javascript}';
    var lbl_ok = '{$lng.lbl_ok|escape:javascript}';
    var lbl_yes = '{$lng.lbl_yes|escape:javascript}';
    var lbl_no = '{$lng.lbl_no|escape:javascript}';
    var txt_minicart_total_note = '{$lng.txt_minicart_total_note|escape:javascript}';
    var txt_ajax_error_note = '{$lng.txt_ajax_error_note|escape:javascript}';
  {if $use_email_validation ne "N"}
    var txt_email_invalid = "{$lng.txt_email_invalid|escape:javascript}";
    var email_validation_regexp = new RegExp("{$email_validation_regexp|escape:javascript}", "gi");
  {/if}
    var is_admin_editor = {if $is_admin_editor}true{else}false{/if};
  {literal}
      var ajax = {'widgets' : { 'add2cart' : false}};
  {/literal}
  var unlimited_products = {if $config.General.unlimited_products eq 'Y'}true{else}false{/if};
  //]]>
</script>
