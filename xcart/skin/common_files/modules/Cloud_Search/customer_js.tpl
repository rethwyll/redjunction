{* vim: set ts=4 sw=4 sts=4 et: *}
{capture name=cloud_search_js}

var Cloud_Search = {ldelim}
  apiKey: '{$config.cloud_search_api_key|escape:'javascript'}',
  price_template: '{$cloud_search_price_template|escape:'javascript'}',
  lang: {ldelim}
    'lbl_showing_results_for': '{$lng.lbl_cloud_search_showing_results_for|escape:'javascript'}',
    'lbl_see_details': '{$lng.lbl_see_details|escape:'javascript'}',
    'lbl_see_more_results_for': '{$lng.lbl_cloud_search_see_more_results_for|escape:'javascript'}',
    'lbl_suggestions': '{$lng.lbl_cloud_search_suggestions|escape:'javascript'}',
    'lbl_products': '{$lng.lbl_products|escape:'javascript'}',
    'lbl_categories': '{$lng.lbl_cloud_search_categories|escape:'javascript'}',
    'lbl_manufacturers': '{$lng.lbl_cloud_search_manufacturers|escape:'javascript'}',
    'lbl_pages': '{$lng.lbl_cloud_search_pages|escape:'javascript'}'
  {rdelim}
{rdelim};

{/capture}
{load_defer file="cloud_search_js" direct_info=$smarty.capture.cloud_search_js type="js"}
{load_defer file="modules/Cloud_Search/js/lib/jquery.hoverIntent.minified.js" type="js"}
{load_defer file="modules/Cloud_Search/js/lib/handlebars-1.0.0.beta.6.js" type="js"}
<script type="text/javascript" src="//cloudsearch.x-cart.com/static/cloud_search_xcart.js"></script>
