{*
553ad884cebe25b494a85fd5177abb3f8ddf289e, v1 (xcart_4_5_3), 2012-08-15 07:26:38, product_notification_subj.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{assign var=subj_lbl_name value="eml_prod_notif_subj_`$type`"}
{config_load file="$skin_config"}{$config.Company.company_name}: {$lng.$subj_lbl_name}
