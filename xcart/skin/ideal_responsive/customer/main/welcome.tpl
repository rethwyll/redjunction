{*
a11d55b2c22f3ed2548072e03cca2ab6454c2e76, v2 (xcart_4_6_0), 2013-04-08 13:50:24, welcome.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="welcome-table">

	<div class="welcome-cell">
	
    {include file="customer/main/home_page_banner.tpl"}
 		{$lng.txt_welcome}

    {if $active_modules.Bestsellers}
      {getvar var=bestsellers func=func_tpl_get_bestsellers}
    {/if}

		{if $active_modules.Bestsellers and $config.Bestsellers.bestsellers_menu ne "Y"}
		  {include file="modules/Bestsellers/bestsellers.tpl"}<br />
		{/if}
		{if $active_modules.Bestsellers && $bestsellers}
			{assign var=row_length value=2}
		{else}
			{assign var=row_length value=false}
		{/if}

        <script type="text/javascript">
          //<![CDATA[
          $(function() {ldelim}
            var days = ('function' != typeof window.func_is_allowed_cookie || func_is_allowed_cookie('welcome-tabs-')) ? 1 : -1;
            var myOpts = {ldelim}
              idPrefix: 'welcome-tabs-', cookie: {ldelim} expires: days, name: "welcome-tabs" {rdelim}  {rdelim};
            $('#welcome-tabs-container').tabs(myOpts);
          {rdelim});
          //]]>
        </script>	
        <div id="welcome-tabs-container">
          <ul>
            {if $active_modules.New_Arrivals and $new_arrivals and $config.New_Arrivals.new_arrivals_home eq 'Y'}
              <li><a href="#new-arrivals">{$lng.lbl_new_arrivals}</a></li>
            {/if}
            {if $active_modules.On_Sale and $on_sale_products and $config.On_Sale.on_sale_home eq 'Y'}
              <li><a href="#on-sale">{$lng.lbl_on_sale}</a></li>
            {/if}
            {if $f_products}
              <li><a href="#featured-products">{$lng.lbl_featured_products}</a></li>
            {/if}
          </ul>
          {if $active_modules.New_Arrivals and $new_arrivals and $config.New_Arrivals.new_arrivals_home eq 'Y'}
            <div id="new-arrivals">
              {include file="modules/New_Arrivals/new_arrivals.tpl" is_home_page="Y" noborder="true"}
            </div>
          {/if}
          {if $active_modules.On_Sale and $on_sale_products and $config.On_Sale.on_sale_home eq 'Y'}
            <div id="on-sale">
              {include file="modules/On_Sale/on_sale.tpl" is_home_page="Y" noborder="true"}
            </div>
          {/if}
          {if $f_products}
            <div id="featured-products">
              {include file="customer/main/featured.tpl" row_length=$row_length noborder="true"}
            </div>
          {/if}
          </div>
	</div>

	{if $active_modules.Bestsellers && $bestsellers}
	<div class="bestsellers-cell">
		{include file="modules/Bestsellers/menu_bestsellers.tpl"}
	</div>
	{/if}
</div>
<div class="clearing"></div>
