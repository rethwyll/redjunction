{*
43e4ca6d6ff3c483566e2bb37aadda6fa50dd0de, v2 (xcart_4_4_0_beta_2), 2010-06-08 10:17:45, subcategories_list.tpl, igoryan
vim: set ts=2 sw=2 sts=2 et:
*}
<table cellspacing="0" summary="{$lng.txt_list_of_subcategories|escape}">

  <tr>
    <td>
      <ul class="subcategories">

        {foreach from=$categories item=subcat name=subcategories}
          <li{interline name=subcategories}>
            <a href="home.php?cat={$subcat.categoryid}">{$subcat.category|escape}</a>
            {if $config.Appearance.count_products eq "Y"}
              {if $subcat.product_count}
                ({$subcat.product_count} {$lng.lbl_products})
              {elseif $subcat.subcategory_count}
                ({$lng.lbl_N_categories|substitute:count:$subcat.subcategory_count})
              {/if}
            {/if}
          </li>
        {/foreach}

      </ul>
    </td>
  </tr>

</table>
