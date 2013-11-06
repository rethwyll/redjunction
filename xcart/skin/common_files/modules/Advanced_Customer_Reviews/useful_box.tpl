{*
2e4157ecd036b2f5e692a43ac7dd4ef2eb69af53, v4 (xcart_4_6_0), 2013-05-22 14:21:42, useful_box.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

<div class="acr-useful-box">
  {capture name=label assign=label}{ldelim}{ldelim}label{rdelim}{rdelim}{/capture}
  {capture name=amount assign=amount}{ldelim}{ldelim}amount{rdelim}{rdelim}{/capture}
  {capture name=class assign=class}{ldelim}{ldelim}class{rdelim}{rdelim}{/capture}
  {capture name=vote assign=vote}{ldelim}{ldelim}vote{rdelim}{rdelim}{/capture}

  {if $review.is_own_review eq 'Y' || ($review.is_voted neq '' && $config.Advanced_Customer_Reviews.acr_allow_change_useful_box_vote neq 'Y')}
    {assign var=is_voted value="Y"}
    {assign var=class_no value=""}
    {assign var=class_yes value=""}
    {if $review.vote eq 1}
      {assign var=class_yes value="useful-voted"}
    {elseif $review.vote eq 0 and $review.vote neq ''}
      {assign var=class_no value="useful-voted"}
    {/if}
  {else}

    {assign var=is_voted value="N"}
    {assign var=class_yes value="useful-yes pseudo-link"}
    {assign var=class_no value="useful-no pseudo-link"}

    {if $config.Advanced_Customer_Reviews.acr_allow_change_useful_box_vote eq 'Y' && $review.is_voted neq ''}
      {if $review.vote eq 1}
        {assign var=class_yes value="useful-voted useful-yes"}
      {elseif $review.vote eq 0 and $review.vote neq ''}
        {assign var=class_no value="useful-voted useful-no"}
      {/if}
    {/if}
  {/if}

  {capture name=useful_block assign=useful_block}
  {if $is_voted ne "Y"}<a class="{$class}" href="{$xcart_web_dir}/get_block.php?block=acr_vote_for_review&amp;productid={$productid}&amp;review_id={$review.review_id}&amp;vote={$vote}">{$label}</a>{else}<span class="{$class}">{$label}</span>{/if} (<span>{$amount|default:0}</span>)
  {/capture}

  <img class="wait" src="{$ImagesDir}/loading.gif" alt="{$lng.lbl_loading}" />{$lng.lbl_acr_is_review_useful} {$useful_block|substitute:label:$lng.lbl_yes:amount:$review.useful_amount_vote:class:$class_yes:vote:1} / {$useful_block|substitute:label:$lng.lbl_no:amount:$review.not_useful_amount_vote:class:$class_no:vote:0}
</div>
