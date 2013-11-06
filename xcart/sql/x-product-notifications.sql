CREATE TABLE `xcart_product_notifications` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`type` binary(1) NOT NULL default 'B',
	`productid` int(11) NOT NULL default '0',
	`variantid` int(11) NOT NULL default '0',
	`email` varchar(128) NOT NULL default '',
	`userid` int(11) NOT NULL default '0',
	`date` int(11) NOT NULL default '0',
	`extra` text NOT NULL,
	`unsubscribe_key` varchar(128) NOT NULL default '',
	PRIMARY KEY (id),
	UNIQUE KEY tpve (type,productid,variantid,email)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO xcart_config SET name='prod_notif_adm_obj_per_page', comment='Product notifications per page (in the admin area)', value='10', category='Product_Notifications', orderby='3', type='numeric', defvalue='10', variants='', validation='';
INSERT INTO xcart_config SET name='prod_notif_enabled_B', comment='Enable \'Back in stock\' notifications', value='Y', category='Product_Notifications', orderby='101', type='checkbox', defvalue='Y', variants='', validation='';
INSERT INTO xcart_config SET name='prod_notif_enabled_L', comment='Enable \'Low stock\' notifications', value='Y', category='Product_Notifications', orderby='201', type='checkbox', defvalue='Y', variants='', validation='';
INSERT INTO xcart_config SET name='prod_notif_enabled_P', comment='Enable \'Price drop\' notifications', value='Y', category='Product_Notifications', orderby='301', type='checkbox', defvalue='Y', variants='', validation='';
INSERT INTO xcart_config SET name='prod_notif_L_amount', comment='Minimal product available amount for low-stock notifications', value='3', category='Product_Notifications', orderby='210', type='numeric', defvalue='3', variants='', validation='';
INSERT INTO xcart_config SET name='prod_notif_show_in_list_B', comment='Show notification request forms in the product list', value='N', category='Product_Notifications', orderby='103', type='checkbox', defvalue='N', variants='', validation='';
INSERT INTO xcart_config SET name='prod_notif_show_in_list_L', comment='Show notification request forms in the product list', value='N', category='Product_Notifications', orderby='203', type='checkbox', defvalue='N', variants='', validation='';
INSERT INTO xcart_config SET name='prod_notif_show_in_list_P', comment='Show notification request forms in the product list', value='N', category='Product_Notifications', orderby='303', type='checkbox', defvalue='N', variants='', validation='';
INSERT INTO xcart_config SET name='sep_prod_notif_B', comment='\'Back in stock\' notifications settings', value='', category='Product_Notifications', orderby='100', type='separator', defvalue='', variants='', validation='';
INSERT INTO xcart_config SET name='sep_prod_notif_general', comment='General product notifications settings', value='', category='Product_Notifications', orderby='0', type='separator', defvalue='', variants='', validation='';
INSERT INTO xcart_config SET name='sep_prod_notif_L', comment='\'Low stock\' notifications settings', value='', category='Product_Notifications', orderby='200', type='separator', defvalue='', variants='', validation='';
INSERT INTO xcart_config SET name='sep_prod_notif_P', comment='\'Price drop\' notifications settings', value='', category='Product_Notifications', orderby='300', type='separator', defvalue='', variants='', validation='';
INSERT INTO xcart_modules SET module_name='Product_Notifications', module_descr='This module allows customer to receive inventory-related and price-related notifications', active='Y', init_orderby=0, author='qualiteam', module_url='', tags='userexp';
