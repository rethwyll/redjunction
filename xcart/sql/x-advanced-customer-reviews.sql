CREATE TABLE `xcart_product_review_reminders` (
  `reminderid` int(11) NOT NULL AUTO_INCREMENT,
  `productid` int(11) NOT NULL DEFAULT '0',
  `email` varchar(128) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reminderid`),
  KEY `productid` (`productid`,`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `xcart_product_review_votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `remote_ip` varchar(15) NOT NULL DEFAULT '',
  `vote` int(1) NOT NULL DEFAULT '0',
  `review_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  KEY `remote_ip` (`remote_ip`,`review_id`),
  KEY `review_id` (`review_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE xcart_orders ADD review_reminder binary(1) NOT NULL default 'N' AFTER access_key;
ALTER TABLE xcart_products ADD review_rating int(2) NOT NULL default '0' AFTER taxcloud_tic;
ALTER TABLE xcart_product_reviews ADD userid int(11) NOT NULL default '0' AFTER productid;
ALTER TABLE xcart_product_reviews ADD author varchar(255) NOT NULL default '' AFTER userid;
ALTER TABLE xcart_product_reviews ADD datetime int(11) NOT NULL default '0' AFTER author;
ALTER TABLE xcart_product_reviews ADD rating int(2) NOT NULL default '0' AFTER datetime;
ALTER TABLE xcart_product_reviews ADD advantages text NOT NULL AFTER rating;
ALTER TABLE xcart_product_reviews ADD disadvantages text NOT NULL AFTER advantages;
ALTER TABLE xcart_product_reviews ADD total_amount_vote int(11) NOT NULL default '0' AFTER disadvantages;
ALTER TABLE xcart_product_reviews ADD useful_amount_vote int(11) NOT NULL default '0' AFTER total_amount_vote;
ALTER TABLE xcart_product_reviews ADD status binary(1) NOT NULL default 'A' AFTER useful_amount_vote;
ALTER TABLE xcart_product_reviews ADD is_verified binary(1) NOT NULL default 'N' AFTER status;
ALTER TABLE xcart_product_reviews ADD INDEX userid (userid);
ALTER TABLE xcart_product_reviews ADD INDEX rating (rating);
ALTER TABLE xcart_product_reviews ADD INDEX useful_amount_vote (useful_amount_vote);
ALTER TABLE xcart_product_reviews ADD INDEX datetime (datetime);
ALTER TABLE xcart_product_reviews ADD INDEX status (status);

INSERT INTO xcart_modules SET module_name='Advanced_Customer_Reviews', module_descr='Allows voting and writing reviews on products (advanced features)<br /><b>Note:</b> This module requires Customer Reviews module to be disabled.', active='Y', init_orderby=2000, author='qualiteam', module_url='', tags='userexp,marketing';

REPLACE INTO xcart_config SET name='acr_admin_reviews_per_page', comment='Reviews per page (admin area)', value='25', category='Advanced_Customer_Reviews', orderby='115', type='numeric', defvalue='50', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_allow_change_useful_box_vote', comment='Allow customers change their vote in the \"Was the above review useful to you?\" box', value='Y', category='Advanced_Customer_Reviews', orderby='113', type='checkbox', defvalue='Y', variants='', validation='', signature='';
REPLACE INTO xcart_config SET name='acr_allow_sort_by_rating', comment='Allow sort products by rating (customer area)', value='Y', category='Advanced_Customer_Reviews', orderby='44', type='checkbox', defvalue='Y', variants='', validation='', signature='';
REPLACE INTO xcart_config SET name='acr_customer_reviews_order', comment='Default reviews order (customer area)', value='useful', category='Advanced_Customer_Reviews', orderby='120', type='selector', defvalue='useful', variants='date:lbl_acr_newest\r\nuseful:lbl_acr_most_useful', validation='';
REPLACE INTO xcart_config SET name='acr_customer_reviews_per_page', comment='Reviews per page (customer area)', value='25', category='Advanced_Customer_Reviews', orderby='130', type='numeric', defvalue='25', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_customer_reviews_per_product', comment='Reviews per product details page (customer area)', value='5', category='Advanced_Customer_Reviews', orderby='125', type='numeric', defvalue='5', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_default_status', comment='Default status for new review', value='P', category='Advanced_Customer_Reviews', orderby='10', type='selector', defvalue='P', variants='P:lbl_acr_pending\r\nA:lbl_acr_approved\r\nR:lbl_acr_rejected', validation='';
REPLACE INTO xcart_config SET name='acr_display_reviews_menu', comment='Display customer reviews menu (customer area)', value='Y', category='Advanced_Customer_Reviews', orderby='205', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_display_useful_box', comment='Display \"Was the above review useful to you?\" box', value='Y', category='Advanced_Customer_Reviews', orderby='110', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_email_addresses_new_review', comment='Notification email addresses (use \',\' to separate multiple email addresses) ', value='', category='Advanced_Customer_Reviews', orderby='310', type='text', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_first_orderid_for_reminder', comment='Start send review reminder for orders with orderid greater than or equal to', value='0', category='Advanced_Customer_Reviews', orderby='430', type='text', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_max_stars', comment='Stars in product rating scale', value='5', category='Advanced_Customer_Reviews', orderby='15', type='selector', defvalue='5', variants='3:3\r\n4:4\r\n5:5\r\n6:6\r\n7:7\r\n8:8\r\n9:9\r\n10:10\r\n11:11\r\n12:12\r\n13:13\r\n14:14\r\n15:15\r\n16:16\r\n17:17\r\n18:18\r\n19:19\r\n20:20', validation='';
REPLACE INTO xcart_config SET name='acr_min_rating_for_reviews_menu', comment='Display reviews with N-stars and higher rating only', value='4', category='Advanced_Customer_Reviews', orderby='215', type='numeric', defvalue='4', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_reviews_menu_order', comment='Reviews order in customer menu', value='random', category='Advanced_Customer_Reviews', orderby='210', type='selector', defvalue='date', variants='date:lbl_acr_newest_reviews\r\nuseful:lbl_acr_most_useful_reviews\r\nrandom:lbl_acr_random_reviews', validation='';
REPLACE INTO xcart_config SET name='acr_review_reminder_key', comment='Secure key for launching review reminder script (send_review_reminders.php)', value='a2gHudkv81bpqrm61ft', category='Advanced_Customer_Reviews', orderby='440', type='text', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_send_email_about_new_review', comment='Receive an email notification when new review is posted', value='Y', category='Advanced_Customer_Reviews', orderby='305', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_send_reminder_in_n_days', comment='Send review reminder to customer in N days', value='21', category='Advanced_Customer_Reviews', orderby='420', type='numeric', defvalue='10', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_send_review_reminder', comment='Send review reminder to customer (for completed orders)', value='Y', category='Advanced_Customer_Reviews', orderby='410', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_use_advantages_block', comment='Show \"Advantages\" and \"Disadvantages\" blocks?', value='Y', category='Advanced_Customer_Reviews', orderby='105', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_use_old_ratings', comment='Use old ratings values for general product rating:', value='N', category='Advanced_Customer_Reviews', orderby='20', type='checkbox', defvalue='N', variants='', validation='';
REPLACE INTO xcart_config SET name='acr_writing_reviews', comment='Who must be allowed to add reviews', value='A', category='Advanced_Customer_Reviews', orderby='5', type='selector', defvalue='A', variants='A:lbl_acr_allow_to_all\r\nR:lbl_acr_allow_to_registered\r\nB:lbl_acr_allow_to_buyers', validation='';
REPLACE INTO xcart_config SET name='sep_acr1', comment='General Customer Reviews options', value='', category='Advanced_Customer_Reviews', orderby='1', type='separator', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='sep_acr2', comment='Appearance options', value='', category='Advanced_Customer_Reviews', orderby='100', type='separator', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='sep_acr3', comment='Customer Reviews Menu Box options', value='', category='Advanced_Customer_Reviews', orderby='200', type='separator', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='sep_acr4', comment='Email notifications options', value='', category='Advanced_Customer_Reviews', orderby='300', type='separator', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='sep_acr5', comment='Review Reminder options', value='', category='Advanced_Customer_Reviews', orderby='400', type='separator', defvalue='', variants='', validation='';

UPDATE xcart_config SET value = COALESCE((SELECT orderid FROM xcart_orders WHERE (TO_DAYS(NOW())-TO_DAYS(FROM_UNIXTIME(`date`))) > 120 ORDER BY `date` DESC LIMIT 1),0) WHERE name='acr_first_orderid_for_reminder';
UPDATE xcart_config SET value = (IF(value='', 0, value)) WHERE name='acr_first_orderid_for_reminder';
UPDATE xcart_modules SET active='N' WHERE module_name='Customer_Reviews';
UPDATE xcart_product_reviews SET `author`=`email` WHERE author='';
