REPLACE INTO xcart_config SET name='is_hot_products_installed', comment='', value='Y', category='', orderby='0', type='checkbox', defvalue='', variants='', validation='';

INSERT INTO xcart_modules SET module_name='New_Arrivals', module_descr='This module enables recently added product list', active='Y', init_orderby=0, author='qualiteam', module_url='', tags='marketing';

REPLACE INTO xcart_config SET name='new_arrivals_home', comment='Show the new arrivals list on the Home page', value='Y', category='New_Arrivals', orderby='25', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='new_arrivals_main', comment='Show the new arrivals list in the main column', value='Y', category='New_Arrivals', orderby='40', type='checkbox', defvalue='N', variants='', validation='';
REPLACE INTO xcart_config SET name='new_arrivals_menu', comment='Show the new arrivals list in the menu column', value='Y', category='New_Arrivals', orderby='30', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='number_of_new_arrivals', comment='Number of products in the new arrivals list', value='3', category='New_Arrivals', orderby='10', type='numeric', defvalue='5', variants='', validation='';
REPLACE INTO xcart_config SET name='number_of_new_arrivals_page', comment='Number of products on the New Arrivals page', value='9', category='New_Arrivals', orderby='15', type='numeric', defvalue='10', variants='', validation='';
REPLACE INTO xcart_config SET name='show_products_for_last_n_days', comment='Show products added in the last N days', value='700', category='New_Arrivals', orderby='20', type='numeric', defvalue='7', variants='', validation='';
REPLACE INTO xcart_config SET name='show_products_including_subcat', comment='Show products on category page including sub-categories', value='Y', category='New_Arrivals', orderby='60', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='view_new_arrivals', comment='View of the new arrivals list', value='F', category='New_Arrivals', orderby='50', type='selector', defvalue='S', variants='S:lbl_new_arrivals_simple\r\nF:lbl_new_arrivals_full', validation='';
REPLACE INTO xcart_config SET name='new_arrivals_show_in_special', comment='Show link to New Arrivals in \"Special\" section', value='Y', category='New_Arrivals', orderby='45', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='show_date_col_on_product_list', comment='Show \"Date added\" column on the product list (admin area)', value='Y', category='New_Arrivals', orderby='70', type='checkbox', defvalue='N', variants='', validation='';
REPLACE INTO xcart_config SET name='show_date_row_in_customer_area', comment='Show \"Added\" row in the customer area', value='Y', category='New_Arrivals', orderby='80', type='checkbox', defvalue='N', variants='', validation='';
REPLACE INTO xcart_config SET name='show_manually_added_na_first', comment='Show manually added products first', value='Y', category='New_Arrivals', orderby='90', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='show_products_marked_new_n_days', comment='Show products marked as new for N days', value='7', category='New_Arrivals', orderby='23', type='numeric', defvalue='7', variants='', validation='';


ALTER TABLE xcart_products ADD on_sale binary(1) NOT NULL default 'N' AFTER title_tag;
ALTER TABLE xcart_products ADD mark_as_new enum('S','A','N') NOT NULL default 'N' AFTER on_sale;
ALTER TABLE xcart_products ADD show_as_new_from int(11) NOT NULL default '0' AFTER mark_as_new;
ALTER TABLE xcart_products ADD show_as_new_to int(11) NOT NULL default '0' AFTER show_as_new_from;
ALTER TABLE xcart_products ADD key sa (show_as_new_from, add_date);
ALTER TABLE xcart_products ADD key on_sale (on_sale);
ALTER TABLE xcart_categories ADD show_new_arrivals enum('Y','N') NOT NULL default 'Y' AFTER title_tag;

INSERT INTO xcart_modules SET module_name='On_Sale', module_descr='This module enables on sale products', active='Y', init_orderby=0, author='qualiteam', module_url='', tags='marketing';

REPLACE INTO xcart_config SET name='on_sale_home', comment='Show on sale products on the Home page', value='Y', category='On_Sale', orderby='30', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_show_first', comment='Show on sale products first in product list', value='Y', category='On_Sale', orderby='10', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_show_in_special', comment='Show link to on sale products in \"Special\" section', value='Y', category='On_Sale', orderby='20', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_bestsellers', comment='In the Bestsellers section', value='N', category='On_Sale', orderby='160', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_cart_page', comment='On the Cart page', value='N', category='On_Sale', orderby='150', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_home_page', comment='On the Home page', value='Y', category='On_Sale', orderby='110', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_pmap_page', comment='On the Products Map page', value='N', category='On_Sale', orderby='220', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_product_list', comment='On the product list', value='Y', category='On_Sale', orderby='120', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_product_page', comment='On the product page', value='Y', category='On_Sale', orderby='130', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_product_tabs', comment='In the tabs on the product page', value='N', category='On_Sale', orderby='180', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_recently_viewed', comment='In the Recently viewed section', value='N', category='On_Sale', orderby='170', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_sale_page', comment='On the \"On Sale\" page', value='Y', category='On_Sale', orderby='100', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_search_page', comment='On the \"Search results\" page', value='Y', category='On_Sale', orderby='140', type='checkbox', defvalue='Y', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_wishlist_carousel', comment='In the Wish list carousel', value='N', category='On_Sale', orderby='210', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_on_wishlist_page', comment='On the Wish list page', value='N', category='On_Sale', orderby='190', type='checkbox', defvalue='', variants='', validation='';
REPLACE INTO xcart_config SET name='on_sale_where_to_display_sep', comment='Where to display icon', value='', category='On_Sale', orderby='50', type='separator', defvalue='', variants='', validation=''; 

INSERT INTO xcart_modules SET module_name='Quick_Reorder', module_descr='This module allows customer to view previously ordered products in a simple list', active='Y', init_orderby=0, author='qualiteam', module_url='', tags='userexp';
