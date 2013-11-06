{*
1f6152e37b8537396ee173ec0e4d1364bfc40fb9, v3 (xcart_4_6_0), 2013-05-20 17:39:12, new_arrivals.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $new_arrivals && (($is_home_page eq "Y" && $config.New_Arrivals.new_arrivals_home eq "Y") || ($new_arrivals_main eq "Y" && $config.New_Arrivals.new_arrivals_main eq "Y" && ($cat eq 0 || $cat eq "" || ($cat gt 0 && $current_category.show_new_arrivals eq "Y"))) || $is_new_arrivals_page)}
  {capture name=dialog}

    {if $is_new_arrivals_page}
      {include file="customer/main/navigation.tpl" navigation_script=$new_arrivals_navigation_script}
    {/if}

    {if $config.New_Arrivals.view_new_arrivals eq "F"}
      {include file="customer/main/products.tpl" products=$new_arrivals new_arrivals_show_date="Y" is_new_arrivals_products="Y"}
    {else}
      <ul class="new_arrivals-item">
        {foreach from=$new_arrivals item=new_arrival}
          <li>
            <a href="product.php?productid={$new_arrival.productid}">{include file="product_thumbnail.tpl" productid=$new_arrival.productid product=$new_arrival.product tmbn_url=$new_arrival.tmbn_url}</a>
            <div class="details">
              <a class="product-title" href="product.php?productid={$new_arrival.productid}">{$new_arrival.product|amp}</a><br />({$new_arrival.add_date|date_format:$config.Appearance.date_format})<br />
              {$lng.lbl_our_price}: {currency value=$new_arrival.taxed_price}
            </div>
            <div class="clearing"></div>
          </li>
        {/foreach}
      </ul>
    {/if}

    {if $is_new_arrivals_page}
      {include file="customer/main/navigation.tpl" navigation_script=$new_arrivals_navigation_script}
    {/if}

  {/capture}
  {include file="customer/dialog.tpl" title=$lng.lbl_new_arrivals content=$smarty.capture.dialog}
{/if}
