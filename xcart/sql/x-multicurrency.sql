



CREATE TABLE `xcart_mc_currencies` (
  `code` char(3) NOT NULL default '',
  `symbol` varchar(128) NOT NULL default '',
  `rate` decimal(12,4) NOT NULL default 1,
  `is_default` int(1) NOT NULL default 0,
  `format` varchar(16) NOT NULL default '',
  `number_format` varchar(3) NOT NULL default '',
  `enabled` int(1) NOT NULL default 1,
  `is_default_in_frontend` int(1) NOT NULL default 0,
  `pos` int(11) NOT NULL default 0,
  PRIMARY KEY  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `xcart_mc_currencies` (`code`, `is_default`, `format`, `number_format`) VALUE ('USD', 1, '$x', '2.');

REPLACE INTO `xcart_modules` (module_name, module_descr, active, init_orderby, author, module_url, tags) VALUES ('XMultiCurrency', 'This module adds multicurrency feature for your store.','N',0,'qualiteam','','userexp');

REPLACE INTO `xcart_config` VALUES ('mc_autoupdate_enabled','','N','',10,'checkbox','N','','','');
REPLACE INTO `xcart_config` VALUES ('mc_allow_select_country','','Y','',10,'checkbox','N','','','');
REPLACE INTO `xcart_config` VALUES ('mc_autoupdate_time','','13:30','',10,'text','','','','');
REPLACE INTO `xcart_config` VALUES ('mc_online_service','','gfin','',10,'text','','','','');
REPLACE INTO `xcart_config` VALUES ('mc_use_custom_countries_list','','N','',10,'checkbox','N','','','');
REPLACE INTO `xcart_config` VALUES ('mc_excluded_countries_list','','','',10,'text','','','','');

