CREATE TABLE xcart_banners (
  bannerid int(11) NOT NULL auto_increment,
  location char(1) NOT NULL default 'T',
  width int(11) NOT NULL default '0',
  height int(11) NOT NULL default '0',
  order_by int(11) NOT NULL default '0',
  start_date int(11) NOT NULL default '0',
  end_date int(11) NOT NULL default '0',
  effect varchar(255) NOT NULL default '',
  home_page char(1) NOT NULL default '',
  pages char(1) NOT NULL default '',
  unlimited char(1) NOT NULL default 'N',
  nav char(1) NOT NULL default 'N',
  PRIMARY KEY (bannerid),
  KEY location (location)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE xcart_banners_categories (
  categoryid int(11) NOT NULL default '0',
  bannerid int(11) NOT NULL default '0',
  PRIMARY KEY (categoryid,bannerid),KEY productid (bannerid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE xcart_banners_html (
  id int(11) NOT NULL auto_increment,
  bannerid int(11) NOT NULL default '0',
  code text NOT NULL,
  avail char(1) NOT NULL default 'Y',
  order_by int(11) NOT NULL default '0',
  PRIMARY KEY (id),
  KEY ib (id,bannerid),
  KEY avail (avail),
  KEY order_by (order_by),
  KEY ia (id,avail),
  KEY ba (bannerid,avail)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE xcart_banners_html_lng (
  lng char(2) NOT NULL default '',
  id int(11) NOT NULL default '0',
  bannerid int(11) NOT NULL default '0',
  code text NOT NULL,
  PRIMARY KEY (lng,id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE xcart_images_A (
  imageid int(11) NOT NULL auto_increment,
  id int(11) NOT NULL default '0',
  image mediumblob NOT NULL,
  image_path varchar(255) NOT NULL default '',
  image_type varchar(64) NOT NULL default 'image/jpeg',
  image_x int(11) NOT NULL default '0',
  image_y int(11) NOT NULL default '0',
  image_size int(11) NOT NULL default '0',
  filename varchar(255) NOT NULL default '',
  date int(11) NOT NULL default '0',
  alt varchar(255) NOT NULL default '',
  avail char(1) NOT NULL default 'Y',
  orderby int(11) NOT NULL default '0',
  md5 varchar(32) NOT NULL default '',
  url varchar(255) NOT NULL default '',
  PRIMARY KEY (imageid),
  KEY image_path (image_path),
  KEY id (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO xcart_config SET name='bs_rotation_time_delay', comment='The time delay for rotating banner, in seconds', value='4', category='Banner_System', orderby='3', type='numeric', defvalue='5', variants='', validation='';
INSERT INTO xcart_config SET name='default_banner_height', comment='Default height of the banner', value='200', category='Banner_System', orderby='51', type='numeric', defvalue='200', variants='', validation='';
INSERT INTO xcart_config SET name='default_banner_width', comment='Default width of the banner', value='400', category='Banner_System', orderby='50', type='numeric', defvalue='400', variants='', validation='';
INSERT INTO xcart_config SET name='unlimited_banners_time', comment='Unlimited time of banners life', value='N', category='Banner_System', orderby='52', type='checkbox', defvalue='N', variants='', validation='';

INSERT INTO xcart_modules SET module_name='Banner_System', module_descr='This module enables banners, which can be both images and html-code', active='Y', init_orderby=0, author='qualiteam', module_url='', tags='marketing';
