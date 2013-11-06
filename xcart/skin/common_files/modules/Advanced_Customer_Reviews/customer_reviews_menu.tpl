{*
2e4157ecd036b2f5e692a43ac7dd4ef2eb69af53, v5 (xcart_4_6_0), 2013-05-22 14:21:42, customer_reviews_menu.tpl, random 
vim: set ts=2 sw=2 sts=2 et:
*}
{if $reviews4menu}
  {capture name=menu}
  <div>
    {section name=i loop=$reviews4menu}
      {assign var="url" value="product.php?productid=`$reviews4menu[i].productid`"}
      <div class="acr-item">
        <div class="acr-image">
         <a href="{$url}">{include file="product_thumbnail.tpl" productid=$reviews4menu[i].productid image_x=$reviews4menu[i].image_x product=$reviews4menu[i].product tmbn_url=$reviews4menu[i].image_url}</a>
        </div>
        <div class="acr-product-title"><a href="{$url}">{$reviews4menu[i].product|amp}</a></div>

        <div class="clearing"></div>

        <div class="acr-rating">
        {include file="modules/Advanced_Customer_Reviews/vote_bar.tpl" rating_value=$reviews4menu[i].rating is_average="N" allow_add_rate="N"}
        </div>

        <div class="clearing"></div>

        <div class="acr-comment">
          {$reviews4menu[i].message|truncate:1000:"...":true}
        </div>

        <div class="acr-author">
        {$reviews4menu[i].author}
        </div>

        {if $reviews4menu[i].datetime gt 0}
        <div class="acr-date">
        {$reviews4menu[i].datetime|date_format:"%b %e, %Y"}
        </div>
        {/if}

        <div class="acr-line"></div>

        <div class="acr-link">
          <a href="reviews.php?productid={$reviews4menu[i].productid}">{$lng.lbl_acr_read_product_reviews}</a>
        </div>

      </div>
    {/section}
    <div class="acr-link">
      <a href="reviews.php" rel="nofollow">{$lng.lbl_acr_read_all_reviews}</a>
    </div>

  </div>
  {/capture}
  {include file="customer/menu_dialog.tpl" title=$lng.lbl_acr_menu_reviews content=$smarty.capture.menu additional_class="menu-reviews-section"}
{/if}
