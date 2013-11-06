{*
553ad884cebe25b494a85fd5177abb3f8ddf289e, v1 (xcart_4_5_3), 2012-08-15 07:26:38, product_notifications_admin.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="page_title.tpl" title=$lng.lbl_prod_notif_adm}

{$lng.txt_prod_notif_adm_top_text}

<br /><br />

{capture name=dialog}

<!-- SEARCH FORM DIALOG START -->
<a name="search"></a>
{include file="modules/Product_Notifications/product_notifications_search_admin.tpl"}
<!-- SEARCH FORM DIALOG END -->

{if $notifications ne ""}
<!-- SEARCH RESULTS START -->
<a name="results"></a>
{if $total_items gt "0"}
  {$lng.txt_N_results_found|substitute:"items":$total_items}
  ( {$lng.txt_displaying_X_Y_results|substitute:"first_item":$first_item:"last_item":$last_item} )
{else}
  {$lng.txt_N_results_found|substitute:"items":0}
{/if}


<br /><br />

{if $total_pages gt 1}
  {assign var="pagestr" value="&page=`$navigation_page`"}
{/if}

{include file="main/navigation.tpl"}

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="processnotificationsform" prefix="selected"}

<form action="product_notifications.php" method="post" name="processnotificationsform">
  <input type="hidden" name="mode" value="delete" />
  <input type="hidden" name="page" value="{$page}" />

  <table cellpadding="2" cellspacing="1" width="100%">

    <tr class="TableHead">
      <td>&nbsp;</td>
      <td>{if $search_prefilled.sort eq "type"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="product_notifications.php?sort=type{$pagestr|amp}">{$lng.lbl_prod_notif_adm_type}</a></td> 
      <td>{if $search_prefilled.sort eq "product"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="product_notifications.php?sort=product{$pagestr|amp}">{$lng.lbl_prod_notif_adm_product}</a></td> 
      <td>{if $search_prefilled.sort eq "email"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="product_notifications.php?sort=email{$pagestr|amp}">{$lng.lbl_prod_notif_adm_email}</a></td> 
      <td>{if $search_prefilled.sort eq "requestor"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="product_notifications.php?sort=requestor{$pagestr|amp}">{$lng.lbl_prod_notif_adm_requestor}</a></td> 
      <td>{if $search_prefilled.sort eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="product_notifications.php?sort=date{$pagestr|amp}">{$lng.lbl_prod_notif_adm_date}</a></td> 
    </tr>

    {foreach from=$notifications item=notification name=notifications}
      <tr{interline name=notifications class=TableSubHead}>
        <td width="5">
          <input type="checkbox" name="selected[{$notification.id}]" />
        </td>
        <td>
          {assign var="notification_type_name" value="lbl_prod_notif_adm_type_name_`$notification.type`"}
          {$lng.$notification_type_name|escape}
        </td>
        <td>
          {$notification.product_title}
        </td>
        <td>
          {$notification.email|escape}
        </td>
        <td>
          {if $notification.requestor_link ne ''}
            <a href="{$notification.requestor_link}" target="_blank">
          {/if}
          {$notification.requestor|escape}
          {if $notification.requestor_link ne ''}
            </a>
          {/if}
        </td>
        <td>
          {$notification.date|date_format:$config.Appearance.datetime_format}
        </td>
      </tr>
    {/foreach}

    <tr>
      <td colspan="4" class="SubmitBox">
        <input type="button" value="{$lng.lbl_prod_notif_adm_delete|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^selected\\[.+\\]', 'gi'))) submitForm(this, 'delete');" />
      </td>
    </tr>
  </table>
</form>

<br />

{include file="main/navigation.tpl"}

<!-- SEARCH RESULTS END -->

{else}
  {$lng.txt_prod_notif_adm_no_results|escape}
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_prod_notif_adm_search_results content=$smarty.capture.dialog extra='width="100%"'}
