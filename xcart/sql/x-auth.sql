CREATE TABLE xcart_xauth_user_ids (
	auth_id int(11) NOT NULL auto_increment PRIMARY KEY,
	id int(11) NOT NULL default 0,
	service varchar(32) NOT NULL default '',
	provider varchar(32) NOT NULL default '',
	identifier varchar(255) NOT NULL default '',
	signature char(40) NOT NULL DEFAULT '' COMMENT 'Used to validate auth_id,id,service,provider,identifier fields',
    UNIQUE KEY (id, service, provider, identifier)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

REPLACE INTO xcart_modules (module_name, module_descr, active, init_orderby, author, module_url, tags) VALUES ('XAuth', '', 'N',0,'qualiteam','','userexp');

REPLACE INTO xcart_config SET name='xauth_create_profile', comment='Create profile', value='Y', category='XAuth', orderby='20', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_auto_login', comment='Auto login', value='Y', category='XAuth', orderby='30', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_login_by_email', comment='Login by email', value='Y', category='XAuth', orderby='40', type='checkbox', defvalue='', variants='', validation='';

REPLACE INTO xcart_config SET name='xauth_sep1', comment='Social Login (RPX) options', value='', category='XAuth', orderby='100', type='separator', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_rpx_api_key', comment='API key', value='', category='XAuth', orderby='110', type='text', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_rpx_app_id', comment='Application ID', value='', category='XAuth', orderby='115', type='text', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_rpx_app_name', comment='Application name', value='', category='XAuth', orderby='120', type='text', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_rpx_services', comment='Enabled services', value='facebook;twitter;google;openid', category='XAuth', orderby='130', type='multiselector', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_enable_social_sharing', comment='Enable Social Sharing on Product page', value='N', category='XAuth', orderby='140', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_enable_ss_cart', comment='Enable Social Sharing on Cart page', value='N', category='XAuth', orderby='150', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_enable_ss_invoice', comment='Enable Social Sharing on Invoice page', value='N', category='XAuth', orderby='160', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='xauth_rpx_display_mode', comment='Display mode', value='h', category='XAuth', orderby='170', type='selector', defvalue='', variants='v:lbl_xauth_display_vertical\nh:lbl_xauth_display_horizontal', validation='';

