{*
2178a473e6db2465d21fe538044f4d24f3a45dca, v2 (xcart_4_5_3), 2012-09-24 07:58:13, menu_new_arrivals.tpl, aim
vim: set ts=2 sw=2 sts=2 et:
*}
{if $config.New_Arrivals.new_arrivals_menu eq "Y" and $is_new_arrivals_page neq true and $new_arrivals}

  {capture name=menu}
      <ul>
      {foreach from=$new_arrivals item=b name=new_arrival}
          <li>
            {$b.add_date|date_format:$config.Appearance.date_format}<br />
            <a href="product.php?productid={$b.productid}">{$b.product|amp}</a>
          </li>
      {/foreach}
      </ul>
  {/capture}
  
  {include file="customer/menu_dialog.tpl" title=$lng.lbl_new_arrivals content=$smarty.capture.menu additional_class="menu-new_arrivals"}

{/if}
