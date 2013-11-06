{*
d4081a5b959ba8da777d033aa38cee71f337d907, v1 (xcart_4_5_3), 2012-07-27 05:59:50, acr_review_reminder_subj.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{config_load file="$skin_config"}{$lng.eml_acr_review_reminder_subj|substitute:"user":$userinfo.firstname:"company_name":$config.Company.company_name}
