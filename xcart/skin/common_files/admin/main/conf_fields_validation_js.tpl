{*
b466260761b3a36702ff4e44fe1e3a4e8c8144cd, v4 (xcart_4_5_3), 2012-09-18 06:11:33, conf_fields_validation_js.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<script type="text/javascript">
//<![CDATA[
var email_validation_regexp = /{$email_validation_regexp}/gi

var validationFields = [
{if $configuration}
{foreach from=$configuration item=conf_var}
{if $conf_var.validation}
{assign var="opt_comment" value="opt_`$conf_var.name`"}
  {ldelim}name: '{$conf_var.name}', validation: "{$conf_var.validation}", comment: "{$lng.$opt_comment|default:$conf_var.comment|wm_remove|escape:javascript}"{rdelim},
{/if}
{/foreach}
{/if}
  {ldelim}{rdelim}
];

var invalid_parameter_text = '{$lng.err_invalid_field_data|wm_remove|escape:javascript}';

{getvar var=_styles func=func_get_configuration_styles}
{if $_styles}
  $(document).ready(function () {ldelim}
    if (typeof _configureFieldsXC == 'function') 
      _configureFieldsXC('{$_styles|wm_remove|escape:javascript}');
  {rdelim});
{/if}

//]]>
</script>
<script type="text/javascript" src="{$SkinDir}/js/conf_fields_validation.js"></script>
